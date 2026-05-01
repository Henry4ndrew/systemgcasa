<?php
session_start();
require '../includes/conexion.php';
date_default_timezone_set('America/La_Paz');

$conexion->begin_transaction();
try {
    if (!isset($_POST['id_venta_cotiz'])) {
        throw new Exception("ID de venta no recibido");
    }
    $idVenta = $_POST['id_venta_cotiz'];
    $idVendedor = $_POST['idVendedor'];
    $fechaEntrega = !empty($_POST['fechaEntrega']) ? $_POST['fechaEntrega'] : null;
    $ambienteVenta = $_POST['ambiente_venta'];
    $tipoPago = $_POST['tipoPago'];
    $anticipo = $_POST['anticipoVenta'];
    $saldo = $_POST['saldo-cobro'];
    $fechaSigPago = !empty($_POST['fechaSigPago']) ? $_POST['fechaSigPago'] : null;
    
    // DEBUG: Verificar valores recibidos
    error_log("DEBUG - Ambiente venta recibido: " . $ambienteVenta);
    
    // 1. ACTUALIZAR TABLA VENTAS
    $sqlVenta = "UPDATE ventas 
                 SET id_user = ?, 
                     fecha_entrega = ?, 
                     fecha_venta = NOW(), 
                     lugar_venta = ?
                 WHERE id_venta = ?";
    
    $stmtVenta = $conexion->prepare($sqlVenta);
    $stmtVenta->bind_param("issi", $idVendedor, $fechaEntrega, $ambienteVenta, $idVenta);
    
    if (!$stmtVenta->execute()) {
        throw new Exception("Error al actualizar venta: " . $stmtVenta->error);
    }
    
    // Verificar si se afectó alguna fila
    if ($stmtVenta->affected_rows == 0) {
        throw new Exception("No se encontró la venta con ID: " . $idVenta);
    }
    
    // Obtener el id_cotizacion de la venta
    $sqlGetCotizacion = "SELECT id_cotizacion FROM ventas WHERE id_venta = ?";
    $stmtGetCotiz = $conexion->prepare($sqlGetCotizacion);
    $stmtGetCotiz->bind_param("i", $idVenta);
    $stmtGetCotiz->execute();
    $resultCotiz = $stmtGetCotiz->get_result();
    
    if ($row = $resultCotiz->fetch_assoc()) {
        $idCotizacion = $row['id_cotizacion'];
        
        // 2. ACTUALIZAR TABLA COTIZACIONES
        $sqlCotizacion = "UPDATE cotizaciones SET aprobado = 'si' WHERE id_cotizacion = ?";
        $stmtCotiz = $conexion->prepare($sqlCotizacion);
        $stmtCotiz->bind_param("i", $idCotizacion);
        
        if (!$stmtCotiz->execute()) {
            throw new Exception("Error al actualizar cotización: " . $stmtCotiz->error);
        }
    }
    
    // 3. INSERTAR EN TABLA PAGOS
    $sqlPago = "INSERT INTO pagos (id_venta, tipo_pago, anticipo, saldo, fecha_sig_pago, id_user, fecha_pago_actual) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmtPago = $conexion->prepare($sqlPago);
    $stmtPago->bind_param("issdsi", $idVenta, $tipoPago, $anticipo, $saldo, $fechaSigPago, $idVendedor);
    
    if (!$stmtPago->execute()) {
        throw new Exception("Error al insertar pago: " . $stmtPago->error);
    }

    // 4. ACTUALIZAR TABLA ALMACEN (RESTAR CANTIDADES) SEGÚN AMBIENTE DE VENTA
    // Primero, obtener los detalles de venta
    $sqlDetalles = "SELECT codigo, id_detalle, cantidad FROM detalle_venta WHERE id_venta = ?";
    $stmtDetalles = $conexion->prepare($sqlDetalles);
    $stmtDetalles->bind_param("i", $idVenta);
    $stmtDetalles->execute();
    $resultDetalles = $stmtDetalles->get_result();
    
    // DEBUG: Contar detalles encontrados
    $numDetalles = $resultDetalles->num_rows;
    error_log("DEBUG - Número de detalles de venta: " . $numDetalles);
    error_log("DEBUG - Ambiente venta para restar: " . $ambienteVenta);
    
    while ($detalle = $resultDetalles->fetch_assoc()) {
        $codigo = $detalle['codigo'];
        $idDetalle = $detalle['id_detalle'];
        $cantidadVenta = $detalle['cantidad'];
        
        error_log("DEBUG - Procesando producto: Código=$codigo, ID Detalle=$idDetalle, Cantidad=$cantidadVenta");
        
        // Determinar la tabla de almacén según el ambiente de venta
        $tablaAlmacen = '';
        if ($ambienteVenta === 'Fabrica') {
            $tablaAlmacen = 'almacen';
        } elseif ($ambienteVenta === 'Tienda') {
            $tablaAlmacen = 'almacen_tienda';
        } else {
            error_log("ERROR - Ambiente de venta no reconocido: " . $ambienteVenta);
            throw new Exception("Ambiente de venta no válido: " . $ambienteVenta);
        }
        
        error_log("DEBUG - Tabla de almacén seleccionada: " . $tablaAlmacen);
        
        // Verificar si existe el registro en la tabla correspondiente
        $sqlCheckAlmacen = "SELECT id_almacen, cantidad FROM $tablaAlmacen WHERE codigo = ? AND id_detalle = ?";
        $stmtCheck = $conexion->prepare($sqlCheckAlmacen);
        $stmtCheck->bind_param("si", $codigo, $idDetalle);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        
        if ($resultCheck->num_rows > 0) {
            // Si existe, restar cantidad (permite valores negativos)
            $fila = $resultCheck->fetch_assoc();
            $idAlmacen = $fila['id_almacen'];
            $cantidadActual = $fila['cantidad'];
            $nuevaCantidad = $cantidadActual - $cantidadVenta;
            
            error_log("DEBUG - Producto existe en $tablaAlmacen. ID: $idAlmacen, Actual: $cantidadActual, Nueva: $nuevaCantidad");
            
            $sqlAlmacen = "UPDATE $tablaAlmacen 
                           SET cantidad = ?, 
                               fecha_modificacion = NOW()
                           WHERE id_almacen = ?";
            
            $stmtAlmacen = $conexion->prepare($sqlAlmacen);
            $stmtAlmacen->bind_param("ii", $nuevaCantidad, $idAlmacen);
            
            if (!$stmtAlmacen->execute()) {
                error_log("ERROR - No se pudo actualizar $tablaAlmacen: " . $stmtAlmacen->error);
                throw new Exception("Error al actualizar $tablaAlmacen: " . $stmtAlmacen->error);
            }
            
            $stmtAlmacen->close();
            error_log("DEBUG - Producto actualizado en $tablaAlmacen exitosamente");
        } else {
            // Si no existe, insertar nuevo registro con cantidad negativa
            error_log("DEBUG - Producto NO existe en $tablaAlmacen. Insertando nuevo registro con cantidad negativa");
            
            $sqlInsertAlmacen = "INSERT INTO $tablaAlmacen (codigo, id_detalle, cantidad, fecha_modificacion) 
                                 VALUES (?, ?, -?, NOW())";
            
            $stmtInsertAlmacen = $conexion->prepare($sqlInsertAlmacen);
            $stmtInsertAlmacen->bind_param("sii", $codigo, $idDetalle, $cantidadVenta);
            
            if (!$stmtInsertAlmacen->execute()) {
                error_log("ERROR - No se pudo insertar en $tablaAlmacen: " . $stmtInsertAlmacen->error);
                throw new Exception("Error al insertar en $tablaAlmacen: " . $stmtInsertAlmacen->error);
            }
            
            $stmtInsertAlmacen->close();
            error_log("DEBUG - Producto insertado en $tablaAlmacen exitosamente");
        }
        
        $stmtCheck->close();
    }
    
    $conexion->commit();
    $_SESSION['mensaje'] = "Venta procesada correctamente y productos descontados de " . ($ambienteVenta === 'Fabrica' ? 'almacén' : 'almacén tienda');
    $_SESSION['tipo_mensaje'] = "success";
    
} catch (Exception $e) {
    $conexion->rollback();
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
    error_log("ERROR en aprobarCotizacion.php: " . $e->getMessage());
}

if (isset($stmtVenta)) $stmtVenta->close();
if (isset($stmtGetCotiz)) $stmtGetCotiz->close();
if (isset($stmtCotiz)) $stmtCotiz->close();
if (isset($stmtPago)) $stmtPago->close();
if (isset($stmtDetalles)) $stmtDetalles->close();

header("Location: ../b1t.php?p=cotiz_ver.php");
exit();
?>
