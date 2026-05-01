<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medida_material = mysqli_real_escape_string($conexion, $_POST['medida_material']);
    $nombre_material = mysqli_real_escape_string($conexion, $_POST['nombre_material']);
    $contiene_material = mysqli_real_escape_string($conexion, $_POST['contiene_material']);
    $medida_contenido_material = mysqli_real_escape_string($conexion, $_POST['medida_contenido_material']);
    $action = $_POST['action'];
    
    // Obtener el ID si está presente (para edición)
    $id_material = isset($_POST['id_material']) ? intval($_POST['id_material']) : 0;
    if (!is_numeric($contiene_material)) {
        $_SESSION['mensaje'] = "Error: El campo 'Contiene' debe ser un valor numérico";
        header("Location: ../b1t.php?p=art_materiales.php");
        exit();
    }
    $contiene_material = floatval($contiene_material);
    
    // FUNCIÓN PARA VERIFICAR SI EL NOMBRE DEL MATERIAL YA EXISTE
    function nombreMaterialExiste($conexion, $nombre_material, $id_material = 0) {
        $sql = "SELECT id_material FROM materia_prima WHERE nombre_material = ? AND id_material != ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $nombre_material, $id_material);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $existe = mysqli_stmt_num_rows($stmt) > 0;
            mysqli_stmt_close($stmt);
            return $existe;
        }
        return false;
    }

    if ($action === 'registrar') {
        // Verificar si el nombre del material ya existe (para nuevo registro)
        if (nombreMaterialExiste($conexion, $nombre_material)) {
            $_SESSION['mensaje'] = "Error: Ya existe un material con el nombre '$nombre_material'";
            header("Location: ../b1t.php?p=art_materiales.php");
            exit();
        }
        $sql = "INSERT INTO materia_prima (medida_material, nombre_material, contiene_material, medida_contenido_material) 
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssds", 
                $medida_material, 
                $nombre_material, 
                $contiene_material, 
                $medida_contenido_material
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
        
        // VERIFICAR SI YA EXISTE OTRO MATERIAL CON EL MISMO NOMBRE (excluyendo el actual)
        if (nombreMaterialExiste($conexion, $nombre_material, $id_material)) {
            $_SESSION['mensaje'] = "Error: Ya existe OTRO material con el nombre '$nombre_material'. El nombre del material debe ser único.";
            header("Location: ../b1t.php?p=art_materiales.php");
            exit();
        }
        
        // ACTUALIZAR incluyendo el campo ultima_actualizacion explícitamente
        $sql = "UPDATE materia_prima 
                SET medida_material = ?, 
                    nombre_material = ?, 
                    contiene_material = ?, 
                    medida_contenido_material = ?,
                    ultima_actualizacion = CURRENT_TIMESTAMP
                WHERE id_material = ?";
        
        $stmt = mysqli_prepare($conexion, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssdsi", 
                $medida_material, 
                $nombre_material, 
                $contiene_material, 
                $medida_contenido_material,
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