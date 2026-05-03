<?php
require 'includes/conexion.php';

$sqlProd = "SELECT 
            a.id_almacen, 
            a.codigo, 
            lp.codigo AS codigo_producto, 
            lp.nombre AS nombre, 
            a.id_detalle, 
            dp.medida, 
            dp.detalle, 
            dp.precio_unitario, 
            a.cantidad, 
            (SELECT ruta_imagen FROM imagenes WHERE codigo = a.codigo LIMIT 1) AS ruta_imagen
        FROM almacen_ferias a
        LEFT JOIN lista_productos lp ON a.codigo = lp.codigo
        LEFT JOIN detalle_producto dp ON a.id_detalle = dp.id_detalle
        ORDER BY a.fecha_modificacion DESC";

$resultProd = $conexion->query($sqlProd);

$productos = [];

if ($resultProd->num_rows > 0) {
    while ($row = $resultProd->fetch_assoc()) {
        $productos[] = $row;
    }
}
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/addProduct_almacen_tienda.php' ?>
<?php include 'forms/editCantProduct_tienda.php' ?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Stock de la Tienda</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input-stock1" oninput="buscar2C3C('search-input-stock1','tablaStockProductos')" placeholder="Nombre prod. o cantidad">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <button class="btn-load orange" onclick="mostrarFormBuscador('formAddProdAlmacenTienda','lista-prod-agregados')"><span>Agregar producto</span></button>
    </div>
</div>



<table class="tablaStyle top105" id="tablaStockProductos">
<thead>
    <tr>
        <th>Imagen</th>
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
                            onclick='editarCantProduct(<?php 
                                echo json_encode([
                                    "codigo" => $fila["codigo"],
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
            <td colspan="7">No hay productos en el almacén de las ferias</td>
        </tr>
    <?php endif; ?>
</tbody>
</table>