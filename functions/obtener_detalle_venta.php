<?php
require '../includes/conexion.php';

if (isset($_POST['id_venta'])) {
    $idVenta = $_POST['id_venta'];

    // Obtener total real desde la tabla ventas
    $queryTotal = "SELECT total_venta FROM ventas WHERE id_venta = $idVenta LIMIT 1";
    $resTotal = mysqli_query($conexion, $queryTotal);
    $totalVenta = 0;

    if ($resTotal && mysqli_num_rows($resTotal) > 0) {
        $rowTotal = mysqli_fetch_assoc($resTotal);
        $totalVenta = $rowTotal['total_venta'];
    }

    // Consulta principal con lógica mejorada para detalle_final
    $query = "
        SELECT 
            dv.id,
            dv.id_venta,
            dv.codigo,
            dv.id_detalle,
            dv.newDetail,
            dp.detalle AS detalle_original,

            -- Detalle final
            CASE 
                WHEN dv.newDetail IS NOT NULL 
                    AND TRIM(dv.newDetail) != '' 
                THEN dv.newDetail
                ELSE COALESCE(dp.detalle, 'Sin descripción')
            END AS detalle_final,

            -- Características finales (prioriza detalle_venta)
            CASE 
                WHEN dv.newCaracteristic IS NOT NULL 
                    AND TRIM(dv.newCaracteristic) != '' 
                THEN dv.newCaracteristic
                ELSE COALESCE(lp.caracteristicas, 'Sin características')
            END AS caracteristicas_final,

            dv.precio_venta,
            dv.cantidad,
            dv.sub_total,

            lp.nombre,
            lp.categoria,

            dp.medida,
            dp.precio_unitario,

            v.tipo_descuento,
            v.valor_descuento,

            (SELECT ruta_imagen 
            FROM imagenes 
            WHERE codigo = dv.codigo 
            LIMIT 1) AS ruta_imagen

        FROM detalle_venta dv
        LEFT JOIN lista_productos lp 
            ON dv.codigo = lp.codigo
        LEFT JOIN detalle_producto dp 
            ON dv.id_detalle = dp.id_detalle
        LEFT JOIN ventas v 
            ON dv.id_venta = v.id_venta
        WHERE dv.id_venta = $idVenta
    ";


    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $productos = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            // Asegurar que detalle_final tenga un valor
            if (empty(trim($fila['detalle_final']))) {
                $fila['detalle_final'] = !empty($fila['detalle_original']) ? 
                                         $fila['detalle_original'] : 'Sin descripción';
            }
            
            // Para depuración, también puedes ver ambos valores
            $fila['_debug_newDetail'] = $fila['newDetail'];
            $fila['_debug_detalle_original'] = $fila['detalle_original'];
            
            $productos[] = $fila;
        }

        echo json_encode([
            'success' => true,
            'id_venta' => $idVenta,
            'productos' => $productos,
            'total_venta' => $totalVenta
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron detalles para esta venta.'
        ]);
    }

    mysqli_close($conexion);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de venta no proporcionado.'
    ]);
}
?>