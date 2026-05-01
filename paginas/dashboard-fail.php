<?php
require 'includes/conexion.php';
include 'includes/permisos.php';

// Fechas por defecto (últimos 30 días)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'));
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

// Filtros
$filtro_cliente = isset($_GET['cliente']) && $_GET['cliente'] != '' ? $_GET['cliente'] : null;
$filtro_vendedor = isset($_GET['vendedor']) && $_GET['vendedor'] != '' ? $_GET['vendedor'] : null;
$filtro_lugar = isset($_GET['lugar']) && $_GET['lugar'] != '' ? $_GET['lugar'] : null;
$filtro_categoria = isset($_GET['categoria']) && $_GET['categoria'] != '' ? $_GET['categoria'] : null;

// Consulta principal de ventas con filtros
$sql_ventas = "SELECT 
    v.id_venta,
    v.fecha_venta,
    v.total_venta,
    v.lugar_venta,
    c.nombre as cliente_nombre,
    u.nombre_usuario as vendedor,
    c.departamento,
    c.empresa
FROM ventas v
LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN usuarios u ON v.id_user = u.id_user
WHERE v.fecha_venta BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

// Aplicar filtros dinámicos
if ($filtro_cliente) {
    $sql_ventas .= " AND (c.nombre LIKE ? OR c.id_cliente = ?)";
    $params[] = "%$filtro_cliente%";
    $params[] = $filtro_cliente;
    $types .= "ss";
}

if ($filtro_vendedor) {
    $sql_ventas .= " AND u.id_user = ?";
    $params[] = $filtro_vendedor;
    $types .= "s";
}

if ($filtro_lugar) {
    $sql_ventas .= " AND v.lugar_venta = ?";
    $params[] = $filtro_lugar;
    $types .= "s";
}

$sql_ventas .= " ORDER BY v.fecha_venta DESC, v.id_venta DESC";

// Preparar consulta
$stmt = $conexion->prepare($sql_ventas);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result_ventas = $stmt->get_result();
$ventas = [];

if ($result_ventas->num_rows > 0) {
    while ($row = $result_ventas->fetch_assoc()) {
        $ventas[] = $row;
    }
}

// Estadísticas generales
$sql_estadisticas = "SELECT 
    COUNT(*) as total_ventas,
    SUM(total_venta) as total_ingresos,
    AVG(total_venta) as promedio_venta,
    MIN(fecha_venta) as primera_venta,
    MAX(fecha_venta) as ultima_venta
FROM ventas 
WHERE fecha_venta BETWEEN ? AND ?";

$stmt2 = $conexion->prepare($sql_estadisticas);
$stmt2->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt2->execute();
$estadisticas = $stmt2->get_result()->fetch_assoc();

// Productos más vendidos
$sql_productos_populares = "SELECT 
    lp.nombre,
    lp.categoria,
    SUM(dv.cantidad) as total_vendido,
    SUM(dv.sub_total) as total_ingresos,
    AVG(dp.precio_unitario) as precio_promedio
FROM detalle_venta dv
JOIN lista_productos lp ON dv.codigo = lp.codigo
JOIN detalle_producto dp ON dv.id_detalle = dp.id_detalle
JOIN ventas v ON dv.id_venta = v.id_venta
WHERE v.fecha_venta BETWEEN ? AND ?
GROUP BY lp.codigo
ORDER BY total_vendido DESC
LIMIT 10";

$stmt3 = $conexion->prepare($sql_productos_populares);
$stmt3->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt3->execute();
$productos_populares = $stmt3->get_result();
$top_productos = [];

if ($productos_populares->num_rows > 0) {
    while ($row = $productos_populares->fetch_assoc()) {
        $top_productos[] = $row;
    }
}

// Ventas por vendedor
$sql_vendedores = "SELECT 
    u.nombre_usuario,
    COUNT(v.id_venta) as ventas_realizadas,
    SUM(v.total_venta) as total_vendido,
    AVG(v.total_venta) as promedio_venta
FROM ventas v
JOIN usuarios u ON v.id_user = u.id_user
WHERE v.fecha_venta BETWEEN ? AND ?
GROUP BY u.id_user
ORDER BY total_vendido DESC";

$stmt4 = $conexion->prepare($sql_vendedores);
$stmt4->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt4->execute();
$result_vendedores = $stmt4->get_result();
$ventas_por_vendedor = [];

