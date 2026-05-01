<?php
session_start();
require '../includes/conexion.php'; 

if(isset($_POST['id_material']) && isset($_POST['cantidad'])) {

    $ids_material = $_POST['id_material'];
    $cantidades = $_POST['cantidad'];
    
    $idVendedor3 = isset($_POST['idVendedor3']) ? intval($_POST['idVendedor3']) : 0;
    $notaMaterialAgregado = isset($_POST['notaMaterialAgregado']) ? trim($_POST['notaMaterialAgregado']) : '';

    if(count($ids_material) === count($cantidades)) {
        $conexion->begin_transaction();
        
        try {
            $accion = "Se agregó";
            $responsable = null;
            
            $stmt_historial = $conexion->prepare("INSERT INTO historial_materia_prima (accion, responsable, nota, id_user) VALUES (?, ?, ?, ?)");
            $stmt_historial->bind_param("sssi", $accion, $responsable, $notaMaterialAgregado, $idVendedor3);
            $stmt_historial->execute();
            
            $id_historial = $conexion->insert_id;
            $stmt_historial->close();
            
            // 2. Procesar cada material
            for($i = 0; $i < count($ids_material); $i++) {
                $id_material = intval($ids_material[$i]);
                $cantidad = floatval($cantidades[$i]);

                if($cantidad > 0) {
                    $stmt_detalle = $conexion->prepare("INSERT INTO historial_matPrim_materiales (id_historial, id_material, cantidad) VALUES (?, ?, ?)");
                    $stmt_detalle->bind_param("iid", $id_historial, $id_material, $cantidad);
                    $stmt_detalle->execute();
                    $stmt_detalle->close();
                    
                    $query = $conexion->prepare("SELECT cantidad FROM almacen_materiales WHERE id_material = ?");
                    $query->bind_param("i", $id_material);
                    $query->execute();
                    $query->store_result();
                    
                    if($query->num_rows > 0) {
                        $query->bind_result($cantidad_actual);
                        $query->fetch();
                        $nueva_cantidad = $cantidad_actual + $cantidad;

                        $update = $conexion->prepare("UPDATE almacen_materiales SET cantidad = ?, fecha_modificacion = NOW() WHERE id_material = ?");
                        $update->bind_param("di", $nueva_cantidad, $id_material);
                        $update->execute();
                        $update->close();
                    } else {
                        $insert = $conexion->prepare("INSERT INTO almacen_materiales (id_material, cantidad, fecha_modificacion) VALUES (?, ?, NOW())");
                        $insert->bind_param("di", $id_material, $cantidad);
                        $insert->execute();
                        $insert->close();
                    }
                    
                    $query->close();
                }
            }
            $conexion->commit();
            
            $_SESSION['mensaje'] = "Materiales agregados correctamente. Historial registrado.";
            header("Location: ../b1t.php?p=stock_materiales.php");
            exit();

        } catch (Exception $e) {
            $conexion->rollback();
            
            $_SESSION['mensaje'] = "Error al procesar la operación: " . $e->getMessage();
            header("Location: ../b1t.php?p=stock_materiales.php");
            exit();
        }

    } else {
        $_SESSION['mensaje'] = "Error: Los datos no coinciden.";
        header("Location: ../b1t.php?p=stock_materiales.php");
        exit();
    }

} else {
    $_SESSION['mensaje'] = "No se recibieron datos.";
    header("Location: ../b1t.php?p=stock_materiales.php");
    exit();
}
?>