<?php
require '../includes/conexion.php';

// Forzar respuesta JSON
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['id_cliente'])) {
    $idCliente = intval($_POST['id_cliente']); 

    $query = "SELECT id_cliente, nombre, nit, carnet_ci, departamento, celular, cel_empresa, correo, empresa, nota, fecha_registro 
              FROM cartera_clientes 
              WHERE id_cliente = $idCliente";

    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $cliente = mysqli_fetch_assoc($resultado);

        echo json_encode([
            'success' => true,
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
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos del cliente.'
        ]);
    }

    mysqli_free_result($resultado);
    mysqli_close($conexion);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibió un ID de cliente válido.'
    ]);
}
?>