if ($result_vendedores->num_rows > 0) {
    while ($row = $result_vendedores->fetch_assoc()) {
        $ventas_por_vendedor[] = $row;
    }
}

// Obtener lista de filtros para los select
$sql_usuarios = "SELECT id_user, nombre_usuario FROM usuarios WHERE estado = 'activo' ORDER BY nombre_usuario";
$usuarios = $conexion->query($sql_usuarios)->fetch_all(MYSQLI_ASSOC);

$sql_lugares = "SELECT DISTINCT lugar_venta FROM ventas WHERE lugar_venta IS NOT NULL ORDER BY lugar_venta";
$lugares = $conexion->query($sql_lugares)->fetch_all(MYSQLI_ASSOC);

$sql_categorias = "SELECT DISTINCT categoria FROM lista_productos WHERE categoria IS NOT NULL ORDER BY categoria";
$categorias = $conexion->query($sql_categorias)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ventas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-stat {
            border-left: 4px solid #0d6efd;
            transition: transform 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .badge-lugar {
            font-size: 0.8em;
            padding: 4px 8px;
        }
        .filtros-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row my-4">
            <div class="col">
                <h1 class="display-5">
                    <i class="fas fa-chart-line text-primary"></i> Dashboard de Ventas
                </h1>
                <p class="text-muted">Análisis y reportes de ventas del sistema</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-container">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" name="fecha_inicio" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" name="fecha_fin" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cliente</label>
                    <input type="text" class="form-control" name="cliente" placeholder="Nombre o ID" value="<?= $filtro_cliente ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Vendedor</label>
                    <select class="form-select" name="vendedor">
                        <option value="">Todos</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id_user'] ?>" <?= $filtro_vendedor == $usuario['id_user'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usuario['nombre_usuario']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lugar</label>
                    <select class="form-select" name="lugar">
                        <option value="">Todos</option>
                        <?php foreach ($lugares as $lugar): ?>
                            <option value="<?= $lugar['lugar_venta'] ?>" <?= $filtro_lugar == $lugar['lugar_venta'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lugar['lugar_venta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    <a href="dashboard_ventas.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Cards de Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted">Total Ventas</h6>
                                <h3 class="mb-0"><?= number_format($estadisticas['total_ventas']) ?></h3>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-3">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted">Ingresos Totales</h6>
                                <h3 class="mb-0">$<?= number_format($estadisticas['total_ingresos'], 2) ?></h3>
                            </div>
                            <div class="bg-success text-white rounded-circle p-3">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted">Promedio por Venta</h6>
                                <h3 class="mb-0">$<?= number_format($estadisticas['promedio_venta'], 2) ?></h3>
                            </div>
                            <div class="bg-info text-white rounded-circle p-3">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted">Período</h6>
                                <h5 class="mb-0">
                                    <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?>
                                </h5>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-3">
                                <i class="fas fa-calendar-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos y Tablas -->
        <div class="row">
            <!-- Tabla de Ventas Recientes -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Ventas Recientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Vendedor</th>
                                        <th>Lugar</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($ventas) > 0): ?>
                                        <?php foreach ($ventas as $venta): ?>
                                            <tr>
                                                <td>#<?= str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT) ?></td>
                                                <td><?= date('d/m/Y', strtotime($venta['fecha_venta'])) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($venta['cliente_nombre']) ?>
                                                    <?php if ($venta['empresa']): ?>
                                                        <br><small class="text-muted"><?= $venta['empresa'] ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($venta['vendedor']) ?></td>
                                                <td>
                                                    <span class="badge bg-secondary badge-lugar">
                                                        <?= htmlspecialchars($venta['lugar_venta']) ?>
                                                    </span>
                                                </td>
                                                <td class="fw-bold">$<?= number_format($venta['total_venta'], 2) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="verDetalleVenta(<?= $venta['id_venta'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                                <p>No hay ventas registradas en el período seleccionado</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Productos y Vendedores -->
            <div class="col-lg-4">
                <!-- Top Productos -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Productos Más Vendidos</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (count($top_productos) > 0): ?>
                                <?php foreach ($top_productos as $index => $producto): ?>
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <?= $index + 1 ?>. <?= htmlspecialchars($producto['nombre']) ?>
                                            </h6>
                                            <small class="text-success"><?= $producto['total_vendido'] ?> unidades</small>
                                        </div>
                                        <small class="text-muted">
                                            Categoría: <?= $producto['categoria'] ?> | 
                                            Total: $<?= number_format($producto['total_ingresos'], 2) ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">No hay datos de productos</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Top Vendedores -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-trophy"></i> Top Vendedores</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (count($ventas_por_vendedor) > 0): ?>
                                <?php foreach ($ventas_por_vendedor as $index => $vendedor): ?>
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                <?= $index + 1 ?>. <?= htmlspecialchars($vendedor['nombre_usuario']) ?>
                                            </h6>
                                            <small class="text-primary">$<?= number_format($vendedor['total_vendido'], 2) ?></small>
                                        </div>
                                        <small class="text-muted">
                                            Ventas: <?= $vendedor['ventas_realizadas'] ?> | 
                                            Promedio: $<?= number_format($vendedor['promedio_venta'], 2) ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">No hay datos de vendedores</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Gráficos -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Ventas por Lugar</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartLugares"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Ventas por Día</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="chartVentasDia"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exportar Datos -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-file-export"></i> Exportar Reporte</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="exportar_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="exportar_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </a>
                            <button onclick="imprimirReporte()" class="btn btn-secondary">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalle de Venta -->
    <div class="modal fade" id="modalDetalleVenta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de Venta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleVentaContent">
                    <!-- Cargado por AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para ver detalle de venta
        function verDetalleVenta(idVenta) {
            fetch(`functions/detalle_venta.php?id=${idVenta}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('detalleVentaContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalDetalleVenta')).show();
                });
        }

        // Gráfico de Ventas por Lugar
        <?php
        // Consulta para gráfico de lugares
        $sql_lugares_chart = "SELECT 
            lugar_venta,
            COUNT(*) as cantidad,
            SUM(total_venta) as total
        FROM ventas 
        WHERE fecha_venta BETWEEN ? AND ?
        GROUP BY lugar_venta";
        
        $stmt5 = $conexion->prepare($sql_lugares_chart);
        $stmt5->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt5->execute();
        $result_lugares_chart = $stmt5->get_result();
        $lugares_chart = [];
        $total_lugares = [];
        $cantidad_lugares = [];
        
        while($row = $result_lugares_chart->fetch_assoc()) {
            $lugares_chart[] = $row['lugar_venta'];
            $total_lugares[] = $row['total'];
            $cantidad_lugares[] = $row['cantidad'];
        }
        ?>

        const ctxLugares = document.getElementById('chartLugares').getContext('2d');
        new Chart(ctxLugares, {
            type: 'pie',
            data: {
                labels: <?= json_encode($lugares_chart) ?>,
                datasets: [{
                    data: <?= json_encode($total_lugares) ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#8AC926', '#1982C4'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Gráfico de Ventas por Día
        <?php
        // Consulta para ventas por día
        $sql_ventas_dia = "SELECT 
            DATE(fecha_venta) as dia,
            COUNT(*) as cantidad,
            SUM(total_venta) as total
        FROM ventas 
        WHERE fecha_venta BETWEEN ? AND ?
        GROUP BY DATE(fecha_venta)
        ORDER BY dia";
        
        $stmt6 = $conexion->prepare($sql_ventas_dia);
        $stmt6->bind_param("ss", $fecha_inicio, $fecha_fin);
        $stmt6->execute();
        $result_ventas_dia = $stmt6->get_result();
        $dias = [];
        $ventas_dia = [];
        
        while($row = $result_ventas_dia->fetch_assoc()) {
            $dias[] = date('d/m', strtotime($row['dia']));
            $ventas_dia[] = $row['total'];
        }
        ?>

        const ctxVentasDia = document.getElementById('chartVentasDia').getContext('2d');
        new Chart(ctxVentasDia, {
            type: 'line',
            data: {
                labels: <?= json_encode($dias) ?>,
                datasets: [{
                    label: 'Ventas por Día',
                    data: <?= json_encode($ventas_dia) ?>,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Función para imprimir
        function imprimirReporte() {
            window.print();
        }

        // Auto-refresh cada 5 minutos
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 300000 ms = 5 minutos
    </script>
</body>
</html>