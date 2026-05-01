<?php
require '../includes/conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$search = $_GET['search'] ?? '';

$response = [
    'success' => false,
    'message' => 'Ingrese un término de búsqueda',
    'materiales' => []
];

if (empty($search) || strlen($search) < 2) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$searchTerm = "%" . mysqli_real_escape_string($conexion, $search) . "%";

$query = "
    SELECT DISTINCT
        mp.id_material,
        mp.codigo_material,
        mp.nombre_material,
        mp.tipo_medida,
        mp.medida1,
        mp.nombre_medida1,
        mp.medida2,
        mp.nombre_medida2,
        mp.ruta_imagen,
        am.cantidad
    FROM materia_prima mp
    INNER JOIN almacen_materiales am 
        ON mp.id_material = am.id_material
    WHERE 
        mp.nombre_material LIKE '$searchTerm'
        OR mp.codigo_material LIKE '$searchTerm'
    ORDER BY mp.nombre_material
";

$resultado = mysqli_query($conexion, $query);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $materiales = [];
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $materiales[] = [
            'id_material' => $fila['id_material'],
            'codigo' => $fila['codigo_material'],
            'nombre_material' => $fila['nombre_material'],
            'tipo_medida' => $fila['tipo_medida'],
            'medida1' => $fila['medida1'],
            'nombre_medida1' => $fila['nombre_medida1'],
            'medida2' => $fila['medida2'],
            'nombre_medida2' => $fila['nombre_medida2'],
            'ruta_imagen' => $fila['ruta_imagen'],
            'cantidad' => $fila['cantidad']
        ];
    }

    echo json_encode([
        'success' => true,
        'materiales' => $materiales,
        'total' => count($materiales)
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontraron materiales en el almacén.',
        'materiales' => [],
        'total' => 0
    ], JSON_UNESCAPED_UNICODE);
}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>