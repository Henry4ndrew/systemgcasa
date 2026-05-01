<?php
require '../includes/conexion.php';
header('Content-Type: application/json; charset=utf-8');
$query = "SELECT id_cliente, nombre, nit, carnet_ci, departamento, celular, 
                 cel_empresa, correo, empresa, nota, fecha_registro
          FROM cartera_clientes
          WHERE estado = 'activo'
          ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);
if ($resultado && mysqli_num_rows($resultado) > 0) {
    $clientes = [];
    
    while ($cliente = mysqli_fetch_assoc($resultado)) {
        $clientes[] = [
            'id_cliente' => $cliente['id_cliente'],
            'nombre' => $cliente['nombre'],
            'nit' => $cliente['nit'],
            'carnet_ci' => $cliente['carnet_ci'],
            'departamento' => $cliente['departamento'],
            'telefono' => $cliente['celular'],
            'cel_empresa' => $cliente['cel_empresa'],
            'correo' => $cliente['correo'],
            'empresa' => $cliente['empresa'],
            'nota' => $cliente['nota'],
            'fecha_registro' => $cliente['fecha_registro']
        ];
    }
    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'total' => count($clientes)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontraron clientes registrados.',
        'clientes' => [],
        'total' => 0
    ]);
}
if (isset($resultado)) {
    mysqli_free_result($resultado);
}
mysqli_close($conexion);
?>