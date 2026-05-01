<?php
require '../includes/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$query = "SELECT 
            lp.codigo,
            lp.nombre,
            dp.id_detalle,
            dp.medida,
            dp.detalle,
            dp.precio_unitario,

            -- Obtener cantidad del almacén
            (SELECT a.cantidad
             FROM almacen a
             WHERE a.codigo = lp.codigo
               AND a.id_detalle = dp.id_detalle
             LIMIT 1) AS cantidad,

            -- Obtener la primera imagen
            (SELECT i.ruta_imagen 
             FROM imagenes i 
             WHERE i.codigo = lp.codigo 
             ORDER BY i.id_imagen ASC 
             LIMIT 1) AS ruta_imagen

        FROM lista_productos lp
        INNER JOIN detalle_producto dp 
            ON lp.codigo = dp.codigo
        ORDER BY lp.codigo, dp.id_detalle";

$resultado = mysqli_query($conexion, $query);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $productos = [];
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $productos[] = [
            'codigo' => $fila['codigo'],
            'nombre' => $fila['nombre'],
            'id_detalle' => $fila['id_detalle'],
            'medida' => $fila['medida'],
            'detalle' => $fila['detalle'],
            'precio_unitario' => $fila['precio_unitario'],
            'cantidad' => $fila['cantidad'] ?? 0, // si no existe, 0
            'ruta_imagen' => $fila['ruta_imagen']
        ];
    }

    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontraron productos con detalles en la base de datos.',
        'productos' => [],
        'total' => 0
    ]);
}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>
