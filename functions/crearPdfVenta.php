<?php
session_start();
ob_start();

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

define('K_PATH_MAIN', dirname(__FILE__) . '/../tcpdf/');
define('K_PATH_URL', '');
define('K_PATH_IMAGES', '');
define('K_BLANK_IMAGE', '_blank.png');
define('PDF_IMAGE_SCALE_RATIO', 1.25);
define('K_CELL_HEIGHT_RATIO', 1.25);
define('K_TCPDF_THROW_EXCEPTION_ON_ERROR', false);
define('K_TCPDF_EXTERNAL_CONFIG', true);

$temp_dir = sys_get_temp_dir() . '/tcpdf_cache_' . session_id() . '/';
define('K_PATH_CACHE', $temp_dir);
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0755, true);
}

require '../includes/conexion.php';
require '../tcpdf/tcpdf.php';

$idVenta = null;
if (isset($_GET['id_venta']) && is_numeric($_GET['id_venta'])) {
    $idVenta = (int) $_GET['id_venta'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idVenta = (int) $_GET['id'];
}

if (!$idVenta) {
    die('ID de venta no proporcionado.');
}

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
    die('No se pudo preparar la consulta de venta.');
}

$stmtVenta->bind_param('i', $idVenta);
$stmtVenta->execute();
$resultVenta = $stmtVenta->get_result();
$venta = $resultVenta->fetch_assoc();
$stmtVenta->close();

if (!$venta) {
    die('No se encontro la venta solicitada.');
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
    die('No se pudo preparar la consulta de productos.');
}

$stmtItems->bind_param('i', $idVenta);
$stmtItems->execute();
$resultItems = $stmtItems->get_result();

$items = [];
while ($row = $resultItems->fetch_assoc()) {
    $cantidad = (int) $row['cantidad'];
    $precio = (float) $row['precio_venta'];
    $items[] = [
        'nombre' => (string) $row['nombre'],
        'cantidad' => $cantidad,
        'precio' => $precio,
        'subtotal' => $cantidad * $precio
    ];
}
$stmtItems->close();
mysqli_close($conexion);

if (count($items) === 0) {
    die('La venta no tiene productos para imprimir.');
}

$customerName = !empty($venta['customer_name']) ? (string) $venta['customer_name'] : 'Cliente sin nombre';
$customerCard = !empty($venta['carnet_ci']) ? (string) $venta['carnet_ci'] : (!empty($venta['nit']) ? (string) $venta['nit'] : 'S/N');

$pdf = new TCPDF('P', 'mm', [80, 297], true, 'UTF-8', false);
$pdf->SetCreator('Sistema de Ventas');
$pdf->SetAuthor('GCasa Club');
$pdf->SetTitle('Venta ' . $idVenta);
$pdf->SetSubject('Comprobante de Pedido');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(true, 3);
$pdf->AddPage();
$pdf->SetFont('courier', '', 10);

$logoJpgPath = realpath(__DIR__ . '/../img/logo-gcasaclub-ticket.jpg');
$logoPngPath = realpath(__DIR__ . '/../img/logo-gcasaclub.png');
$logoHtml = '';
if ($logoJpgPath && is_file($logoJpgPath)) {
    $logoSrc = str_replace('\\', '/', $logoJpgPath);
    $logoHtml = '<div style="text-align:center;"><img src="' . htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8') . '" width="75" /></div>';
} else {
    // TCPDF needs GD/Imagick for PNG with alpha channel.
    $canRenderPngWithAlpha = extension_loaded('gd') || extension_loaded('imagick');
    if ($logoPngPath && is_file($logoPngPath) && $canRenderPngWithAlpha) {
        $logoSrc = str_replace('\\', '/', $logoPngPath);
        $logoHtml = '<div style="text-align:center;"><img src="' . htmlspecialchars($logoSrc, ENT_QUOTES, 'UTF-8') . '" width="75" /></div>';
    }
}

