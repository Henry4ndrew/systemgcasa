<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require '../includes/conexion.php';

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
    SELECT 
        id_material,
        codigo_material,
        nombre_material,
        tipo_medida,
        medida1,
        nombre_medida1,
        medida2,
        nombre_medida2,
        ruta_imagen
    FROM materia_prima
    WHERE 
        nombre_material LIKE '$searchTerm'
        OR codigo_material LIKE '$searchTerm'
    ORDER BY nombre_material
";

$resultado = mysqli_query($conexion, $query);

$materiales = [];

if ($resultado && mysqli_num_rows($resultado) > 0) {

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
            'ruta_imagen' => $fila['ruta_imagen']
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
        'message' => 'No se encontraron materiales.',
        'materiales' => [],
        'total' => 0
    ], JSON_UNESCAPED_UNICODE);
}

mysqli_close($conexion);
?>