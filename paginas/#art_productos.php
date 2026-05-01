<?php
require 'includes/conexion.php';
$sql = "
SELECT 
    codigo, 
    nombre, 
    categoria,
    caracteristicas,
    tienda_virtual,
    ultima_actualizacion
FROM lista_productos
ORDER BY ultima_actualizacion DESC";
$resultado = $conexion->query($sql);
$resultado2 = $conexion->query($sql);
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addProducto.php'; ?>
<?php include 'forms/addDetailProd.php'; ?>


<section class="panel">
    <h3 class="b-naranja f-white pad-left20">Lista de productos</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" oninput="buscar1C2C('search-input','tablaProdcts')" id="search-input" placeholder="Ingrese nombre o código">
                    <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <button class="btn-load orange" onclick="mostrarForm('formProducto')"><span>Agregar producto</span></button>
    </div>
</section>


<table class="tablaStyle col1-peq top105" id="tablaProdcts">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th><div class="f-peq">Tienda virtual</div></th>
            <th><div class="m-right180">Características</div></th>
            <th>Medidas</th>
            <th>Imágenes</th>
            <th>Acciónes</th>
        </tr>
    </thead>
    <tbody>

        <?php while ($producto = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><div class="hora"><?php echo $producto['codigo']; ?></div></td>
                <td><div class="f-peq"><?php echo $producto['nombre']; ?></div></td>
                <td><div class="hora"><?php echo $producto['categoria']; ?></div></td>
                <td><div class="hora centrar"><?php echo $producto['tienda_virtual']; ?></div></td>
                <td><div class="f-peq h-celda"><?php echo nl2br(htmlspecialchars($producto['caracteristicas'], ENT_QUOTES, 'UTF-8')); ?></div></td>
                <td>
                    <button class="btn-invi" onclick="mostrarDetalles('<?php echo $producto['codigo']; ?>')">Ver medidas</button>
                </td>
                <td>
                    <button class="btn-invi" onclick="mostrarImagenes('<?php echo $producto['codigo']; ?>')">Ver imágenes</button>
                </td>
                <td> 
                    <form action="actions/eliminar_producto.php" class="formFunctions" method="post">
                        <button type="button" class="btn-load azul" 
                                onclick="editarProd(`<?php echo $producto['codigo']; ?>`, `<?php echo addslashes($producto['nombre']); ?>`, `<?php echo addslashes($producto['categoria']); ?>`, `<?php echo addslashes($producto['caracteristicas']); ?>`, `<?php echo $producto['tienda_virtual']; ?>`)">
                                <span><i class="fa-solid fa-pencil"></i></span>
                        </button>
                        <input type="hidden" name="codigoProd" value="<?php echo htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        <button type="submit" class="btn-load rojo" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')"><span><i class="fa-solid fa-trash"></i></span></button>
                    </form>
                </td>
            </tr>
            <?php } ?>
    </tbody>
</table>

<style>
    .col1-peq th:nth-child(1),
    .col1-peq td:nth-child(1) {
        max-width: 120px;
    }
</style>








