<?php
require 'includes/conexion.php';
include 'includes/permisos.php';

// Configuración inicial
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Últimos 30 días desde hoy (más común para reportes de ventas)
$fecha_fin = date('Y-m-d 23:59:59'); // Hasta hoy
$fecha_inicio = date('Y-m-d 00:00:00', strtotime('-30 days')); // Últimos 30 días

// Opción alternativa: mes en curso (desde el día 1 hasta hoy)
// $fecha_inicio = date('Y-m-01 00:00:00');
// $fecha_fin = date('Y-m-d 23:59:59');

// Consulta para productos más vendidos - CORREGIDA
$sql = "
    SELECT 
        lp.codigo,
        lp.nombre,
        lp.categoria,
        SUM(dv.cantidad) as total_vendido,
        SUM(dv.cantidad * dv.precio_venta) as ingresos_totales,
        COUNT(DISTINCT dv.id_venta) as veces_vendido,
        ROUND(AVG(dv.precio_venta), 2) as precio_promedio
    FROM 
        detalle_venta dv
    INNER JOIN 
        ventas v ON dv.id_venta = v.id_venta
    INNER JOIN 
        lista_productos lp ON dv.codigo = lp.codigo
    WHERE 
        v.fecha_venta BETWEEN ? AND ?
";

// Agregar filtro de categoría si está especificado
if (!empty($categoria)) {
    $sql .= " AND lp.categoria = ?";
}

$sql .= " GROUP BY lp.codigo, lp.nombre, lp.categoria";
$sql .= " ORDER BY total_vendido DESC";
$sql .= " LIMIT ?";

// Preparar consulta
if (!empty($categoria)) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $fecha_inicio, $fecha_fin, $categoria, $limite);
} else {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $fecha_inicio, $fecha_fin, $limite);
}

$stmt->execute();
$result = $stmt->get_result();
$productos_mas_vendidos = [];
$total_unidades = 0;
$total_ingresos = 0;

// Obtener productos y calcular totales
while ($row = $result->fetch_assoc()) {
    $productos_mas_vendidos[] = $row;
    $total_unidades += $row['total_vendido'];
    $total_ingresos += $row['ingresos_totales'];
}

// DEBUG: Verificar si hay datos en ventas
$sql_debug = "SELECT COUNT(*) as total_ventas, 
                     MIN(fecha_venta) as primera_venta, 
                     MAX(fecha_venta) as ultima_venta 
              FROM ventas 
              WHERE fecha_venta BETWEEN ? AND ?";
$stmt_debug = $conexion->prepare($sql_debug);
$stmt_debug->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_debug->execute();
$debug_result = $stmt_debug->get_result();
$debug_data = $debug_result->fetch_assoc();

// Obtener categorías disponibles para el filtro
$sql_categorias = "SELECT DISTINCT categoria FROM lista_productos ORDER BY categoria";
$result_categorias = $conexion->query($sql_categorias);
$categorias_disponibles = [];
while ($row = $result_categorias->fetch_assoc()) {
    $categorias_disponibles[] = $row['categoria'];
}

// Determinar período para mostrar
$periodo_texto = date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y');
?>


