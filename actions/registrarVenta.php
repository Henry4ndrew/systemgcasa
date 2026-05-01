<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idCliente = $_POST['id_cliente'] ?? null;

    // Datos del cliente
    $nombreCliente = $_POST['nameCliente'];
    $empresaCliente = $_POST['empresaCliente'];
    $nitCliente = $_POST['nit'];
    $carnetCliente = $_POST['carnetCliente'];
    $dptoCliente = $_POST['departamento'];
    $correoCliente = $_POST['correo'];
    $celularCliente = $_POST['celCliente'];
    $celularEmpresa = $_POST['celEmpresa'];
    $notaCliente = $_POST['note_client'];
    $fechaRegistro = date('Y-m-d H:i:s');

    // Si existe un idCliente, actualizamos los datos del cliente
    if ($idCliente) {
        $sqlCliente = "UPDATE cartera_clientes 
                       SET nombre = ?, nit = ?, carnet_ci = ?, departamento = ?, celular = ?, cel_empresa = ?, correo = ?, empresa = ?, nota = ?, fecha_registro = ?
                       WHERE id_cliente = ?";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("ssssssssssi", $nombreCliente, $nitCliente, $carnetCliente, $dptoCliente, $celularCliente, $celularEmpresa, $correoCliente, $empresaCliente, $notaCliente, $fechaRegistro, $idCliente);

        if (!$stmtCliente->execute()) {
            $_SESSION['mensaje'] = "Error al actualizar el cliente: " . $stmtCliente->error;
            header("Location: ../b1t.php?p=ventas_registro.php");
            exit;
        }
    } else {
        // Si no existe un idCliente, insertamos un nuevo cliente
        $sqlCliente = "INSERT INTO cartera_clientes (nombre, nit, carnet_ci, departamento, celular, cel_empresa, correo, empresa, nota, fecha_registro) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("ssssssssss", $nombreCliente, $nitCliente, $carnetCliente, $dptoCliente, $celularCliente, $celularEmpresa, $correoCliente, $empresaCliente, $notaCliente, $fechaRegistro);

        if ($stmtCliente->execute()) {
            $idCliente = $stmtCliente->insert_id;
        } else {
            $_SESSION['mensaje'] = "Error al registrar el cliente: " . $stmtCliente->error;
            header("Location: ../b1t.php?p=ventas_registro.php");
            exit;
        }
    }

    // Insertar datos en la tabla ventas
    $idVendedor = $_POST['idVendedor'];
    $totalVenta = $_POST['total_venta'];
    $nota = $_POST['nota'] ?? '';
    $nota = trim($nota); 
    $fechaEntrega = $_POST['fechaEntrega'] ?? null;
    $fechaVenta = date('Y-m-d H:i:s');
    $lugarVenta = $_POST['ambiente_venta'];

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

    $sqlVenta = "INSERT INTO ventas (id_cliente, fecha_entrega, fecha_venta, total_venta, id_user, nota, tipo_descuento, valor_descuento, lugar_venta) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtVenta = $conexion->prepare($sqlVenta);
    $stmtVenta->bind_param("isssissds", $idCliente, $fechaEntrega, $fechaVenta, $totalVenta, $idVendedor, $nota, $tipoDescuento, $valorDescuento, $lugarVenta);

    if ($stmtVenta->execute()) {
        $idVenta = $stmtVenta->insert_id;

        // Insertar datos en la tabla pagos
        $tipoPago = $_POST['tipoPago'];
        $anticipoVenta = $_POST['anticipoVenta'];
        $saldoVenta = $_POST['saldo-cobro'];
        $fechaSigPago = $_POST['fechaSigPago'];
        $fechaPagoActual = date('Y-m-d H:i:s');
        $idVendedorCobro = $_POST['idVendedor'];

        $sqlPago = "INSERT INTO pagos (id_venta, tipo_pago, anticipo, saldo, fecha_sig_pago, fecha_pago_actual, id_user) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtPago = $conexion->prepare($sqlPago);
        $stmtPago->bind_param("issssss", $idVenta, $tipoPago, $anticipoVenta, $saldoVenta, $fechaSigPago, $fechaPagoActual, $idVendedorCobro);

        if ($stmtPago->execute()) {
            // Insertar datos en la tabla detalle_venta y descontar del almacén
            if (isset($_POST['codigo']) && is_array($_POST['codigo'])) {
                foreach ($_POST['codigo'] as $index => $codigoProducto) {
                    $idDetalle = $_POST['idDetalle'][$index];
                    $precio = $_POST['precio'][$index];
                    $cantidad = $_POST['cantidad'][$index];
                    $subtotal = $_POST['subtotal'][$index];
                    $detallesAdicionales = $_POST['descripcion'][$index] ?? '';
                    $caracteristicaAdicional = $_POST['caracteristica'][$index] ?? '';

                    // Insertar en detalle_venta
                    $sqlDetalle = "INSERT INTO detalle_venta (id_venta, codigo, id_detalle, precio_venta, cantidad, sub_total, newDetail, newCaracteristic) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtDetalle = $conexion->prepare($sqlDetalle);
                    $stmtDetalle->bind_param("isiiddss", $idVenta, $codigoProducto, $idDetalle, $precio, $cantidad, $subtotal, $detallesAdicionales, $caracteristicaAdicional);

                    if (!$stmtDetalle->execute()) {
                        $_SESSION['mensaje'] = "Error al registrar el detalle de la venta: " . $stmtDetalle->error;
                        header("Location: ../b1t.php?p=ventas_registro.php");
                        exit;
                    }


                    






                     // ===========================================
                    // DESCONTAR PRODUCTOS DEL ALMACÉN SEGÚN LUGAR DE VENTA
                    // ===========================================
                    
                    $tablaAlmacen = '';
                    $campoId = '';


                    // ===========================================
                    // DESCONTAR PRODUCTOS DEL ALMACÉN SEGÚN LUGAR DE VENTA
                    // ===========================================
                    
                    // MODIFICACIÓN: Determinar de qué tabla descontar según el lugar de venta
                    if ($lugarVenta === 'Fabrica' || $lugarVenta === 'Tienda') {
                        // Definir tabla y campo según el lugar de venta
                        if ($lugarVenta === 'Fabrica') {
                            $tablaAlmacen = 'almacen';
                        } elseif ($lugarVenta === 'Tienda') {
                            $tablaAlmacen = 'almacen_tienda';
                        }
                        
                        // La cantidad a descontar (convertir a negativo para almacén)
                        $cantidadADescontar = -$cantidad;
                        
                        // MODIFICACIÓN: Buscar producto en la tabla correspondiente
                        $sqlBuscarAlmacen = "SELECT id_almacen, cantidad FROM $tablaAlmacen 
                                             WHERE codigo = ? AND id_detalle = ?";
                        $stmtBuscar = $conexion->prepare($sqlBuscarAlmacen);
                        $stmtBuscar->bind_param("si", $codigoProducto, $idDetalle);
                        $stmtBuscar->execute();
                        $resultadoAlmacen = $stmtBuscar->get_result();
                        
                        if ($resultadoAlmacen->num_rows > 0) {
                            // El producto ya existe en almacén, actualizar cantidad
                            $filaAlmacen = $resultadoAlmacen->fetch_assoc();
                            $idAlmacen = $filaAlmacen['id_almacen'];
                            $cantidadExistente = $filaAlmacen['cantidad'];
                            
                            // Calcular nueva cantidad
                            $nuevaCantidad = $cantidadExistente + $cantidadADescontar;
                            
                            // MODIFICACIÓN: Actualizar en la tabla correspondiente
                            if ($lugarVenta === 'Fabrica') {
                                $sqlActualizarAlmacen = "UPDATE almacen SET cantidad = ? 
                                                         WHERE id_almacen = ?";
                            } elseif ($lugarVenta === 'Tienda') {
                                $sqlActualizarAlmacen = "UPDATE almacen_tienda SET cantidad = ?, fecha_modificacion = NOW() 
                                                         WHERE id_almacen = ?";
                            }
                            
                            $stmtActualizar = $conexion->prepare($sqlActualizarAlmacen);
                            $stmtActualizar->bind_param("ii", $nuevaCantidad, $idAlmacen);
                            
                            if (!$stmtActualizar->execute()) {
                                $_SESSION['mensaje'] = "Error al actualizar $tablaAlmacen para producto: " . $stmtActualizar->error;
                                header("Location: ../b1t.php?p=ventas_registro.php");
                                exit;
                            }
                        } else {
                            // El producto no existe en almacén, insertar nuevo registro
                            // MODIFICACIÓN: Insertar en la tabla correspondiente
                            if ($lugarVenta === 'Fabrica') {
                                $sqlInsertarAlmacen = "INSERT INTO almacen (codigo, id_detalle, cantidad) 
                                                       VALUES (?, ?, ?)";
                                $stmtInsertar = $conexion->prepare($sqlInsertarAlmacen);
                                $stmtInsertar->bind_param("sii", $codigoProducto, $idDetalle, $cantidadADescontar);
                            } elseif ($lugarVenta === 'Tienda') {
                                $sqlInsertarAlmacen = "INSERT INTO almacen_tienda (codigo, id_detalle, cantidad, fecha_modificacion) 
                                                       VALUES (?, ?, ?, NOW())";
                                $stmtInsertar = $conexion->prepare($sqlInsertarAlmacen);
                                $stmtInsertar->bind_param("sii", $codigoProducto, $idDetalle, $cantidadADescontar);
                            }
                            
                            if (!$stmtInsertar->execute()) {
                                $_SESSION['mensaje'] = "Error al insertar en $tablaAlmacen para producto: " . $stmtInsertar->error;
                                header("Location: ../b1t.php?p=ventas_registro.php");
                                exit;
                            }
                        }
                        
                        // Cerrar statements de almacén
                        if (isset($stmtBuscar)) $stmtBuscar->close();
                        if (isset($stmtActualizar)) $stmtActualizar->close();
                        if (isset($stmtInsertar)) $stmtInsertar->close();
                    }
                }
            }











            $_SESSION['mensaje'] = "Venta registrada correctamente y productos descontados del almacén.";
            header("Location: ../b1t.php?p=ventas_historial.php");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al registrar el pago: " . $stmtPago->error;
            header("Location: ../b1t.php?p=ventas_registro.php");
            exit;
        }
    } else {
        $_SESSION['mensaje'] = "Error al registrar la venta: " . $stmtVenta->error;
        header("Location: ../b1t.php?p=ventas_registro.php");
        exit;
    }
} else {
    $_SESSION['mensaje'] = "No se han enviado datos del formulario.";
    header("Location: ../b1t.php?p=ventas_registro.php");
    exit;
}
?>