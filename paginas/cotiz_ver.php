<?php
require 'includes/conexion.php';

$registrosPorPagina = 15;

$paginaActual = 1;

if (isset($_GET['pagina'])) {
    $paginaActual = (int) $_GET['pagina'];
} elseif (isset($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $queryParams);
    if (isset($queryParams['pagina'])) {
        $paginaActual = (int) $queryParams['pagina'];
    }
}
if ($paginaActual < 1) {
    $paginaActual = 1;
}

$totalQuery = mysqli_query(
    $conexion,
    "SELECT COUNT(*) AS total 
     FROM cotizaciones"
);

if (!$totalQuery) {
    die("Error al contar cotizaciones: " . mysqli_error($conexion));
}

$totalRegistros = (int) mysqli_fetch_assoc($totalQuery)['total'];
$totalPaginas   = ($totalRegistros > 0)
    ? ceil($totalRegistros / $registrosPorPagina)
    : 1;

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$inicio = ($paginaActual - 1) * $registrosPorPagina;

function obtenerCotizaciones($conexion, $inicio, $registrosPorPagina) {

    $query = "
        SELECT 
            id_cotizacion,
            titulo,
            fecha_caducidad,
            REPLACE(cuenta_bancaria, 'cuenta_', '') AS cuenta_id,
            ruta_cotizacion,
            aprobado,
            estado,
            DATE(fecha_cotizacion) AS solo_fecha,
            TIME(fecha_cotizacion) AS solo_hora
        FROM cotizaciones
        ORDER BY fecha_cotizacion DESC
        LIMIT ?, ?
    ";

    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        die("Error en prepare: " . $conexion->error);
    }

    $stmt->bind_param("ii", $inicio, $registrosPorPagina);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $cotizaciones = [];

    while ($fila = $resultado->fetch_assoc()) {

        if (!empty($fila['cuenta_id'])) {
            $cuentaQuery = $conexion->prepare(
                "SELECT titularCuenta, nombreBanco 
                 FROM cuentas_bancarias 
                 WHERE id = ?"
            );
            $cuentaQuery->bind_param("i", $fila['cuenta_id']);
            $cuentaQuery->execute();
            $cuentaResult = $cuentaQuery->get_result();

            if ($cuentaResult->num_rows > 0) {
                $cuenta = $cuentaResult->fetch_assoc();
                $fila['cuenta_info'] = $cuenta['titularCuenta'] . ' - ' . $cuenta['nombreBanco'];
            } else {
                $fila['cuenta_info'] = 'Cuenta no encontrada';
            }
        } else {
            $fila['cuenta_info'] = 'Sin cuenta asignada';
        }

        $cotizaciones[] = $fila;
    }

    return $cotizaciones;
}

$cotizaciones = obtenerCotizaciones($conexion, $inicio, $registrosPorPagina);

$sqlCuentas = "
    SELECT id, titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR 
    FROM cuentas_bancarias
";
$resultadoCuentas = mysqli_query($conexion, $sqlCuentas);

if (!$resultadoCuentas) {
    die("Error al obtener cuentas: " . mysqli_error($conexion));
}
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/acordeonCotiz.php' ?>
<?php include 'forms/aprobarCotiz.php' ?>
<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Buscar una cotización</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" oninput="buscar1C2C('search-input', 'tabla-cotizaciones')" id="search-input" placeholder="Ingrese # o título">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="m-left">
            <label class="f-white" for="filtroLugarVenta">Filtrar por aprobados:</label>
            <select class="select pd" id="filtro-aprobado" onchange="filtrarAprobadas()">
                <option value="todos">Todas</option>
                <option value="si">Solo aprobadas</option>
                <option value="no">Solo no aprobadas</option>
            </select>
        </div>
    </div>
</div>

<table class="tablaStyle top105" id="tabla-cotizaciones">
    <thead>
        <tr>
            <th>#</th>
            <th>Título</th>
            <th>Aprobado</th>
            <th>Fecha Cotización</th>
            <th>Fecha Caducidad</th>
            <th>Cuenta Bancaria</th>
            <th>Descargar</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($cotizaciones) > 0): ?>
            <?php foreach ($cotizaciones as $cotizacion): ?>
            <tr>
                <td><?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?></td>
                <td><p class="f-peq"><?php echo htmlspecialchars($cotizacion['titulo']); ?><p></td>
                <td> 
                    <div class="centrar">
                        <?php if ($cotizacion['aprobado'] == 'no' || $cotizacion['aprobado'] == '0'): ?>
                            <button type="button" class="btn-load rojo" onclick="aprobarCotization('<?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?>')">
                                <span><?php echo htmlspecialchars($cotizacion['aprobado']); ?></span>
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn-load" style="background:green;">
                                <span><?php echo htmlspecialchars($cotizacion['aprobado']); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <div class="column centrar">
                      <b><?= date('d-m-Y', strtotime($cotizacion['solo_fecha'])); ?></b>
                      <span class="hora"><?php echo htmlspecialchars($cotizacion['solo_hora']); ?></span>
                    </div>
                </td>
                <td><div class="hora"><?php echo htmlspecialchars($cotizacion['fecha_caducidad']); ?></div></td>
                <td><div class="hora"><?php echo htmlspecialchars($cotizacion['cuenta_info'] ?? 'Sin información'); ?></div></td>
                <td>
                    <div class="centrar"><button class="btn-load azul" onclick="descargarPdf('<?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?>')" type="button"><span><i class="fa-solid fa-file-pdf"></span></i></button></div>
                </td>
                <td> 
                    <form action="actions/eliminar_cotizacion.php" class="formFunctions" method="post" onsubmit="return confirm('¿Desea eliminar la cotización? Se borrarán todos los registros de esta cotización');">
                        <input type="hidden" name="id_cotizacion" value="<?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?>">
                         
                        <?php if ($cotizacion['aprobado'] == 'no' || $cotizacion['aprobado'] == '0'): ?>
                            <button class="btn-load azul" type="button"
                                    onclick="editarCotization('<?php echo htmlspecialchars($cotizacion['id_cotizacion']); ?>')">
                                <span><i class="fa-solid fa-pencil"></i></span>
                            </button>
                        <?php else: ?>
                            <button class="btn-load gris" type="button" disabled
                                    title="No se puede editar una cotización aprobada">
                                <span><i class="fa-solid fa-pencil"></i></span>
                            </button>
                        <?php endif; ?>

                        <button class="btn-load rojo" type="submit">
                            <span><i class="fa-solid fa-trash"></i></span>
                        </button>
                     </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No hay cotizaciones registradas.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>








