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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
    
    // Obtener la primera imagen del producto
    $query = "SELECT ruta_imagen FROM imagenes WHERE codigo = '$codigo' LIMIT 1";
    $result = mysqli_query($conexion, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $ruta = $row['ruta_imagen'];
        
        // Ajustar ruta
        if (!empty($ruta)) {
            if (strpos($ruta, '../') !== 0 && strpos($ruta, 'http') !== 0) {
                $ruta = '../' . $ruta;
            }
        }
        
        echo json_encode([
            'success' => true,
            'imagen' => $ruta
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró imagen'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida'
    ]);
}
?>