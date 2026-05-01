
<form action="actions/addCant_product.php" method="post" class="formStyle b-azul grande" id="formAddProdAlmacen">
    <div class="cabecera">
      <h2>Agregar productos al almacén</h2>
      <button type="button" onclick="plop('formAddProdAlmacen')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <section class="campoForm">
    <div class="input-wrapper" style="margin-bottom:7px;">
      <input class="input padInput" type="text" id="search-products3" oninput="buscarProductDetails();"onfocus="mostrarLista('list-prod-details')" onblur="ocultarLista('list-prod-details')" placeholder="Ingrese nombre de producto">
      <i class="fa-solid fa-magnifying-glass"></i>
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
