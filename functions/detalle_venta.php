<?php
require '../includes/conexion.php';

$id_venta = $_GET['id'];

$sql = "SELECT 
    v.*,
    c.nombre as cliente_nombre,
    c.nit,
    c.celular,
    c.empresa,
    u.nombre_usuario as vendedor
FROM ventas v
LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN usuarios u ON v.id_user = u.id_user
WHERE v.id_venta = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

// Detalle de productos
$sql_detalle = "SELECT 
    dv.*,
    lp.nombre,
    lp.categoria,
    dp.medida,
    dp.detalle
FROM detalle_venta dv
JOIN lista_productos lp ON dv.codigo = lp.codigo
JOIN detalle_producto dp ON dv.id_detalle = dp.id_detalle
WHERE dv.id_venta = ?";

$stmt2 = $conexion->prepare($sql_detalle);
$stmt2->bind_param("i", $id_venta);
$stmt2->execute();
$detalles = $stmt2->get_result();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h5>Información de la Venta</h5>
            <table class="table table-sm">
                <tr>
                    <th>Venta ID:</th>
                    <td>#<?= str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <th>Fecha:</th>
                    <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                </tr>
                <tr>
                    <th>Cliente:</th>
                    <td><?= htmlspecialchars($venta['cliente_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Vendedor:</th>
                    <td><?= htmlspecialchars($venta['vendedor']) ?></td>
                </tr>
                <tr>
                    <th>Lugar:</th>
                    <td><?= htmlspecialchars($venta['lugar_venta']) ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h5>Información del Cliente</h5>
            <table class="table table-sm">
                <tr>
                    <th>Empresa:</th>
                    <td><?= htmlspecialchars($venta['empresa'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>NIT:</th>
                    <td><?= htmlspecialchars($venta['nit'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Celular:</th>
                    <td><?= htmlspecialchars($venta['celular'] ?? 'N/A') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h5>Productos Vendidos</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Medida</th>
                            <th>Precio Unit.</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php while($detalle = $detalles->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($detalle['nombre']) ?></td>
                                <td><?= htmlspecialchars($detalle['categoria']) ?></td>
                                <td><?= htmlspecialchars($detalle['medida']) ?></td>
                                <td>$<?= number_format($detalle['precio_venta'], 2) ?></td>
                                <td><?= $detalle['cantidad'] ?></td>
                                <td>$<?= number_format($detalle['sub_total'], 2) ?></td>
                            </tr>
                            <?php $total += $detalle['sub_total']; ?>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                            <td class="fw-bold">$<?= number_format($total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>