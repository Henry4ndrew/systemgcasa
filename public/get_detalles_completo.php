
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
    
    // Obtener información del producto
    $queryProducto = "SELECT codigo, nombre, caracteristicas FROM lista_productos WHERE codigo = '$codigo'";
    $resultProducto = mysqli_query($conexion, $queryProducto);
    
    if (!$resultProducto) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al buscar producto: ' . mysqli_error($conexion)
        ]);
        exit;
    }
    
    $producto = mysqli_fetch_assoc($resultProducto);
    
    if ($producto) {
        // Obtener todas las imágenes del producto
        $queryImagenes = "SELECT id_imagen, ruta_imagen FROM imagenes WHERE codigo = '$codigo' ORDER BY id_imagen ASC";
        $resultImagenes = mysqli_query($conexion, $queryImagenes);
        
        $imagenes = [];
        while ($row = mysqli_fetch_assoc($resultImagenes)) {
            $imagenes[] = $row;
        }
        
        // Obtener detalles del producto
        $queryDetalles = "SELECT id_detalle, codigo, medida, detalle, precio_unitario 
                          FROM detalle_producto 
                          WHERE codigo = '$codigo' 
                          ORDER BY medida ASC";
        
        $resultDetalles = mysqli_query($conexion, $queryDetalles);
        
        if (!$resultDetalles) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar detalles: ' . mysqli_error($conexion)
            ]);
            exit;
        }
        
        $detalles = [];
        while ($row = mysqli_fetch_assoc($resultDetalles)) {
            $detalles[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'producto' => $producto,
            'imagenes' => $imagenes,
            'detalles' => $detalles
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Producto no encontrado con código: ' . $codigo
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida. Se requiere método POST y parámetro codigo.'
    ]);
}
?>