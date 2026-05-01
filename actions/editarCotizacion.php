<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';

// Verificar si la conexión está disponible
if (!$conexion) {
    $_SESSION['mensaje'] = "Error de conexión a la base de datos";
    header("Location: ../b1t.php?p=cotiz_ver.php");
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
    $idCotizacion = isset($_POST['idCotizacion']) && !empty($_POST['idCotizacion']) ? $_POST['idCotizacion'] : null;

    // Validar que tenemos un ID de cotización
    if (!$idCotizacion) {
        throw new Exception("Error: No se recibió ID de cotización para actualizar");
    }

    $titulo = $_POST['tituloCotizacion'] ?? '';
    $fechaCaducidad = !empty($_POST['fechaCaducidad']) ? $_POST['fechaCaducidad'] : null;
    $cuentaBancaria = !empty($_POST['cuenta_bancaria']) ? $_POST['cuenta_bancaria'] : null;
    $aprobado = "no";
    $fechaCotizacion = date('Y-m-d H:i:s');
    $idPiePagina = $_POST['piePagina'] ?? '';

    // Verificar si la cotización existe
    $checkSql = "SELECT id_cotizacion FROM cotizaciones WHERE id_cotizacion = ?";
$checkStmt = $conexion->prepare($checkSql);
$checkStmt->bind_param("i", $idCotizacion);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
        $sqlCotizacion = "UPDATE cotizaciones 
              SET titulo = ?, 
                  fecha_caducidad = ?, 
                  cuenta_bancaria = ?, 
                  aprobado = ?, 
                  fecha_cotizacion = ?, 
                  id_dataPiePag = ?
              WHERE id_cotizacion = ?";

    $stmtCotizacion = $conexion->prepare($sqlCotizacion);
    $stmtCotizacion->bind_param("sssssii", 
        $titulo, 
        $fechaCaducidad, 
        $cuentaBancaria, 
        $aprobado, 
        $fechaCotizacion, 
        $idPiePagina,
        $idCotizacion
    );
    
    // FALTA: Ejecutar la actualización
    if (!$stmtCotizacion->execute()) {
        throw new Exception("Error al actualizar cotización: " . $stmtCotizacion->error);
    }
} else {
    $_SESSION['mensaje'] = "Id de cotización no encontrada";
    throw new Exception("La cotización con ID $idCotizacion no existe");
}

