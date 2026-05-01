<?php
require '../includes/conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['id_venta']) || !is_numeric($_POST['id_venta'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de venta no valido.'
    ]);
    exit;
}

$idVenta = (int) $_POST['id_venta'];

$sqlVenta = "
    SELECT
        v.id_venta,
        v.fecha_venta,
        v.total_venta,
        c.nombre AS customer_name,
        c.carnet_ci,
        c.nit
    FROM ventas v
    LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
    WHERE v.id_venta = ?
    LIMIT 1
";

$stmtVenta = $conexion->prepare($sqlVenta);
if (!$stmtVenta) {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo preparar la consulta de venta.'
    ]);
    exit;
}

$stmtVenta->bind_param('i', $idVenta);
$stmtVenta->execute();
$resultVenta = $stmtVenta->get_result();
$venta = $resultVenta->fetch_assoc();
$stmtVenta->close();

if (!$venta) {
    echo json_encode([
        'success' => false,
        'message' => 'Venta no encontrada.'
    ]);
    mysqli_close($conexion);
    exit;
}

$sqlItems = "
    SELECT
        COALESCE(lp.nombre, 'Producto') AS nombre,
        dv.cantidad,
        dv.precio_venta
    FROM detalle_venta dv
    LEFT JOIN lista_productos lp ON dv.codigo = lp.codigo
    WHERE dv.id_venta = ?
";

$stmtItems = $conexion->prepare($sqlItems);
if (!$stmtItems) {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo preparar la consulta de productos.'
    ]);
    mysqli_close($conexion);
    exit;
}

$stmtItems->bind_param('i', $idVenta);
$stmtItems->execute();
$resultItems = $stmtItems->get_result();

$items = [];
while ($row = $resultItems->fetch_assoc()) {
    $items[] = [
        'nombre' => (string) $row['nombre'],
        'cantidad' => (int) $row['cantidad'],
        'precio' => (float) $row['precio_venta']
    ];
}
$stmtItems->close();
mysqli_close($conexion);

if (count($items) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'La venta no tiene productos para imprimir.'
    ]);
    exit;
}

$customerCard = '';
if (!empty($venta['carnet_ci'])) {
    $customerCard = (string) $venta['carnet_ci'];
} elseif (!empty($venta['nit'])) {
    $customerCard = (string) $venta['nit'];
} else {
    $customerCard = 'S/N';
}

$ticket = [
    'orderNumber' => (string) $venta['id_venta'],
    'orderDate' => date('d/m/Y H:i', strtotime($venta['fecha_venta'])),
    'customerName' => !empty($venta['customer_name']) ? (string) $venta['customer_name'] : 'Cliente sin nombre',
    'customerCard' => $customerCard,
    'items' => $items,
    'total' => (float) $venta['total_venta']
];

echo json_encode([
    'success' => true,
    'ticket' => $ticket
]);