<!-- Formulario de detalles -->
<?php while ($producto = $resultado2->fetch_assoc()) { ?>

<section id="detail_<?php echo $producto['codigo']; ?>" class="formStyle b-azul" style="z-index:5;">
    <div class="cabecera">
        <h2 class="f-med f-white"><?php echo $producto['codigo']; ?> - <?php echo $producto['nombre']; ?></h2>
        <button type="button" onclick="mostrarDetalles('<?php echo $producto['codigo']; ?>')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="panelDatosTable">
        <button class="btn-load gold" type="button" onclick="agregarDetalleProd2('<?php echo $producto['codigo']; ?>')"><span>Agregar medida</span></button>
    </div>

    <div class="containerTableDetails">
        <table class="tablaStyle" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2); width:100%;">
            <thead>
                <tr>
                    <th>Code-Barra</th>
                    <th>Medida</th>
                    <th>Detalle</th>
                    <th>Precio Unitario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $detalles = $conexion->query("SELECT id_detalle, medida, detalle, precio_unitario, codigo_detalle FROM detalle_producto WHERE codigo = '{$producto['codigo']}'");
                if ($detalles->num_rows > 0) {
                    while ($detalle = $detalles->fetch_assoc()) { ?>
                        <tr>
                            <td><span class="hora"><?php echo $detalle['codigo_detalle']; ?></span></td>
                            <td><?php echo $detalle['medida']; ?></td>
                            <td><?php echo $detalle['detalle']; ?></td>
                            <td><?php echo $detalle['precio_unitario']; ?></td>
                            <td>
                                <form action="actions/eliminar_detail_prod.php" class="formFunctions" method="post">
                                
                                    <button class="btn-load azul" type="button"  onclick="editarDetalleProd(`<?php echo $detalle['id_detalle']; ?>`, `<?php echo addslashes($detalle['precio_unitario']); ?>`, `<?php echo addslashes($detalle['medida']); ?>`, `<?php echo addslashes($detalle['detalle']); ?>`)">
                                      <span><i class="fa-solid fa-pencil"></i></span>
                                    </button>
                                            
                                            
                                    <input type="hidden" name="id_detalle" value="<?php echo $detalle['id_detalle']; ?>" readonly> 
                                    <button class="btn-load rojo centrarIcon" type="submit">
                                       <span><i class="fa-solid fa-trash"></i></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <!-- Fila con mensaje si no hay detalles -->
                    <tr>
                        <td colspan="4">
                            No se encontraron detalles para este producto.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>












<!-- Formulario de imágenes -->
<form action="actions/eliminar_imagenes.php" id="img_<?php echo $producto['codigo']; ?>" method="post" class="formStyle b-azul grande" onsubmit="return validarSeleccionImgs()">
    <div class="cabecera">
        <h2 class="f-med f-white"><?php echo $producto['codigo']; ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo $producto['nombre']; ?></h2>
        <button type="button" onclick="mostrarImagenes('<?php echo $producto['codigo']; ?>')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <p class="txtFormImgs">Selecciona imagenes para eliminar:</p>
    
    <div class="panelImgsTable">
                <?php
                $imagenes = $conexion->query("SELECT id_imagen, ruta_imagen FROM imagenes WHERE codigo = '{$producto['codigo']}'");
                while ($imagen = $imagenes->fetch_assoc()) {
                        $ruta_imagen = str_replace('../', '', $imagen['ruta_imagen']);
                        $idImagen = $imagen['id_imagen'];
                    ?>
                    <div class="imagenItem"> 
                        <img src="<?php echo $ruta_imagen; ?>" alt="Imagen del Producto" class="imgTblaSolo">
                        <input type="checkbox" name="seleccionarImagen[]" value="<?= $idImagen; ?>" class="checkboxItem">  
                    </div>
                <?php } ?>
    </div>
    <div class="containerBtns">
        <button type="submit" class="btn-load rojo" name="eliminarImagenes"><span><i class="fa-solid fa-trash"></i> <span class="f-peq">Eliminar imágenes</span></span></button>
    </div>
</form>
<?php } ?>
















<style>
    
    .formEmergente {
    overflow:hidden;
    }
    .txtFormImgs{
        padding:5px 35px;
        color:white;
    }
    .imagenItem{
        position:relative;
        margin:10px;
    }
    .imagenItem img{
        width:250px;
        height:200px;
        object-fit:cover;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.5);
        border-radius:7px;
    }
    .checkboxItem {
        position: absolute;
        top: 5px; 
        right: 5px; 
        z-index: 1; 
        width: 27px; 
        height: 27px; 
    }

    /*Estilo de las imagenes form*/
    .panelImgsTable{
    display:flex;
    padding:10px;
    overflow-x:auto;
    background:white;
    }

    .imgTblaSolo{
        width:150px;
        height:100px;
        object-fit:cover;
        margin:7px;
    }






/*Estilo del form detallles prod*/
.containerTableDetails{
    width:100%;
    padding:10px;
    padding-bottom:20px;
    box-sizing:border-box;
    max-height:65vh;
    overflow-y:auto;
}
.panelDatosTable{
    display:flex;
    gap:20px;
    justify-content:center;
    align-items:center;
    padding:20px;

}












.containerVistaPrevia {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  border: 1px solid #999;
  height: 200px;
  overflow: auto;
  border-radius: 5px;
  width: 96%;
  margin: 5px auto;
  background: rgb(255, 255, 255, 0.5);
  box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
}
.imagen-preview {
  position: relative;
  display: inline-block;
  background:orange;
  max-height: 100px;
}
.imagen-preview img {
  max-width: 100px;
  max-height: 100px;
  object-fit: cover;
}
.eliminar-imagen {
  position: absolute;
  top: 3px;
  right: 3px;
  background-color: red;
  color: white;
  border: none;
  padding: 5px;
  font-weight:600;
}
.eliminar-imagen:hover{
  color:yellow;
  background-color: rgb(255, 102, 0);
}
</style>
