<?php
session_start();
require '../includes/conexion.php';

if(isset($_POST['idalmacen']) && isset($_POST['cantidad'])) {
    $idAlmacen = intval($_POST['idalmacen']);
    $cantidad = floatval($_POST['cantidad']);

    // Datos para el historial
    $idVendedor4 = isset($_POST['idVendedor4']) ? intval($_POST['idVendedor4']) : 0;
    $accion = "Se editó";
    $responsable = null;
    $cantidadAnterior = floatval($_POST['cantidadAnterior']);
    $nota = "Se cambió de " . $cantidadAnterior . " a " . $cantidad;
    
    // Obtener el id_material desde almacen_materiales
    $query_material = $conexion->prepare("SELECT id_material FROM almacen_materiales WHERE id_almacen = ?");
    $query_material->bind_param("i", $idAlmacen);
    $query_material->execute();
    $query_material->bind_result($id_material);
    $query_material->fetch();
    $query_material->close();
    
    if(empty($id_material)) {
        $_SESSION['mensaje'] = "Error: No se encontró el material.";
        header("Location: ../b1t.php?p=stock_materiales.php");
        exit();
    }

    // Iniciar transacción para asegurar consistencia
    $conexion->begin_transaction();
    
    try {
        // 1. Insertar en historial_materia_prima
        $stmt_historial = $conexion->prepare("INSERT INTO historial_materia_prima (accion, responsable, nota, id_user) VALUES (?, ?, ?, ?)");
        $stmt_historial->bind_param("sssi", $accion, $responsable, $nota, $idVendedor4);
        $stmt_historial->execute();
        
        // Obtener el id_historial recién insertado
        $id_historial = $conexion->insert_id;
        $stmt_historial->close();
        
        // 2. Insertar en historial_matPrim_materiales (diferencia entre cantidades)
        $diferencia = $cantidad - $cantidadAnterior;
        
        $stmt_detalle = $conexion->prepare("INSERT INTO historial_matPrim_materiales (id_historial, id_material, cantidad) VALUES (?, ?, ?)");
        $stmt_detalle->bind_param("iid", $id_historial, $id_material, $diferencia);
        $stmt_detalle->execute();
        $stmt_detalle->close();
        
        // 3. Actualizar almacen_materiales
        $stmt_update = $conexion->prepare("UPDATE almacen_materiales SET cantidad = ?, fecha_modificacion = NOW() WHERE id_almacen = ?");
        $stmt_update->bind_param("di", $cantidad, $idAlmacen);
        $stmt_update->execute();
        
        if($stmt_update->affected_rows > 0) {
            $conexion->commit();
            $_SESSION['mensaje'] = "Cantidad actualizada correctamente. Historial registrado.";
        } else {
            throw new Exception("No se pudo actualizar el almacén.");
        }
        
        $stmt_update->close();
        
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error al procesar la operación: " . $e->getMessage();
    }
    
} else {
    $_SESSION['mensaje'] = "Faltan datos para actualizar.";
}

$conexion->close();
header("Location: ../b1t.php?p=stock_materiales.php");
exit();
?>

