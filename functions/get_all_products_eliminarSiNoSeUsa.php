<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

set_time_limit(30);
ini_set('memory_limit', '256M');

require '../includes/conexion.php';

$limit = min((int)($_GET['limit'] ?? 50), 100);
$page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$response = [
    'success' => false,
    'message' => 'Sin resultados',
    'productos' => [],
    'total' => 0
];

$sql = "SELECT codigo, nombre, categoria, caracteristicas, tienda_virtual, ultima_actualizacion
        FROM lista_productos
        ORDER BY codigo
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$productos = [];
$codigos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $cod = $row['codigo'];

    $codigos[] = "'" . mysqli_real_escape_string($conexion, $cod) . "'";

    $productos[$cod] = [
        'codigo' => $cod,
        'nombre' => $row['nombre'],
        'categoria' => $row['categoria'],
        'caracteristicas' => $row['caracteristicas'],
        'tienda_virtual' => $row['tienda_virtual'],
        'ultima_actualizacion' => $row['ultima_actualizacion'],
        'detalles' => [],
        'imagenes' => []
    ];
}

mysqli_stmt_close($stmt);
if (empty($codigos)) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$listaCodigos = implode(',', $codigos);

function fetchByCodigos($conexion, $query, $productos, $callback) {
    $result = mysqli_query($conexion, $query);
    if (!$result) return $productos;

    while ($row = mysqli_fetch_assoc($result)) {
        $codigo = $row['codigo'];
        if (isset($productos[$codigo])) {
            $callback($productos[$codigo], $row);
        }
    }
    mysqli_free_result($result);

    return $productos;
}


$sqlDetalles = "SELECT dp.codigo, dp.id_detalle, dp.medida, dp.detalle, dp.precio_unitario,
                       COALESCE(a.cantidad, 0) AS cantidad_disponible
                FROM detalle_producto dp
                LEFT JOIN almacen a ON dp.id_detalle = a.id_detalle
                WHERE dp.codigo IN ($listaCodigos)
                ORDER BY dp.id_detalle";

$productos = fetchByCodigos($conexion, $sqlDetalles, $productos, function (&$producto, $row) {
    $producto['detalles'][] = [
        'id_detalle' => (int)$row['id_detalle'],
        'medida' => $row['medida'],
        'detalle' => $row['detalle'],
        'precio_unitario' => $row['precio_unitario'],
        'cantidad_disponible' => (int)$row['cantidad_disponible']
    ];
});

$sqlImagenes = "SELECT codigo, id_imagen, ruta_imagen
                FROM imagenes
                WHERE codigo IN ($listaCodigos)
                ORDER BY id_imagen";

$productos = fetchByCodigos($conexion, $sqlImagenes, $productos, function (&$producto, $row) {
    $producto['imagenes'][] = [
        'id_imagen' => (int)$row['id_imagen'],
        'ruta_imagen' => $row['ruta_imagen']
    ];
});

$productosArray = array_values($productos);

$response = [
    'success' => true,
    'message' => count($productosArray) . ' productos encontrados',
    'productos' => $productosArray,
    'total' => count($productosArray),
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'has_more' => count($productosArray) === $limit
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
exit;
?>
