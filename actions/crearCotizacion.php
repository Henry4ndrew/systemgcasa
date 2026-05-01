<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';

// Verificar si la conexión está disponible
if (!$conexion) {
    $_SESSION['mensaje'] = "Error de conexión a la base de datos";
    header("Location: ../b1t.php?p=cotiz_crear.php");
    exit;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // ========== SECCIÓN CLIENTES ==========
    $idCliente = isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
    
    if (empty($idCliente)) {
        // Insertar nuevo cliente
        $nombre = $_POST['nameCliente'] ?? '';
        $empresa = $_POST['empresaCliente'] ?? '';
        $nit = $_POST['nit'] ?? '';
        $carnet = $_POST['carnetCliente'] ?? '';
        $departamento = $_POST['departamento'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $celular = $_POST['celCliente'] ?? '';
        $celEmpresa = $_POST['celEmpresa'] ?? '';
        $notaCliente = $_POST['note_client'] ?? '';
        $fechaRegistro = date('Y-m-d H:i:s');
        
        $sqlCliente = "INSERT INTO cartera_clientes (nombre, nit, carnet_ci, departamento, celular, cel_empresa, correo, empresa, nota, fecha_registro) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("ssssssssss", $nombre, $nit, $carnet, $departamento, $celular, $celEmpresa, $correo, $empresa, $notaCliente, $fechaRegistro);
        
        if (!$stmtCliente->execute()) {
            throw new Exception("Error al registrar cliente: " . $stmtCliente->error);
        }
        
        $idCliente = $conexion->insert_id;
    } else {
        // Actualizar cliente existente
        $nombre = $_POST['nameCliente'] ?? '';
        $empresa = $_POST['empresaCliente'] ?? '';
        $nit = $_POST['nit'] ?? '';
        $carnet = $_POST['carnetCliente'] ?? '';
        $departamento = $_POST['departamento'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $celular = $_POST['celCliente'] ?? '';
        $celEmpresa = $_POST['celEmpresa'] ?? '';
        $notaCliente = $_POST['note_client'] ?? '';
        
        $sqlUpdateCliente = "UPDATE cartera_clientes SET 
                            nombre = ?, nit = ?, carnet_ci = ?, departamento = ?, 
                            celular = ?, cel_empresa = ?, correo = ?, empresa = ?, nota = ? 
                            WHERE id_cliente = ?";
        $stmtUpdateCliente = $conexion->prepare($sqlUpdateCliente);
        $stmtUpdateCliente->bind_param("sssssssssi", $nombre, $nit, $carnet, $departamento, 
                                      $celular, $celEmpresa, $correo, $empresa, $notaCliente, $idCliente);
        
        if (!$stmtUpdateCliente->execute()) {
            throw new Exception("Error al actualizar cliente: " . $stmtUpdateCliente->error);
        }
    }
    
    // ========== SECCIÓN COTIZACIÓN ==========
    $titulo = $_POST['tituloCotizacion'] ?? '';
    $fechaCaducidad = !empty($_POST['fechaCaducidad']) ? $_POST['fechaCaducidad'] : null;
    $cuentaBancaria = !empty($_POST['cuenta_bancaria']) ? $_POST['cuenta_bancaria'] : null;
    $aprobado = "no";
    $fechaCotizacion = date('Y-m-d H:i:s');
    $idPiePagina = $_POST['piePagina'] ?? '';
    
    $sqlCotizacion = "INSERT INTO cotizaciones (titulo, fecha_caducidad, cuenta_bancaria, aprobado, fecha_cotizacion, id_dataPiePag) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmtCotizacion = $conexion->prepare($sqlCotizacion);
    $stmtCotizacion->bind_param("sssssi", $titulo, $fechaCaducidad, $cuentaBancaria, $aprobado, $fechaCotizacion, $idPiePagina);
    
    if (!$stmtCotizacion->execute()) {
        throw new Exception("Error al crear cotización: " . $stmtCotizacion->error);
    }
    
    $idCotizacion = $conexion->insert_id;
    
    // ========== SECCIÓN VENTA ==========
    $idVendedor = $_POST['idVendedor'] ?? 0;
    $notaVenta = $_POST['nota'] ?? '';
    $totalVenta = $_POST['total_venta'] ?? 0;

    
    $tipoDescuento = $_POST['tipoDescuento']; 
    $descuentoMonto = $_POST['descuentoMonto']; 
    $descuentoPorcentaje = $_POST['descuentoPorcentaje'];

    if (empty($tipoDescuento)) {
        $valorDescuento = null;
    } elseif ($tipoDescuento == "monto") {
        $valorDescuento = $descuentoMonto; 
    } elseif ($tipoDescuento == "porcentaje") {
        $valorDescuento = $descuentoPorcentaje;
    }
    
    $sqlVenta = "INSERT INTO ventas (id_cliente, id_cotizacion, id_user, nota, total_venta, tipo_descuento, valor_descuento) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtVenta = $conexion->prepare($sqlVenta);
    $stmtVenta->bind_param("iiisdsd", $idCliente,  $idCotizacion, $idVendedor, $notaVenta, $totalVenta, $tipoDescuento, $valorDescuento);
    
    if (!$stmtVenta->execute()) {
        throw new Exception("Error al registrar venta: " . $stmtVenta->error);
    }
    
    $idVenta = $conexion->insert_id;
    
    // ========== DETALLES DE LA VENTA ==========
    if (isset($_POST['codigo']) && is_array($_POST['codigo'])) {
        foreach ($_POST['codigo'] as $index => $codigoProducto) {
            $idDetalle = $_POST['idDetalle'][$index];
            $precio = $_POST['precio'][$index];
            $cantidad = $_POST['cantidad'][$index];
            $subtotal = $_POST['subtotal'][$index];
            $detallesAdicionales = $_POST['descripcion'][$index] ?? '';
            $caracteristicaAdicional = $_POST['caracteristica'][$index] ?? '';
            
            $sqlDetalle = "INSERT INTO detalle_venta (id_venta, codigo, id_detalle, precio_venta, cantidad, sub_total, newDetail, newCaracteristic) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtDetalle = $conexion->prepare($sqlDetalle);
            $stmtDetalle->bind_param("isiiddss", $idVenta, $codigoProducto, $idDetalle, $precio, $cantidad, $subtotal, $detallesAdicionales, $caracteristicaAdicional);
            
            if (!$stmtDetalle->execute()) {
                throw new Exception("Error al registrar detalle de venta: " . $stmtDetalle->error);
            }
        }
    }
    
    // Confirmar transacción
    $conexion->commit();
    
    $_SESSION['mensaje'] = "Cotización registrada correctamente.";
    header("Location: ../b1t.php?p=cotiz_ver.php");
    exit;
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    header("Location: ../b1t.php?p=cotiz_crear.php");
    exit;
} finally {
    // Cerrar conexión
    if (isset($stmtCliente)) $stmtCliente->close();
    if (isset($stmtUpdateCliente)) $stmtUpdateCliente->close();
    if (isset($stmtCotizacion)) $stmtCotizacion->close();
    if (isset($stmtVenta)) $stmtVenta->close();
    if (isset($stmtDetalle)) $stmtDetalle->close();
    $conexion->close();
}
?>