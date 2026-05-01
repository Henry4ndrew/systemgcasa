<?php
require '../includes/conexion.php';

if (isset($_POST['id_cotizacion'])) {
    $idCotizacion = $_POST['id_cotizacion'];
    
    // Obtener datos principales de la cotización
    $queryCotizacion = "
        SELECT 
            c.id_cotizacion,
            c.titulo,
            c.fecha_caducidad,
            c.cuenta_bancaria,
            c.aprobado,
            c.id_dataPiePag
        FROM cotizaciones c
        WHERE c.id_cotizacion = $idCotizacion
        LIMIT 1
    ";
    
    $resCotizacion = mysqli_query($conexion, $queryCotizacion);
    
    if ($resCotizacion && mysqli_num_rows($resCotizacion) > 0) {
        $cotizacion = mysqli_fetch_assoc($resCotizacion);
        
        // Obtener datos de venta relacionada si existe
        $venta = null;
        $cliente = null; // Variable para almacenar datos del cliente
        
        $queryVenta = "
            SELECT 
                v.id_venta,
                v.id_cliente,
                v.total_venta,
                v.nota,
                v.tipo_descuento,
                v.valor_descuento
            FROM ventas v
            WHERE v.id_cotizacion = $idCotizacion
            LIMIT 1
        ";
        
        $resVenta = mysqli_query($conexion, $queryVenta);
        if ($resVenta && mysqli_num_rows($resVenta) > 0) {
            $venta = mysqli_fetch_assoc($resVenta);
            $idVenta = $venta['id_venta'];
            
            // Obtener datos del cliente si existe id_cliente
            if (!empty($venta['id_cliente'])) {
                $idCliente = $venta['id_cliente'];
                $queryCliente = "
                    SELECT 
                        id_cliente,
                        nombre,
                        nit,
                        carnet_ci,
                        departamento,
                        celular,
                        cel_empresa,
                        correo,
                        empresa,
                        nota
                    FROM cartera_clientes
                    WHERE id_cliente = $idCliente
                    LIMIT 1
                ";
                
                $resCliente = mysqli_query($conexion, $queryCliente);
                if ($resCliente && mysqli_num_rows($resCliente) > 0) {
                    $cliente = mysqli_fetch_assoc($resCliente);
                }
            }
            
            // Obtener los detalles de venta
            $queryDetalles = "
                SELECT 
                    dv.id,
                    dv.id_venta,
                    dv.codigo,
                    dv.id_detalle,
                    dv.newDetail,
                    dv.newCaracteristic,
                    dv.precio_venta,
                    dv.cantidad,
                    dv.sub_total,
                    lp.nombre,
                    lp.caracteristicas AS caracteristicas_original,
                    dp.detalle AS detalle_producto,
                    dp.medida,
                    dp.precio_unitario,
                    (SELECT ruta_imagen FROM imagenes WHERE codigo = dv.codigo LIMIT 1) AS ruta_imagen
                FROM detalle_venta dv
                LEFT JOIN lista_productos lp ON dv.codigo = lp.codigo
                LEFT JOIN detalle_producto dp ON dv.id_detalle = dp.id_detalle
                WHERE dv.id_venta = $idVenta
            ";
            
            $resultadoDetalles = mysqli_query($conexion, $queryDetalles);
            $productos = [];
            
            if ($resultadoDetalles && mysqli_num_rows($resultadoDetalles) > 0) {
                while ($fila = mysqli_fetch_assoc($resultadoDetalles)) {
                    // Determinar características finales
                    $caracteristicasFinal = '';
                    if (!empty(trim($fila['newCaracteristic']))) {
                        $caracteristicasFinal = $fila['newCaracteristic'];
                    } elseif (!empty($fila['caracteristicas_original'])) {
                        $caracteristicasFinal = $fila['caracteristicas_original'];
                    } else {
                        $caracteristicasFinal = 'Sin características';
                    }
                    
                    // Determinar detalle final
                    $detalleFinal = '';
                    if (!empty(trim($fila['newDetail']))) {
                        $detalleFinal = $fila['newDetail'];
                    } elseif (!empty($fila['detalle_producto'])) {
                        $detalleFinal = $fila['detalle_producto'];
                    } else {
                        $detalleFinal = 'Sin descripción';
                    }
                    
                    $productos[] = [
                        'id' => $fila['id'],
                        'codigo' => $fila['codigo'],
                        'nombre' => $fila['nombre'],
                        'id_detalle' => $fila['id_detalle'],
                        'detalle_final' => $detalleFinal,
                        'caracteristicas_final' => $caracteristicasFinal,
                        'precio_venta' => $fila['precio_venta'],
                        'cantidad' => $fila['cantidad'],
                        'sub_total' => $fila['sub_total'],
                        'medida' => $fila['medida'],
                        'precio_unitario' => $fila['precio_unitario'],
                        'ruta_imagen' => $fila['ruta_imagen']
                    ];
                }
            }
            
            // Agregar productos a la venta
            $venta['productos'] = $productos;
            $venta['total_productos'] = count($productos);
        }
        
        echo json_encode([
            'success' => true,
            'cotizacion' => $cotizacion,
            'venta' => $venta,
            'cliente' => $cliente, // Datos del cliente por separado
            'tiene_venta' => !empty($venta),
            'tiene_cliente' => !empty($cliente)
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró la cotización solicitada.'
        ]);
    }
    
    mysqli_close($conexion);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de cotización no proporcionado.'
    ]);
}
?>