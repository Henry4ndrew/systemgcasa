<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica qué acción se seleccionó: guardar o editar
    if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $directorioDestino = '../portadas/';
            $archivoTmp = $_FILES['imagen']['tmp_name'];
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION); 
            $nombreArchivoUnico = uniqid('img_', true) . '.' . $extension;
            $rutaImagen = $directorioDestino . $nombreArchivoUnico;
            if (move_uploaded_file($archivoTmp, $rutaImagen)) {
                $titulo = isset($_POST['title']) ? $_POST['title'] : NULL;
                $descripcion = isset($_POST['desc']) ? $_POST['desc'] : NULL;
                $query = "INSERT INTO portada (ruta_img, titulo, descripcion) VALUES (?, ?, ?)";
                if ($stmt = $conexion->prepare($query)) {
                    $stmt->bind_param("sss", $rutaImagen, $titulo, $descripcion);
                    if ($stmt->execute()) {
                        $_SESSION['mensaje'] = "Imagen y datos guardados correctamente.";
                    } else {
                        $_SESSION['mensaje'] = "Error al guardar los datos.";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['mensaje'] = "Error en la preparación de la consulta.";
                }
            } else {
                $_SESSION['mensaje'] = "Error al subir la imagen.";
            }
        } else {
            $_SESSION['mensaje'] = "No se ha seleccionado una imagen o ocurrió un error al subirla.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'editar') {
        $idPortada = isset($_POST['idPortada']) ? $_POST['idPortada'] : NULL;
        if ($idPortada) {
            // Obtener la imagen actual de la base de datos
            $query = "SELECT ruta_img FROM portada WHERE id = ?";
            if ($stmt = $conexion->prepare($query)) {
                $stmt->bind_param("i", $idPortada);
                $stmt->execute();
                $stmt->bind_result($rutaImagenActual);
                $stmt->fetch();
                $stmt->close();
    
                // Si se recibe una nueva imagen, eliminar la anterior
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    // Eliminar la imagen anterior
                    if (file_exists($rutaImagenActual)) {
                        unlink($rutaImagenActual);
                    }
    
                    // Subir la nueva imagen
                    $directorioDestino = '../portadas/';
                    $archivoTmp = $_FILES['imagen']['tmp_name'];
                    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION); 
                    $nombreArchivoUnico = uniqid('img_', true) . '.' . $extension;
                    $rutaImagenNueva = $directorioDestino . $nombreArchivoUnico;
    
                    if (move_uploaded_file($archivoTmp, $rutaImagenNueva)) {
                        // Actualizar imagen en la base de datos
                        $query = "UPDATE portada SET ruta_img = ?, titulo = ?, descripcion = ? WHERE id = ?";
                        if ($stmt = $conexion->prepare($query)) {
                            $titulo = isset($_POST['title']) ? $_POST['title'] : NULL;
                            $descripcion = isset($_POST['desc']) ? $_POST['desc'] : NULL;
                            $stmt->bind_param("sssi", $rutaImagenNueva, $titulo, $descripcion, $idPortada);
                            if ($stmt->execute()) {
                                $_SESSION['mensaje'] = "Imagen y datos actualizados correctamente.";
                            } else {
                                $_SESSION['mensaje'] = "Error al actualizar los datos.";
                            }
                            $stmt->close();
                        } else {
                            $_SESSION['mensaje'] = "Error en la preparación de la consulta de actualización.";
                        }
                    } else {
                        $_SESSION['mensaje'] = "Error al subir la nueva imagen.";
                    }
                } else {
                    // Si no se recibe nueva imagen, solo se actualizan los datos de título y descripción
                    $titulo = isset($_POST['title']) ? $_POST['title'] : NULL;
                    $descripcion = isset($_POST['desc']) ? $_POST['desc'] : NULL;
                    $query = "UPDATE portada SET titulo = ?, descripcion = ? WHERE id = ?";
                    if ($stmt = $conexion->prepare($query)) {
                        $stmt->bind_param("ssi", $titulo, $descripcion, $idPortada);
                        if ($stmt->execute()) {
                            $_SESSION['mensaje'] = "Datos actualizados correctamente.";
                        } else {
                            $_SESSION['mensaje'] = "Error al actualizar los datos.";
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['mensaje'] = "Error en la preparación de la consulta de actualización.";
                    }
                }
            }
        }
    }
    header("Location: ../b1t.php?p=web_portada.php");
    exit;
}

$conexion->close();

?>
