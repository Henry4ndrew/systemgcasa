<?php
session_start();
require '../includes/conexion.php';

// Datos para el historial
$idVendedor4 = isset($_POST['idVendedor2']) ? intval($_POST['idVendedor2']) : 0;
$accion = "Se retiró";
$responsable = isset($_POST['responsableRetiro']) ? trim($_POST['responsableRetiro']) : '';
$nota = isset($_POST['notaMaterialEliminado']) ? trim($_POST['notaMaterialEliminado']) : '';

if(isset($_POST['id_material']) && isset($_POST['cantidad'])) {

    $ids_material = $_POST['id_material'];
    $cantidades = $_POST['cantidad'];
    
    // Validar que coincidan los arrays
    if(count($ids_material) === count($cantidades)) {
        // Iniciar transacción para asegurar consistencia de datos
        $conexion->begin_transaction();
        
        try {
            // 1. Primero, insertar en historial_materia_prima
            $stmt_historial = $conexion->prepare("INSERT INTO historial_materia_prima (accion, responsable, nota, id_user) VALUES (?, ?, ?, ?)");
            $stmt_historial->bind_param("sssi", $accion, $responsable, $nota, $idVendedor4);
            $stmt_historial->execute();
            
            // Obtener el id_historial recién insertado
            $id_historial = $conexion->insert_id;
            $stmt_historial->close();
            
            // 2. Procesar cada material
            for($i = 0; $i < count($ids_material); $i++) {
                $id_material = intval($ids_material[$i]);
                $cantidad = floatval($cantidades[$i]);

                if($cantidad > 0) {
                    // 2a. Insertar en historial_matPrim_materiales con cantidad NEGATIVA
                    $stmt_detalle = $conexion->prepare("INSERT INTO historial_matPrim_materiales (id_historial, id_material, cantidad) VALUES (?, ?, ?)");
                    $cantidad_negativa = -$cantidad; // Negativo porque es retiro
                    $stmt_detalle->bind_param("iid", $id_historial, $id_material, $cantidad_negativa);
                    $stmt_detalle->execute();
                    $stmt_detalle->close();
                    
                    // 2b. Actualizar almacen_materiales (descontar cantidad)
                    // Primero verificar si existe el registro
                    $query_check = $conexion->prepare("SELECT id_almacen FROM almacen_materiales WHERE id_material = ?");
                    $query_check->bind_param("i", $id_material);
                    $query_check->execute();
                    $query_check->store_result();
                    
                    if($query_check->num_rows > 0) {
                        // Si existe, actualizar restando
                        $update = $conexion->prepare("UPDATE almacen_materiales SET cantidad = cantidad - ?, fecha_modificacion = NOW() WHERE id_material = ?");
                        $update->bind_param("di", $cantidad, $id_material);
                        $update->execute();
                        $update->close();
                    } else {
                        // Si no existe, crear con cantidad negativa
                        $insert = $conexion->prepare("INSERT INTO almacen_materiales (id_material, cantidad, fecha_modificacion) VALUES (?, ?, NOW())");
                        $cantidad_inicial = -$cantidad; // Negativo porque no había stock y se retira
                        $insert->bind_param("id", $id_material, $cantidad_inicial);
                        $insert->execute();
                        $insert->close();
                    }
                    
                    $query_check->close();
                } else {
                    throw new Exception("Cantidad inválida para el material ID: $id_material");
                }
            }
            
            // Confirmar la transacción
            $conexion->commit();
            
            $_SESSION['mensaje'] = "Materiales retirados correctamente. Historial registrado.";
            header("Location: ../b1t.php?p=stock_materiales.php");
            exit();

        } catch (Exception $e) {
            // Revertir en caso de error
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
    $_SESSION['mensaje'] = "No se recibieron datos válidos.";
    header("Location: ../b1t.php?p=stock_materiales.php");
    exit();
}

$conexion->close();
exit();
?>