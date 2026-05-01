<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $codigo_material = mysqli_real_escape_string($conexion, $_POST['codigo_material'] ?? '');
    $nombre_material = mysqli_real_escape_string($conexion, $_POST['nombre_material'] ?? '');
    $tipo_medida = mysqli_real_escape_string($conexion, $_POST['tipo_medida'] ?? '');
    $medida1 = mysqli_real_escape_string($conexion, $_POST['medida1'] ?? '');
    $nombre_medida1 = mysqli_real_escape_string($conexion, $_POST['nombre_medida1'] ?? '');
    $medida2 = mysqli_real_escape_string($conexion, $_POST['medida2'] ?? '');
    $nombre_medida2 = mysqli_real_escape_string($conexion, $_POST['nombre_medida2'] ?? '');
    $action = $_POST['action'] ?? '';
    
    // Obtener el ID si está presente (para edición)
    $id_material = isset($_POST['id_material']) ? intval($_POST['id_material']) : 0;
    $ruta_imagen = '';
    
    // Procesar la imagen si se ha subido
    if (isset($_FILES['imagenMaterial']) && $_FILES['imagenMaterial']['error'] == UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['imagenMaterial']['name'];
        $tipo_archivo = $_FILES['imagenMaterial']['type'];
        $tamano_archivo = $_FILES['imagenMaterial']['size'];
        $archivo_temporal = $_FILES['imagenMaterial']['tmp_name'];
        
        // Validar que sea una imagen
        $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif');
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        
        if (in_array($extension, $extensiones_permitidas) && 
            strpos($tipo_archivo, 'image/') === 0) {
            
            // Generar nombre único para evitar sobrescribir
            $nombre_unico = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $nombre_archivo);
            $ruta_destino = '../materiaPrima/' . $nombre_unico;
            
            if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
                $ruta_imagen = 'materiaPrima/' . $nombre_unico;
            } else {
                $_SESSION['mensaje'] = "Error al subir la imagen";
                header("Location: ../b1t.php?p=art_materiales.php");
                exit();
            }
        } else {
            $_SESSION['mensaje'] = "Formato de imagen no permitido. Use JPG, JPEG, PNG o GIF";
            header("Location: ../b1t.php?p=art_materiales.php");
            exit();
        }
    }
    
    // Verificar que los campos obligatorios estén presentes
    if (empty($codigo_material) || empty($nombre_material) || empty($tipo_medida)) {
        $_SESSION['mensaje'] = "Error: Los campos código, nombre y tipo son obligatorios";
        header("Location: ../b1t.php?p=art_materiales.php");
        exit();
    }

    if ($action === 'registrar') {
        $sql = "INSERT INTO materia_prima (codigo_material, nombre_material, tipo_medida, medida1, nombre_medida1, medida2, nombre_medida2, ruta_imagen) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssss", 
                $codigo_material, 
                $nombre_material, 
                $tipo_medida,
                $medida1,
                $nombre_medida1,
                $medida2,
                $nombre_medida2,
                $ruta_imagen
            );
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensaje'] = "Materia prima registrada exitosamente";
            } else {
                $_SESSION['mensaje'] = "Error al registrar la materia prima: " . mysqli_error($conexion);
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['mensaje'] = "Error en la preparación de la consulta: " . mysqli_error($conexion);
        }
        
    } elseif ($action === 'editar') {
        if ($id_material <= 0) {
            $_SESSION['mensaje'] = "Error: ID de material no válido para edición";
            header("Location: ../b1t.php?p=art_materiales.php");
            exit();
        }
        
        // Obtener la ruta de la imagen actual para eliminarla si es necesario
        $imagen_actual = '';
        $sql_select = "SELECT ruta_imagen FROM materia_prima WHERE id_material = ?";
        $stmt_select = mysqli_prepare($conexion, $sql_select);
        if ($stmt_select) {
            mysqli_stmt_bind_param($stmt_select, "i", $id_material);
            mysqli_stmt_execute($stmt_select);
            mysqli_stmt_bind_result($stmt_select, $imagen_actual);
            mysqli_stmt_fetch($stmt_select);
            mysqli_stmt_close($stmt_select);
        }
        
        // Si se subió una nueva imagen, eliminar la anterior
        if (!empty($ruta_imagen) && !empty($imagen_actual) && file_exists('../' . $imagen_actual)) {
            unlink('../' . $imagen_actual);
        }
        
        // Si no se subió nueva imagen, mantener la existente
        if (empty($ruta_imagen) && !empty($imagen_actual)) {
            $ruta_imagen = $imagen_actual;
        }
        
        // Si se dejó vacío el campo imagen (en caso de edición con checkbox para eliminar)
        if (isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1') {
            if (!empty($imagen_actual) && file_exists('../' . $imagen_actual)) {
                unlink('../' . $imagen_actual);
            }
            $ruta_imagen = '';
        }
        
        // Actualizar incluyendo el campo ruta_imagen
        $sql = "UPDATE materia_prima 
                SET codigo_material = ?, 
                    nombre_material = ?, 
                    tipo_medida = ?, 
                    medida1 = ?, 
                    nombre_medida1 = ?,
                    medida2 = ?, 
                    nombre_medida2 = ?,
                    ruta_imagen = ?,
                    fecha_modificacion = CURRENT_TIMESTAMP
                WHERE id_material = ?";
        
        $stmt = mysqli_prepare($conexion, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssssi", 
                $codigo_material, 
                $nombre_material, 
                $tipo_medida,
                $medida1,
                $nombre_medida1,
                $medida2,
                $nombre_medida2,
                $ruta_imagen,
                $id_material
            );
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION['mensaje'] = "Materia prima actualizada exitosamente";
                } else {
                    $_SESSION['mensaje'] = "No se realizaron cambios o el registro no existe";
                }
            } else {
                $_SESSION['mensaje'] = "Error al actualizar la materia prima: " . mysqli_error($conexion);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['mensaje'] = "Error en la preparación de la consulta: " . mysqli_error($conexion);
        }
    }
} else {
    $_SESSION['mensaje'] = "Método no permitido";
}
header("Location: ../b1t.php?p=art_materiales.php");
exit();
?>