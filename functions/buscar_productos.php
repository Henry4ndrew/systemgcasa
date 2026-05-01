<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require '../includes/conexion.php';

$search = $_GET['search'] ?? '';
$response = [
    'success' => false,
    'message' => 'Ingrese un término de búsqueda',
    'productos' => []
];

if (empty($search) || strlen($search) < 2) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$searchTerm = "%" . mysqli_real_escape_string($conexion, $search) . "%";

// Primero obtenemos los productos que coinciden con la búsqueda
// Cambia la primera consulta para que solo traiga productos con detalles
$sql = "SELECT lp.codigo, lp.nombre, lp.categoria, lp.caracteristicas, 
               lp.tienda_virtual, lp.ultima_actualizacion
        FROM lista_productos lp
        INNER JOIN detalle_producto dp ON lp.codigo = dp.codigo
        WHERE lp.codigo LIKE ? OR lp.nombre LIKE ? OR lp.categoria LIKE ?
        GROUP BY lp.codigo
        LIMIT 50";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, 'sss', $searchTerm, $searchTerm, $searchTerm);
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
    $response['message'] = 'No se encontraron productos';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Función reutilizable para obtener detalles
function fetchDataForProducts($conexion, $codigos, $productos) {
    if (empty($codigos)) return $productos;
    
    $listaCodigos = implode(',', $codigos);
    
    // Obtener detalles CON cantidades de ambas tablas
    $sqlDetalles = "SELECT dp.codigo, dp.id_detalle, dp.medida, dp.detalle, dp.precio_unitario,
                           COALESCE(a.cantidad, 0) AS cantidad_disponible,
                           COALESCE(at.cantidad, 0) AS cantidad_tienda,
                           at.fecha_modificacion AS fecha_modificacion_tienda
                    FROM detalle_producto dp
                    LEFT JOIN almacen a ON dp.id_detalle = a.id_detalle
                    LEFT JOIN almacen_tienda at ON dp.id_detalle = at.id_detalle
                    WHERE dp.codigo IN ($listaCodigos)
                    ORDER BY dp.id_detalle";
    
    $result = mysqli_query($conexion, $sqlDetalles);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $codigo = $row['codigo'];
            if (isset($productos[$codigo])) {
                $detalle = [
                    'id_detalle' => (int)$row['id_detalle'],
                    'medida' => $row['medida'],
                    'detalle' => $row['detalle'],
                    'precio_unitario' => $row['precio_unitario'],
                    'cantidad_disponible' => (int)$row['cantidad_disponible'],
                    'cantidad_tienda' => (int)$row['cantidad_tienda']
                ];
                
                // Añadir fecha de modificación de tienda si existe
                if ($row['fecha_modificacion_tienda']) {
                    $detalle['fecha_modificacion_tienda'] = $row['fecha_modificacion_tienda'];
                }
                
                $productos[$codigo]['detalles'][] = $detalle;
            }
        }
        mysqli_free_result($result);
    }
    
    // Obtener imágenes
    $sqlImagenes = "SELECT codigo, id_imagen, ruta_imagen
                    FROM imagenes
                    WHERE codigo IN ($listaCodigos)
                    ORDER BY id_imagen";
    
    $result = mysqli_query($conexion, $sqlImagenes);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $codigo = $row['codigo'];
            if (isset($productos[$codigo])) {
                $productos[$codigo]['imagenes'][] = [
                    'id_imagen' => (int)$row['id_imagen'],
                    'ruta_imagen' => $row['ruta_imagen']
                ];
            }
        }
        mysqli_free_result($result);
    }
    
    return $productos;
}

// Obtener detalles e imágenes para los productos encontrados
$productos = fetchDataForProducts($conexion, $codigos, $productos);
$productosArray = array_values($productos);

$response = [
    'success' => true,
    'message' => count($productosArray) . ' productos encontrados',
    'productos' => $productosArray,
    'total' => count($productosArray)
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
exit;
?>