<!-- Controles de Paginación -->
<div class="contenedor-paginacion" style="position: relative;">
    <!-- Controles de Paginación -->
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacion">
        <div class="info-paginacion">
            Mostrando <?php echo count($cotizaciones); ?> de <?php echo $totalRegistros; ?> cotizaciones
        </div>
        
        <ul class="pagination">
            <!-- Botón Anterior -->
            <?php if ($paginaActual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="cargarPaginaConPagina('cotiz_ver.php', <?php echo $paginaActual - 1; ?>)" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            <?php endif; ?>

            <!-- Números de página -->
            <?php 
            $inicioPagina = max(1, $paginaActual - 2);
            $finPagina = min($totalPaginas, $paginaActual + 2);
            
            if ($inicioPagina > 1) {
                echo '<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="cargarPaginaConPagina(\'cotiz_ver.php\', 1)">1</a></li>';
                if ($inicioPagina > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            for ($i = $inicioPagina; $i <= $finPagina; $i++): 
            ?>
                <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                    <a class="page-link" href="javascript:void(0);" onclick="cargarPaginaConPagina('cotiz_ver.php', <?php echo $i; ?>)">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <?php if ($finPagina < $totalPaginas): 
                if ($finPagina < $totalPaginas - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                ?>
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="cargarPaginaConPagina('cotiz_ver.php', <?php echo $totalPaginas; ?>)">
                        <?php echo $totalPaginas; ?>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Botón Siguiente -->
            <?php if ($paginaActual < $totalPaginas): ?>
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="cargarPaginaConPagina('cotiz_ver.php', <?php echo $paginaActual + 1; ?>)" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            <?php endif; ?>
        </ul>
        
        <!-- Selector de página rápida -->
        <div class="selector-pagina">
            <label for="irPagina">Ir a página:</label>
            <select id="irPagina" class="select-pagina" onchange="cargarPaginaConPagina('cotiz_ver.php', this.value)">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $paginaActual) ? 'selected' : ''; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>
</div>









<style>
/* Estilos de paginación */
/* Contenedor principal para asegurar posición */
.contenedor-paginacion {
    width: 100%;
    margin-top: 20px;
    clear: both;
}

/* Estilos de paginación */
.paginacion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    flex-wrap: wrap;
    gap: 15px;
    position: static !important;
    z-index: 10 !important;
}

.info-paginacion {
    font-size: 14px;
    color: #666;
    white-space: nowrap;
}

.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 5px;
    flex-wrap: wrap;
}

.page-item {
    margin: 0;
}

.page-link {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #007bff;
    background-color: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    transition: all 0.3s ease;
    min-width: 40px;
    text-align: center;
}

.page-link:hover {
    background-color: #e9ecef;
    color: #0056b3;
}

.page-item.active .page-link {
    background: var(--blue-meta);
    color: white;
    border-color: #007bff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #f8f9fa;
    opacity: 0.6;
}

.selector-pagina {
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}

.selector-pagina label {
    font-size: 14px;
    color: #666;
}

.select-pagina {
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    min-width: 60px;
}

.select-pagina:focus {
    outline: none;
    border-color: #007bff;
}

/* Botones utilizados para opacar los btns de edidion de una cotizacion aprobada */
.btn-load.gris {
    background-color: #cccccc;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-load.gris:hover {
    background-color: #cccccc;
    transform: none;
}

/* Estilos responsivos */
@media (max-width: 992px) {
    .paginacion {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 15px;
    }
    
    .info-paginacion {
        order: 1;
    }
    
    .pagination {
        order: 2;
        justify-content: center;
    }
    
    .selector-pagina {
        order: 3;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .page-link {
        padding: 6px 10px;
        min-width: 35px;
        font-size: 14px;
    }
    
    .pagination {
        gap: 3px;
    }
    
    .selector-pagina {
        flex-direction: column;
        gap: 5px;
    }
}
</style>