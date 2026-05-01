<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require '../includes/conexion.php';

$codigoBarra = $_GET['codigo_barra'] ?? '';
$response = [
    'success' => false,
    'message' => 'Código de barra no proporcionado',
    'producto' => null
];

if (empty($codigoBarra)) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Buscar el detalle_producto por codigo_detalle (código de barra)
$sql = "SELECT dp.codigo, dp.id_detalle, dp.medida, dp.detalle, dp.precio_unitario, dp.codigo_detalle,
               lp.nombre, lp.categoria, lp.caracteristicas, lp.tienda_virtual, lp.ultima_actualizacion,
               COALESCE(a.cantidad, 0) AS cantidad_disponible,
               COALESCE(at.cantidad, 0) AS cantidad_tienda,
               (SELECT i.ruta_imagen FROM imagenes i WHERE i.codigo = lp.codigo ORDER BY i.id_imagen ASC LIMIT 1) AS ruta_imagen
        FROM detalle_producto dp
        INNER JOIN lista_productos lp ON dp.codigo = lp.codigo
        LEFT JOIN almacen a ON dp.id_detalle = a.id_detalle
        LEFT JOIN almacen_tienda at ON dp.id_detalle = at.id_detalle
        WHERE dp.codigo_detalle = ?
        LIMIT 1";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, 's', $codigoBarra);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // También traer todas las imágenes del producto
    $imagenes = [];
    $sqlImg = "SELECT id_imagen, ruta_imagen FROM imagenes WHERE codigo = ? ORDER BY id_imagen";
    $stmtImg = mysqli_prepare($conexion, $sqlImg);
    mysqli_stmt_bind_param($stmtImg, 's', $row['codigo']);
    mysqli_stmt_execute($stmtImg);
    $resImg = mysqli_stmt_get_result($stmtImg);
    while ($img = mysqli_fetch_assoc($resImg)) {
        $imagenes[] = [
            'id_imagen' => (int)$img['id_imagen'],
            'ruta_imagen' => $img['ruta_imagen']
        ];
    }
    mysqli_stmt_close($stmtImg);

    // Traer todos los detalles del mismo producto
    $detalles = [];
    $sqlDet = "SELECT dp2.id_detalle, dp2.medida, dp2.detalle, dp2.precio_unitario, dp2.codigo_detalle,
                      COALESCE(a2.cantidad, 0) AS cantidad_disponible,
                      COALESCE(at2.cantidad, 0) AS cantidad_tienda
               FROM detalle_producto dp2
               LEFT JOIN almacen a2 ON dp2.id_detalle = a2.id_detalle
               LEFT JOIN almacen_tienda at2 ON dp2.id_detalle = at2.id_detalle
               WHERE dp2.codigo = ?
               ORDER BY dp2.id_detalle";
    $stmtDet = mysqli_prepare($conexion, $sqlDet);
    mysqli_stmt_bind_param($stmtDet, 's', $row['codigo']);
    mysqli_stmt_execute($stmtDet);
    $resDet = mysqli_stmt_get_result($stmtDet);
    while ($det = mysqli_fetch_assoc($resDet)) {
        $detalles[] = [
            'id_detalle' => (int)$det['id_detalle'],
            'medida' => $det['medida'],
            'detalle' => $det['detalle'],
            'precio_unitario' => $det['precio_unitario'],
            'codigo_detalle' => $det['codigo_detalle'],
            'cantidad_disponible' => (int)$det['cantidad_disponible'],
            'cantidad_tienda' => (int)$det['cantidad_tienda']
        ];
    }
    mysqli_stmt_close($stmtDet);

    $producto = [
        'codigo' => $row['codigo'],
        'nombre' => $row['nombre'],
        'categoria' => $row['categoria'],
        'caracteristicas' => $row['caracteristicas'],
        'tienda_virtual' => $row['tienda_virtual'],
        'ultima_actualizacion' => $row['ultima_actualizacion'],
        'detalles' => $detalles,
        'imagenes' => $imagenes,
        'detalle_encontrado' => [
            'id_detalle' => (int)$row['id_detalle'],
            'medida' => $row['medida'],
            'detalle' => $row['detalle'],
            'precio_unitario' => $row['precio_unitario'],
            'codigo_detalle' => $row['codigo_detalle'],
            'cantidad_disponible' => (int)$row['cantidad_disponible'],
            'cantidad_tienda' => (int)$row['cantidad_tienda'],
            'ruta_imagen' => $row['ruta_imagen']
        ]
    ];

    $response = [
        'success' => true,
        'message' => 'Producto encontrado',
        'producto' => $producto
    ];
} else {
    $response['message'] = 'No se encontró ningún producto con el código de barra: ' . $codigoBarra;
}

mysqli_stmt_close($stmt);
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
exit;
?>
