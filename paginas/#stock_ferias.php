<?php
require 'includes/conexion.php';

// Configuración de paginación
$registrosPorPagina = 15;

$paginaActual = 1;

// Obtener página actual desde GET
if (isset($_GET['pagina'])) {
    $paginaActual = (int) $_GET['pagina'];
}
if ($paginaActual < 1) {
    $paginaActual = 1;
}

// Obtener filtro de feria desde GET
$feriaFiltro = isset($_GET['feria']) ? $_GET['feria'] : 'todos';

// Consulta para contar el total de registros con filtro
$sqlCount = "SELECT COUNT(*) AS total 
             FROM almacen_ferias a
             LEFT JOIN lista_productos lp ON a.codigo = lp.codigo
             LEFT JOIN detalle_producto dp ON a.id_detalle = dp.id_detalle
             LEFT JOIN ferias f ON a.id_feria = f.id_feria";

// Agregar condición WHERE si hay filtro
if ($feriaFiltro !== 'todos' && !empty($feriaFiltro)) {
    $sqlCount .= " WHERE a.id_feria = " . intval($feriaFiltro);
}

$totalQuery = mysqli_query($conexion, $sqlCount);

if (!$totalQuery) {
    die("Error al contar productos: " . mysqli_error($conexion));
}

$totalRegistros = (int) mysqli_fetch_assoc($totalQuery)['total'];
$totalPaginas   = ($totalRegistros > 0)
    ? ceil($totalRegistros / $registrosPorPagina)
    : 1;

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$inicio = ($paginaActual - 1) * $registrosPorPagina;

// Consulta principal con LIMIT para paginación y filtro
$sqlProd = "SELECT 
            a.id_almacen, 
            a.codigo, 
            lp.codigo AS codigo_producto, 
            lp.nombre AS nombre, 
            a.id_detalle,
            a.id_feria,
            f.nombre_feria,
            dp.medida, 
            dp.detalle, 
            dp.precio_unitario, 
            a.cantidad, 
            (SELECT ruta_imagen FROM imagenes WHERE codigo = a.codigo LIMIT 1) AS ruta_imagen
        FROM almacen_ferias a
        LEFT JOIN lista_productos lp ON a.codigo = lp.codigo
        LEFT JOIN detalle_producto dp ON a.id_detalle = dp.id_detalle
        LEFT JOIN ferias f ON a.id_feria = f.id_feria";

// Agregar condición WHERE si hay filtro
if ($feriaFiltro !== 'todos' && !empty($feriaFiltro)) {
    $sqlProd .= " WHERE a.id_feria = " . intval($feriaFiltro);
}

$sqlProd .= " ORDER BY a.fecha_modificacion DESC
        LIMIT ?, ?";

$stmt = $conexion->prepare($sqlProd);
if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

$stmt->bind_param("ii", $inicio, $registrosPorPagina);
$stmt->execute();
$resultProd = $stmt->get_result();

$productos = [];

if ($resultProd->num_rows > 0) {
    while ($row = $resultProd->fetch_assoc()) {
        $productos[] = $row;
    }
}
$stmt->close();

// Consulta de ferias
$query = "SELECT id_feria, nombre_feria 
          FROM ferias 
          WHERE estado = 'activo' 
          ORDER BY nombre_feria ASC";

$resultado = mysqli_query($conexion, $query);
$ferias = [];
if ($resultado) {
    $ferias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_free_result($resultado);
}
mysqli_close($conexion);

// Función para construir URLs con los parámetros correctos
function construirUrl($pagina = null, $feria = null) {
    $params = [];
    
    // Siempre mantener el parámetro p
    $params['p'] = 'stock_ferias.php';
    
    // Agregar página si se especifica
    if ($pagina !== null) {
        $params['pagina'] = $pagina;
    } elseif (isset($_GET['pagina'])) {
        $params['pagina'] = $_GET['pagina'];
    }
    
    // Agregar filtro de feria si se especifica
    if ($feria !== null) {
        if ($feria !== 'todos') {
            $params['feria'] = $feria;
        }
    } elseif (isset($_GET['feria']) && $_GET['feria'] !== 'todos') {
        $params['feria'] = $_GET['feria'];
    }
    
    return 'b1t.php?' . http_build_query($params);
}
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/addProduct_almacen_feria.php' ?>
<?php include 'forms/editCantProduct_feria.php' ?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Stock de las Ferias</h3>

    <div class="b-azul pad20 cont-elemts">

        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input-stock1" oninput="buscar2C3C('search-input-stock1','tablaStockProductos')" placeholder="Nombre prod. o cantidad">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>

        <button class="btn-load orange" onclick="mostrarFormBuscador('formAddProdAlmacenTienda','lista-prod-agregados')"><span>Agregar producto</span></button>
       
        <div class="m-left">
            <label class="f-white" for="filtroLugarVenta">Filtrar por:</label>
            <select class="select pd" id="feria_select" name="feria_select" onchange="filtrarPorFerias()">
                <option value="todos" <?php echo ($feriaFiltro == 'todos') ? 'selected' : ''; ?>>Todos</option>
                <?php foreach ($ferias as $feria): ?>
                    <option value="<?= $feria['id_feria']; ?>" <?php echo ($feriaFiltro == $feria['id_feria']) ? 'selected' : ''; ?>>
                        <?= $feria['nombre_feria']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<table class="tablaStyle top105" id="tablaStockProductos">