$checkStmt->close();
    
    // ========== SECCIÓN VENTA ==========
    $idVendedor = $_POST['idVendedor'] ?? 0;
    $notaVenta = $_POST['nota'] ?? '';
    $totalVenta = $_POST['total_venta'] ?? 0;

    $tipoDescuento = $_POST['tipoDescuento'] ?? null; 
    $descuentoMonto = $_POST['descuentoMonto'] ?? 0; 
    $descuentoPorcentaje = $_POST['descuentoPorcentaje'] ?? 0;

    if (empty($tipoDescuento)) {
        $valorDescuento = null;
    } elseif ($tipoDescuento == "monto") {
        $valorDescuento = $descuentoMonto; 
    } elseif ($tipoDescuento == "porcentaje") {
        $valorDescuento = $descuentoPorcentaje;
    } else {
        $valorDescuento = null;
    }

    // PRIMERO: Obtener el id_venta basado en id_cotizacion
    $sqlGetVentaId = "SELECT id_venta, id_cliente FROM ventas WHERE id_cotizacion = ?";
    $stmtGetVentaId = $conexion->prepare($sqlGetVentaId);
    $stmtGetVentaId->bind_param("i", $idCotizacion);
    $stmtGetVentaId->execute();
    $stmtGetVentaId->bind_result($idVenta, $idClienteExistente);
    $stmtGetVentaId->fetch();
    $stmtGetVentaId->close();

    // Verificar si existe una venta asociada a esta cotización
    if (!$idVenta) {
        $_SESSION['mensaje'] = "Error: No se encontró una venta asociada a esta cotización";
        throw new Exception("No se encontró venta con id_cotizacion: $idCotizacion");
    }

    // SEGUNDO: Actualizar la venta existente
    $sqlVenta = "UPDATE ventas 
                SET id_cliente = ?,
                    id_user = ?, 
                    nota = ?, 
                    total_venta = ?, 
                    tipo_descuento = ?, 
                    valor_descuento = ?
                WHERE id_venta = ?";

    $stmtVenta = $conexion->prepare($sqlVenta);
    $stmtVenta->bind_param("iisdsdi", 
        $idCliente,  // Usar el idCliente (nuevo o actualizado)
        $idVendedor, 
        $notaVenta, 
        $totalVenta, 
        $tipoDescuento, 
        $valorDescuento,
        $idVenta
    );

    if (!$stmtVenta->execute()) {
        throw new Exception("Error al actualizar venta: " . $stmtVenta->error);
    }

    // Verificar si se actualizó
    if ($stmtVenta->affected_rows > 0) {
        $_SESSION['mensaje_venta'] = "Venta actualizada correctamente";
    } else {
        $_SESSION['mensaje_venta'] = "No hubo cambios en la venta";
    }

    $stmtVenta->close();
    
    // ========== DETALLES DE LA VENTA ==========
    // PRIMERO: Eliminar los detalles de venta existentes
    $sqlEliminarDetalles = "DELETE FROM detalle_venta WHERE id_venta = ?";
    $stmtEliminarDetalles = $conexion->prepare($sqlEliminarDetalles);
    $stmtEliminarDetalles->bind_param("i", $idVenta);
    
    if (!$stmtEliminarDetalles->execute()) {
        throw new Exception("Error al eliminar detalles de venta anteriores: " . $stmtEliminarDetalles->error);
    }
    
    $stmtEliminarDetalles->close();
    
    // SEGUNDO: Insertar los nuevos detalles de venta
    if (isset($_POST['codigo']) && is_array($_POST['codigo'])) {
        foreach ($_POST['codigo'] as $index => $codigoProducto) {
            $idDetalle = $_POST['idDetalle'][$index] ?? null;
            $precio = $_POST['precio'][$index] ?? 0;
            $cantidad = $_POST['cantidad'][$index] ?? 0;
            $subtotal = $_POST['subtotal'][$index] ?? 0;
            $detallesAdicionales = $_POST['descripcion'][$index] ?? '';
            $caracteristicaAdicional = $_POST['caracteristica'][$index] ?? '';
            
            // Validar datos requeridos
            if (empty($codigoProducto) || empty($idDetalle) || $cantidad <= 0) {
                continue; // Saltar este registro si falta información esencial
            }
            
            $sqlDetalle = "INSERT INTO detalle_venta (id_venta, codigo, id_detalle, precio_venta, cantidad, sub_total, newDetail, newCaracteristic) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtDetalle = $conexion->prepare($sqlDetalle);
            $stmtDetalle->bind_param("isiidiss", $idVenta, $codigoProducto, $idDetalle, $precio, $cantidad, $subtotal, $detallesAdicionales, $caracteristicaAdicional);
            
            if (!$stmtDetalle->execute()) {
                throw new Exception("Error al registrar detalle de venta: " . $stmtDetalle->error);
            }
            
            $stmtDetalle->close();
        }
    }
    
    // Confirmar transacción
    $conexion->commit();
    
    $_SESSION['mensaje'] = "Cotización actualizada correctamente.";
    header("Location: ../b1t.php?p=cotiz_ver.php");
    exit;
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($conexion) {
        $conexion->rollback();
    }
    
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    header("Location: ../b1t.php?p=cotiz_ver.php");
    exit;
} finally {
    // Cerrar conexión
    if (isset($stmtCliente)) $stmtCliente->close();
    if (isset($stmtUpdateCliente)) $stmtUpdateCliente->close();
    if (isset($checkStmt)) $checkStmt->close();
    if (isset($stmtCotizacion)) $stmtCotizacion->close();
    if (isset($stmtGetVentaId)) $stmtGetVentaId->close();
    if (isset($stmtVenta)) $stmtVenta->close();
    if (isset($stmtEliminarDetalles)) $stmtEliminarDetalles->close();
    if (isset($stmtDetalle)) $stmtDetalle->close();
    if ($conexion) $conexion->close();
}
?>