<style>
        :root {
            --primary-color: #2c5282; /* Azul principal de tu paleta */
            --secondary-color: #1e3a5f; /* Azul oscuro */
            --success-color: #2ecc71;
            --info-color: #4a7bac; /* Azul claro de tu paleta */
            --warning-color: #ff9800; /* Naranja de tu paleta */
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --border-radius: var(--rad);
            --box-shadow: var(--s);
            --transition: all 0.3s ease;
        }
        
        .header3 h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            gap: 15px;
            background: var(--gold);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            filter: drop-shadow(0 0 8px rgba(218, 165, 32, 0.4));
        }
        
        .header3 h1 i {
            color: #ff9800;
            text-shadow: var(--txt-sh);
        }
        
        .header3 p {
            font-size: 1.2rem;
            color: var(--gray-color);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .periodo-selector {
            background: white;
            padding: 20px;
            border-radius: var(--rad);
            margin-top: 20px;
            box-shadow: var(--s);
            display: inline-block;
        }
        
        .periodo-selector label {
            font-weight: 600;
            color: var(--dark-color);
            margin-right: 10px;
        }
        
        .periodo-selector select {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
        }
        
        .date-info {
            background: white;
            padding: 15px;
            border-radius: var(--rad);
            display: inline-block;
            margin-top: 10px;
            box-shadow: var(--s);
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: var(--rad);
            padding: 15px;
            margin: 20px auto;
            max-width: 800px;
            font-size: 0.9rem;
        }
        
        .debug-info h4 {
            color: #856404;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .debug-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 10px;
        }
        
        .debug-stat {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--s);
        }
        
        .debug-label {
            font-weight: 600;
            color: var(--gray-color);
            font-size: 0.8rem;
        }
        
        .debug-value {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-top: 5px;
        }
        
        .filtros-container {
            background: white;
            border-radius: var(--rad);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--s);
        }
        
        .filtros-container h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .filtro-grupo {
            gap: 8px;
        }
        
        .filtro-grupo label {
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filtro-grupo select {
            padding: var(--pad);
            border: 2px solid #e0e0e0;
            border-radius: var(--rad);
            font-size: 1rem;
            background: white;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .filtro-grupo select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 82, 130, 0.2);
        }
        
        .btn-filtrar {
            background: var(--orange-meta);
            color: white;
            border: none;
            padding: var(--pad);
            border-radius: var(--rad2);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            align-self: flex-end;
            box-shadow: var(--s);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-filtrar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--gold);
            transition: width 0.5s ease;
            border-radius: var(--rad2);
            z-index: 0;
        }
        
        .btn-filtrar:hover::before {
            width: 100%;
        }
        
        .btn-filtrar span {
            position: relative;
            z-index: 2;
        }
        
        .btn-filtrar:hover {
            transform: translateY(-2px);
            box-shadow: var(--s2);
            border-color: yellow;
            text-shadow: var(--txt-sh);
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: var(--rad);
            padding: 25px;
            box-shadow: var(--s);
            transition: var(--transition);
            border-top: 4px solid var(--primary-color);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--s2);
        }
        
        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            color: var(--gray-color);
            font-size: 1rem;
        }
        
        .card-title i {
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }
        
        .card-subtitle {
            font-size: 0.9rem;
            color: var(--gray-color);
            margin-top: 5px;
        }
        
        .productos-table-container {
            background: white;
            border-radius: var(--rad);
            overflow: hidden;
            box-shadow: var(--s);
            margin-bottom: 30px;
        }
        
        .table-header3 {
            background: var(--blue-meta);
            color: white;
            padding: 25px;
        }
        
        .table-header3 h2 {
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: var(--txt-sh);
        }
        
        .table-subtitle {
            margin-top: 8px;
            opacity: 0.9;
            font-size: 0.95rem;
            text-shadow: var(--txt-sh);
        }
        
        .productos-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .productos-table thead {
            background-color: #f8f9fa;
        }
        
        .productos-table th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid #e9ecef;
            font-size: 1rem;
        }
        
        .productos-table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid #e9ecef;
        }
        
        .productos-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .productos-table td {
            padding: 18px 15px;
            color: var(--dark-color);
        }
        
        .ranking-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            box-shadow: var(--s);
        }
        
        .ranking-1 { background: var(--gold); }
        .ranking-2 { background: linear-gradient(135deg, #c0c0c0, #a0a0a0); }
        .ranking-3 { background: linear-gradient(135deg, #cd7f32, #a6692e); }
        .ranking-other { background: linear-gradient(135deg, var(--info-color), #3a86ff); }
        
        .producto-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .producto-nombre {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
        }
        
        .producto-codigo {
            font-size: 0.85rem;
            color: var(--gray-color);
        }
        
        .categoria-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            background-color: rgba(44, 82, 130, 0.1);
            color: var(--primary-color);
        }
        
        .stats-cell {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .unidades-cell { color: #2ecc71; }
        .ingresos-cell { 
            color: #e74c3c;
            background: var(--gold);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .precio-cell { color: #3498db; }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--gray-color);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #a0a0a0;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .periodo-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--gray-color);
            background: white;
            padding: 15px;
            border-radius: var(--rad);
            box-shadow: var(--s);
        }
        
        /* Nuevas clases para mejor integración */
        .text-shadow {
            text-shadow: var(--txt-sh);
        }
        
        .box-shadow {
            box-shadow: var(--s);
        }
        
        .box-shadow-hover:hover {
            box-shadow: var(--s2);
        }
        
        .border-radius {
            border-radius: var(--rad);
        }
        
        .border-radius-lg {
            border-radius: var(--rad2);
        }
        
        .bg-orange-meta {
            background: var(--orange-meta);
        }
        
        .bg-blue-meta {
            background: var(--blue-meta);
        }
        
        .bg-gold {
            background: var(--gold);
        }
        
        .text-gold {
            background: var(--gold);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        @media (max-width: 768px) {
            .header3 h1 {
                font-size: 2rem;
            }
            
            .filtros-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-filtrar {
                width: 100%;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .productos-table {
                display: block;
                overflow-x: auto;
            }
            
            .productos-table th,
            .productos-table td {
                padding: 12px 10px;
            }
            
            .periodo-info {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .debug-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>










    <div class="container">
        <!-- header3 -->
        <div class="header3 centrar column">
            <h1 class="f-center"><i class="fas fa-chart-line"></i> Productos Más Vendidos</h1>
            <p>Análisis de ventas de los últimos 30 días - Top <?= $limite ?> productos</p>
            <div class="date-info">
                <i class="fas fa-calendar-alt"></i>
                Período: <?= $periodo_texto ?>
            </div>
        </div>
        <br>
        
        <!-- Filtros -->
        <div class="filtros-container" style="display:none">
            <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
            <form method="GET" class="filtros-grid">
                <div class="filtro-grupo column">
                    <label for="limite"><i class="fas fa-sort-amount-down"></i> Cantidad de productos:</label>
                    <select id="limite" name="limite">
                        <option value="5" <?= $limite == 5 ? 'selected' : '' ?>>Top 5 productos</option>
                        <option value="10" <?= $limite == 10 ? 'selected' : '' ?>>Top 10 productos</option>
                        <option value="15" <?= $limite == 15 ? 'selected' : '' ?>>Top 15 productos</option>
                        <option value="20" <?= $limite == 20 ? 'selected' : '' ?>>Top 20 productos</option>
                    </select>
                </div>
                
                <div class="filtro-grupo column">
                    <label for="categoria"><i class="fas fa-tags"></i> Filtrar por categoría:</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias_disponibles as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" 
                                <?= $categoria == $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-filtrar">
                    <i class="fas fa-search"></i> Aplicar Filtros
                </button>
            </form>
        </div>
        
        <!-- Estadísticas principales -->
        <div class="stats-cards">
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-boxes"></i> Total de Productos
                </div>
                <div class="card-value"><?= count($productos_mas_vendidos) ?></div>
                <div class="card-subtitle">Productos en el ranking</div>
            </div>
            
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-shopping-cart"></i> Unidades Vendidas
                </div>
                <div class="card-value"><?= number_format($total_unidades) ?></div>
                <div class="card-subtitle">Total de unidades en el período</div>
            </div>
            
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-money-bill-wave"></i> Ingresos Totales
                </div>
                <div class="card-value">Bs <?= number_format($total_ingresos, 2) ?></div>
                <div class="card-subtitle">Generado por estos productos</div>
            </div>
        </div>
        
        <!-- Tabla de productos -->
        <div class="productos-table-container">
            <div class="table-header3">
                <h2><i class="fas fa-list-ol"></i> Ranking de Productos Más Vendidos</h2>
                <div class="table-subtitle">
                    <?php if (!empty($categoria)): ?>
                        Mostrando categoría: <strong><?= htmlspecialchars($categoria) ?></strong>
                    <?php else: ?>
                        Todas las categorías
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (empty($productos_mas_vendidos)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No se encontraron ventas</h3>
                    <p>No hay registros de ventas para el período seleccionado. Intenta cambiar los filtros o seleccionar otro período.</p>
                    <div style="margin-top: 20px; font-size: 0.9rem;">
                        <p><strong>Consulta SQL utilizada:</strong></p>
                        <pre style="background: #f8f9fa; padding: 10px; border-radius: 5px; text-align: left; font-size: 0.8rem;">
Período: <?= $fecha_inicio ?> - <?= $fecha_fin ?>
Categoría: <?= $categoria ?: 'Todas' ?>
Límite: <?= $limite ?>
                        </pre>
                    </div>
                </div>
            <?php else: ?>
                <table class="productos-table">
                    <thead>
                        <tr>
                            <th width="80">Ranking</th>
                            <th>Producto</th>
                            <th width="180">Categoría</th>
                            <th width="150" class="text-center">Unidades Vendidas</th>
                            <th width="150" class="text-center">Veces Vendido</th>
                            <th width="150" class="text-center">Precio Promedio</th>
                            <th width="180" class="text-center">Ingresos Totales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_mas_vendidos as $index => $producto): 
                            $ranking_class = $index == 0 ? 'ranking-1' : 
                                           ($index == 1 ? 'ranking-2' : 
                                           ($index == 2 ? 'ranking-3' : 'ranking-other'));
                        ?>
                            <tr>
                                <td>
                                    <div class="ranking-badge <?= $ranking_class ?>">
                                        <?= $index + 1 ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="producto-info">
                                        <div class="producto-nombre">
                                            <?= htmlspecialchars($producto['nombre']) ?>
                                        </div>
                                        <div class="producto-codigo">
                                            Código: <?= htmlspecialchars($producto['codigo']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="categoria-badge">
                                        <?= htmlspecialchars($producto['categoria']) ?>
                                    </span>
                                </td>
                                <td class="stats-cell unidades-cell">
                                    <i class="fas fa-box"></i> <?= number_format($producto['total_vendido']) ?>
                                </td>
                                <td class="stats-cell">
                                    <i class="fas fa-repeat"></i> <?= number_format($producto['veces_vendido']) ?>
                                </td>
                                <td class="stats-cell precio-cell">
                                    Bs <?= number_format($producto['precio_promedio'], 2) ?>
                                </td>
                                <td class="stats-cell ingresos-cell">
                                    <i class="fas fa-money-bill"></i> <?= number_format($producto['ingresos_totales'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Información del período -->
        <div class="periodo-info">
            <div>
                <i class="fas fa-info-circle"></i>
                Los datos corresponden a los últimos 30 días (<?= $periodo_texto ?>)
            </div>
            <div>
                <i class="fas fa-database"></i>
                <?= count($productos_mas_vendidos) ?> productos mostrados
            </div>
            <div>
                <i class="fas fa-clock"></i>
                Actualizado: <?= date('d/m/Y H:i:s') ?>
            </div>
        </div>
    </div>
    
