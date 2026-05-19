<?php
require 'includes/conexion.php';

// Recuperar filtro de feria desde URL
$filtro_feria_id = isset($_GET['filtro_feria']) ? $_GET['filtro_feria'] : null;

// Consulta principal CON filtro
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

// Agregar filtro si existe y no es 'todos' (SOLO UNA VEZ)
if ($filtro_feria_id && $filtro_feria_id !== 'todos' && is_numeric($filtro_feria_id)) {
    $sqlProd .= " WHERE a.id_feria = " . intval($filtro_feria_id);
}
$sqlProd .= " ORDER BY a.fecha_modificacion DESC";
$resultProd = mysqli_query($conexion, $sqlProd);
if (!$resultProd) {
    die("Error al consultar productos: " . mysqli_error($conexion));
}
$productos = [];

if (mysqli_num_rows($resultProd) > 0) {
    while ($row = mysqli_fetch_assoc($resultProd)) {
        $productos[] = $row;
    }
}
mysqli_free_result($resultProd);


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
       
        <!-- En la parte del HTML, modifica el select para agregar un ID y atributo data-selected -->
        <div class="m-left">
            <label class="f-white" for="filtroLugarVenta">Filtrar por:</label>
            <select class="select pd" id="feria_select" name="feria_select" onchange="filtrarPorFerias(this.value)">
                <option value="todos">Todos</option>
                <?php 
                // Obtener el valor del filtro desde la URL
                $filtro_feria_seleccionado = isset($_GET['filtro_feria']) ? $_GET['filtro_feria'] : null;
                
                foreach ($ferias as $feria): 
                    $selected = ($filtro_feria_seleccionado == $feria['id_feria']) ? 'selected' : '';
                ?>
                    <option value="<?= $feria['id_feria']; ?>" <?= $selected; ?>>
                        <?= htmlspecialchars($feria['nombre_feria']); ?>
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