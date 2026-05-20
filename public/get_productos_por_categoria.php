<?php
header('Content-Type: application/json');
require '../includes/conexion.php';

if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoria'])) {
    $categoria = mysqli_real_escape_string($conexion, $_POST['categoria']);
    
    $query = "SELECT codigo, nombre, categoria, caracteristicas, tienda_virtual 
              FROM lista_productos 
              WHERE categoria = '$categoria' AND tienda_virtual = 'si' 
              ORDER BY nombre ASC";
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta: ' . mysqli_error($conexion)
        ]);
        exit;
    }
    
    $productos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Obtener la primera imagen del producto
        $img_query = "SELECT ruta_imagen FROM imagenes WHERE codigo = '{$row['codigo']}' LIMIT 1";
        $img_result = mysqli_query($conexion, $img_query);
        $row['imagen'] = ($img_result && mysqli_num_rows($img_result) > 0) ? mysqli_fetch_assoc($img_result)['ruta_imagen'] : null;
        $productos[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida'
    ]);
}
?>