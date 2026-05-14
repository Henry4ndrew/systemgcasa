<?php
require 'includes/conexion.php';
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

<form action="actions/addCant_product_feria.php" method="post" class="formStyle b-azul grande" id="formAddProdAlmacenTienda">
    <div class="cabecera">
      <h2>Agregar productos al almacén - Feria</h2>
      <button type="button" onclick="plop('formAddProdAlmacenTienda')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <section class="campoForm">
    <div class="input-wrapper" style="margin-bottom:7px;">
      <input class="input padInput" type="text" id="search-products3" oninput="buscarProductDetails()" onfocus="mostrarLista('list-prod-details')" onblur="ocultarLista('list-prod-details')" placeholder="Ingrese nombre de producto">
      <i class="fa-solid fa-magnifying-glass"></i>

                    <div class="elem2 column mitad" id="contenedor-feria">
                        <label class="f-peq f-white" for="feria_select">
                            Nombre de feria:<span class="a">*</span>
                        </label>

                        <select class="select pd" name="feria_id" id="feria_select" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <?php foreach ($ferias as $feria): ?>
                                <option value="<?= $feria['id_feria']; ?>">
                                    <?= $feria['nombre_feria']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
    </div>
    <div class="item-material listaFlotanteProductos" id="list-prod-details"></div>

    <div id="lista-prod-agregados" class="listaMateriales">
        <!-- Este es el panel donde se agregan los productos -->
    </div>
    
    </section> <!--fin campo Form-->
    <section class="containerBtns">
        <button class="btn-load verde" type="submit" name="action" value="registrar"><span>Agregar al almacén</span></button>
    </section>
</form>