<thead>
    <tr>
        <th>Imagen</th>
        <th>Feria</th>
        <th>Cantidad</th>
        <th>Nombre del Producto</th>
        <th>Código</th>
        <th><div class="f-center">Medida</div></th>
        <th>Detalle</th>
        <th><div class="m-right50">Acciones</div></th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($productos)): ?>
       <?php foreach ($productos as $fila):
            $precioFormateado = $fila['precio_unitario'];
            if (is_numeric($precioFormateado)) {
                $precioFormateado = (float)$precioFormateado;
                $precioFormateado = $precioFormateado + 0;
            }
        ?>
            <tr>
                <td>
                    <?php 
                        $ruta_imagen = $fila['ruta_imagen'] ?: 'No disponible';
                        $ruta_imagen = str_replace('../', '', $ruta_imagen);
                        if ($ruta_imagen !== 'No disponible') {
                            echo '<img src="' . $ruta_imagen . '" width="80" height="80" alt="Imagen del producto">';
                        } else {
                            echo 'No disponible';
                        }
                    ?>
                </td>
                <td>
                    <span style="display:none"><?php echo htmlspecialchars($fila['id_feria'] ?: 'No especificada'); ?></span> 
                    <?php echo htmlspecialchars($fila['nombre_feria'] ?: 'S/Nombre'); ?> 
                </td>
                <td>
                    <div class="f-center f-med">
                        <b><?php echo htmlspecialchars($fila['cantidad']); ?></b>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($fila['nombre'] ?: 'Sin nombre'); ?></td>
                <td><?php echo htmlspecialchars($fila['codigo'] ?: 'Sin código'); ?></td>
                <td>
                    <div class="column centrar">
                      <p class="f-peq"><?php echo htmlspecialchars($fila['medida'] ?: 'No especificado'); ?></p>
                      <p class="hora"><?php echo htmlspecialchars($precioFormateado); ?><span>Bs</span></p>
                    </div>
                </td>
                <td>
                    <div id="det_<?php echo htmlspecialchars($fila['codigo']); ?>">
                        <div class="f-peq h-celda">
                            <?php echo nl2br(htmlspecialchars($fila['detalle'] ?: 'No especificado')); ?>
                        </div>
                    </div>
                </td>
                <td>
                    <form action="actions/eliminar_cantidadAlmacen_tienda.php" class="formFunctions" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta cantidad del almacén?');">
                        <button type="button" class="btn-load azul"
                            onclick='editarCantProductFeria(<?php 
                                echo json_encode([
                                    "codigo" => $fila["codigo"],
                                    "id_feria" => $fila["id_feria"],
                                    "id_detalle" => $fila["id_detalle"],
                                    "cantidad" => $fila["cantidad"],
                                    "nombre" => $fila["nombre"],
                                    "medida" => $fila["medida"],
                                    "detalle" => $fila["detalle"],
                                    "ruta_imagen" => $ruta_imagen,
                                    "precio" => $fila["precio_unitario"],
                                    "id_almacen" => $fila["id_almacen"]
                                ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG); 
                            ?>)'>
                            <span><i class="fa-solid fa-pencil"></i></span>
                        </button>
                        <button type="submit" class="btn-load rojo">
                            <span><i class="fa-solid fa-trash"></i></span>
                        </button>
                        <input type="hidden" name="id_almacen" value="<?php echo htmlspecialchars($fila['id_almacen']); ?>" readonly>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">No hay productos en el almacén de las ferias</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>

<!-- Controles de Paginación -->
<div class="contenedor-paginacion" style="position: relative;">
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacion">
        <div class="info-paginacion">
            Mostrando <?php echo count($productos); ?> de <?php echo $totalRegistros; ?> productos
        </div>
        
        <ul class="pagination">
            <!-- Botón Anterior -->
            <?php if ($paginaActual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo construirUrl($paginaActual - 1); ?>" aria-label="Anterior">
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
                echo '<li class="page-item"><a class="page-link" href="' . construirUrl(1) . '">1</a></li>';
                if ($inicioPagina > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            for ($i = $inicioPagina; $i <= $finPagina; $i++): 
            ?>
                <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo construirUrl($i); ?>">
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
                    <a class="page-link" href="<?php echo construirUrl($totalPaginas); ?>">
                        <?php echo $totalPaginas; ?>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Botón Siguiente -->
            <?php if ($paginaActual < $totalPaginas): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo construirUrl($paginaActual + 1); ?>" aria-label="Siguiente">
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
            <select id="irPagina" class="select-pagina" onchange="window.location.href=this.value">
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <option value="<?php echo construirUrl($i); ?>" <?php echo ($i == $paginaActual) ? 'selected' : ''; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>
</div>


