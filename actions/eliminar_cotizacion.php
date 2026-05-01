<?php
session_start();
require '../includes/conexion.php';

$redirect = "../b1t.php?p=cotiz_ver.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = "Acceso no permitido.";
    header("Location: $redirect");
    exit;
}

$id_cotizacion = (int) ($_POST['id_cotizacion'] ?? 0);

if ($id_cotizacion <= 0) {
    $_SESSION['mensaje'] = "Datos inválidos.";
    header("Location: $redirect");
    exit;
}

// Iniciar transacción
try {
    $conexion->begin_transaction();
    
    // 1. Primero, buscar el id_venta y lugar_venta en la tabla ventas
    $id_venta = null;
    $lugar_venta = null;
    $sql_venta = "SELECT id_venta, lugar_venta FROM ventas WHERE id_cotizacion = ?";
    $stmt_venta = $conexion->prepare($sql_venta);
    $stmt_venta->bind_param("i", $id_cotizacion);
    $stmt_venta->execute();
    $result_venta = $stmt_venta->get_result();
    
    if ($row_venta = $result_venta->fetch_assoc()) {
        $id_venta = $row_venta['id_venta'];
        $lugar_venta = $row_venta['lugar_venta'];
    }
    $stmt_venta->close();
    
    // 2. Si existe id_venta, primero sumar los productos al almacén (operación inversa)
    if ($id_venta && $lugar_venta) {
        // Obtener los detalles de venta antes de eliminarlos
        $sql_detalles = "SELECT codigo, id_detalle, cantidad FROM detalle_venta WHERE id_venta = ?";
        $stmt_detalles = $conexion->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $id_venta);
        $stmt_detalles->execute();
        $result_detalles = $stmt_detalles->get_result();
        
        // Determinar la tabla de almacén según el lugar de venta
        $tablaAlmacen = '';
        if ($lugar_venta === 'Fabrica') {
            $tablaAlmacen = 'almacen';
        } elseif ($lugar_venta === 'Tienda') {
            $tablaAlmacen = 'almacen_tienda';
        } else {
            throw new Exception("Lugar de venta no válido: " . $lugar_venta);
        }
        
        // Procesar cada detalle para sumar al almacén
        while ($detalle = $result_detalles->fetch_assoc()) {
            $codigo = $detalle['codigo'];
            $idDetalle = $detalle['id_detalle'];
            $cantidadVenta = $detalle['cantidad'];
            
            // Verificar si existe el registro en la tabla correspondiente
            $sqlCheckAlmacen = "SELECT id_almacen, cantidad FROM $tablaAlmacen WHERE codigo = ? AND id_detalle = ?";
            $stmtCheck = $conexion->prepare($sqlCheckAlmacen);
            $stmtCheck->bind_param("si", $codigo, $idDetalle);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            
            if ($resultCheck->num_rows > 0) {
                // Si existe, sumar cantidad
                $fila = $resultCheck->fetch_assoc();
                $idAlmacen = $fila['id_almacen'];
                $cantidadActual = $fila['cantidad'];
                $nuevaCantidad = $cantidadActual + $cantidadVenta;
                
                $sqlAlmacen = "UPDATE $tablaAlmacen 
                               SET cantidad = ?, 
                                   fecha_modificacion = NOW()
                               WHERE id_almacen = ?";
                
                $stmtAlmacen = $conexion->prepare($sqlAlmacen);
                $stmtAlmacen->bind_param("ii", $nuevaCantidad, $idAlmacen);
                
                if (!$stmtAlmacen->execute()) {
                    throw new Exception("Error al actualizar $tablaAlmacen: " . $stmtAlmacen->error);
                }
                
                $stmtAlmacen->close();
            } else {
                // Si no existe, insertar nuevo registro con cantidad positiva
                $sqlInsertAlmacen = "INSERT INTO $tablaAlmacen (codigo, id_detalle, cantidad, fecha_modificacion) 
                                     VALUES (?, ?, ?, NOW())";
                
                $stmtInsertAlmacen = $conexion->prepare($sqlInsertAlmacen);
                $stmtInsertAlmacen->bind_param("sii", $codigo, $idDetalle, $cantidadVenta);
                
                if (!$stmtInsertAlmacen->execute()) {
                    throw new Exception("Error al insertar en $tablaAlmacen: " . $stmtInsertAlmacen->error);
                }
                
                $stmtInsertAlmacen->close();
            }
            
            $stmtCheck->close();
        }
        
        // Cerrar el statement de detalles después de usarlo
        $stmt_detalles->close();
        
        // 3. Ahora eliminar los pagos asociados
        $sql_pagos = "DELETE FROM pagos WHERE id_venta = ?";
        $stmt_pagos = $conexion->prepare($sql_pagos);
        $stmt_pagos->bind_param("i", $id_venta);
        if (!$stmt_pagos->execute()) {
            throw new Exception("Error al eliminar pagos");
        }
        $stmt_pagos->close();
        
        // 4. Eliminar los detalles de venta (ya procesados)
        $sql_detalles_delete = "DELETE FROM detalle_venta WHERE id_venta = ?";
        $stmt_detalles_delete = $conexion->prepare($sql_detalles_delete);
        $stmt_detalles_delete->bind_param("i", $id_venta);
        if (!$stmt_detalles_delete->execute()) {
            throw new Exception("Error al eliminar detalles de venta");
        }
        $stmt_detalles_delete->close();
        
        // 5. Eliminar la venta
        $sql_eliminar_venta = "DELETE FROM ventas WHERE id_venta = ?";
        $stmt_eliminar_venta = $conexion->prepare($sql_eliminar_venta);
        $stmt_eliminar_venta->bind_param("i", $id_venta);
        if (!$stmt_eliminar_venta->execute()) {
            throw new Exception("Error al eliminar venta");
        }
        $stmt_eliminar_venta->close();
        
    } else if ($id_venta) {
        // Si existe id_venta pero no lugar_venta, eliminar registros normalmente
        // (para casos donde la venta no fue completada)
        
        // Eliminar pagos asociados
        $sql_pagos = "DELETE FROM pagos WHERE id_venta = ?";
        $stmt_pagos = $conexion->prepare($sql_pagos);
        $stmt_pagos->bind_param("i", $id_venta);
        if (!$stmt_pagos->execute()) {
            throw new Exception("Error al eliminar pagos");
        }
        $stmt_pagos->close();
        
        // Eliminar detalles de venta
        $sql_detalles = "DELETE FROM detalle_venta WHERE id_venta = ?";
        $stmt_detalles = $conexion->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $id_venta);
        if (!$stmt_detalles->execute()) {
            throw new Exception("Error al eliminar detalles de venta");
        }
        $stmt_detalles->close();
        
        // Eliminar la venta
        $sql_eliminar_venta = "DELETE FROM ventas WHERE id_venta = ?";
        $stmt_eliminar_venta = $conexion->prepare($sql_eliminar_venta);
        $stmt_eliminar_venta->bind_param("i", $id_venta);
        if (!$stmt_eliminar_venta->execute()) {
            throw new Exception("Error al eliminar venta");
        }
        $stmt_eliminar_venta->close();
    }
    
    // 6. Finalmente, eliminar la cotización
    $sql_cotizacion = "DELETE FROM cotizaciones WHERE id_cotizacion = ?";
    $stmt_cotizacion = $conexion->prepare($sql_cotizacion);
    $stmt_cotizacion->bind_param("i", $id_cotizacion);
    
    if ($stmt_cotizacion->execute()) {
        $_SESSION['mensaje'] = "Cotización y registros relacionados eliminados exitosamente. Productos devueltos a " . ($lugar_venta === 'Fabrica' ? 'almacén' : 'almacén tienda');
        $conexion->commit();
    } else {
        throw new Exception("Error al eliminar cotización");
    }
    
    $stmt_cotizacion->close();
    
} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['mensaje'] = "Error al eliminar: " . $e->getMessage();
}

header("Location: $redirect");
exit;
?>