$fechaVenta = date('d/m/Y H:i', strtotime($venta['fecha_venta']));
$totalVenta = number_format((float) $venta['total_venta'], 2);

$rowsHtml = '';
foreach ($items as $item) {
    $rowsHtml .= '<tr>'
        . '<td width="44%" style="font-size:10px;font-weight:bold;">' . htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td width="14%" align="right" style="font-size:10px;font-weight:bold;">' . $item['cantidad'] . '</td>'
        . '<td width="21%" align="right" style="font-size:10px;font-weight:bold;">' . number_format($item['precio'], 2) . '</td>'
        . '<td width="21%" align="right" style="font-size:10px;font-weight:bold;">' . number_format($item['subtotal'], 2) . '</td>'
        . '</tr>';
}

$html = '
<div style="font-family:courier; font-size:10px;">
    ' . $logoHtml . '
    <div style="text-align:center; font-size:16px; font-weight:bold; letter-spacing:1px;">GCASACLUB</div>
    <div style="text-align:center; font-size:10px; font-weight:bold; text-transform:uppercase;">Comprobante de Pedido</div>

    <div style="border-top:1px dashed #000; margin:4px 0;"></div>

    <table cellpadding="1">
        <tr>
            <td width="28%" style="font-weight:bold;">Pedido:</td>
            <td width="72%" align="right" style="font-weight:bold;">' . $idVenta . '</td>
        </tr>
        <tr>
            <td width="28%" style="font-weight:bold;">Fecha:</td>
            <td width="72%" align="right" style="font-weight:bold;">' . htmlspecialchars($fechaVenta, ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
        <tr>
            <td width="28%" style="font-weight:bold;">Cliente:</td>
            <td width="72%" align="right" style="font-weight:bold;">' . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
        <tr>
            <td width="28%" style="font-weight:bold;">CI:</td>
            <td width="72%" align="right" style="font-weight:bold;">' . htmlspecialchars($customerCard, ENT_QUOTES, 'UTF-8') . '</td>
        </tr>
    </table>

    <div style="border-top:1px dashed #000; margin:4px 0;"></div>

    <table cellpadding="2">
        <thead>
            <tr>
                <th width="44%" align="left" style="font-size:10px; font-weight:bold; border-bottom:1px solid #000;">Producto</th>
                <th width="14%" align="right" style="font-size:10px; font-weight:bold; border-bottom:1px solid #000;">Cant</th>
                <th width="21%" align="right" style="font-size:10px; font-weight:bold; border-bottom:1px solid #000;">P/U</th>
                <th width="21%" align="right" style="font-size:10px; font-weight:bold; border-bottom:1px solid #000;">Sub</th>
            </tr>
        </thead>
        <tbody>
            ' . $rowsHtml . '
        </tbody>
    </table>

    <div style="border-top:1px dashed #000; margin:4px 0;"></div>

    <table cellpadding="1">
        <tr>
            <td width="45%" style="font-size:12px; font-weight:bold;">TOTAL</td>
            <td width="55%" align="right" style="font-size:12px; font-weight:bold;">Bs ' . $totalVenta . '</td>
        </tr>
    </table>

    <div style="margin-top:4px; font-size:9px; font-weight:bold; line-height:1.3;">
        <div><b>Email:</b> gcasaclubgerencia@gmail.com</div>
        <div><b>Telefono:</b> 76968777</div>
        <div><b>Direccion:</b> Calle Cesar Adriazola, Av. Cuarta innominada</div>
        <div>Barrio industrial, km 4 1/2 Av. Blanco Galindo</div>
    </div>

    <div style="text-align:center; margin-top:6px; font-weight:bold;">Gracias por su compra</div>
</div>';

$pdf->writeHTML($html, true, false, true, false, '');

while (ob_get_level()) {
    ob_end_clean();
}

$pdf->Output('Venta_' . $idVenta . '.pdf', 'I');

if (is_dir($temp_dir)) {
    $files = glob($temp_dir . '*');
    if ($files) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
    @rmdir($temp_dir);
}
exit;
