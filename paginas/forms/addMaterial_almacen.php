<form action="actions/addCant_material.php" method="post" class="formStyle b-azul grande" id="formAddMateriaPrima">
    <div class="cabecera">
      <h2 id="txt-formAddMateriaPrima">Agregar materia prima</h2>
      <button type="button" onclick="plop('formAddMateriaPrima')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
        
    <!-- Este dato se inserta en la columna id_user -->
    <input type="hidden" name="idVendedor3" id="idVendedor3" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>


    <section class="campoForm">
        <div class="flex-between">
            <div class="input-wrapper" style="margin-bottom:7px;">
                <input class="input padInput" type="text" id="search-materials" oninput="buscarMateriaPrimaAlmacen('search-materials', 'lista-materia-prima')" onfocus="mostrarLista('lista-materia-prima')" onblur="ocultarLista('lista-materia-prima')" placeholder="Ingrese nombre de material">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <!-- Este dato se inserta en la columna nota -->
            <div class="elem2 column" style="max-width:300px;">
                <label class="f-peq f-white" for="notaMaterialAgregado">Nota:</label>
                <textarea class="input pd rad7" name="notaMaterialAgregado" id="notaMaterialAgregado" rows="2"></textarea>
            </div>
        </div>
         <!-- Estas son las opciones de materiales a elegir -->
        <div class="item-material listaFlotanteProductos" id="lista-materia-prima"></div>
        
        <div id="lista-materiales-agregados" class="listaMateriales">
            <!-- Este es el panel donde se agregan los materiales -->
        </div>
    </section> <!--fin campo Form-->
    <section class="containerBtns">
        <button class="btn-load verde" type="submit" name="action" value="registrar"><span>Agregar al almacén</span></button>
    </section>
</form>


<style>
#lista-materia-prima{
    display:none;
}


</style>