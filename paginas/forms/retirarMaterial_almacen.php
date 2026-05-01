<form action="actions/eliminarCant_material.php" method="post" class="formStyle b-azul grande" id="formRetirarMateriaPrima">
    <div class="cabecera">
      <h2>Retirar materia prima</h2>
      <button type="button" onclick="plop('formRetirarMateriaPrima')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    
    <input type="hidden" name="idVendedor2" id="idVendedor2" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>
  
    <section class="campoForm">
        <div class="flex-between centrar">
            <div class="input-wrapper" style="margin-bottom:7px;">
                <input class="input padInput" type="text" id="search-materials2" oninput="buscarMateriaPrimaAlmacen('search-materials2', 'lista-materia-prima2')" onfocus="mostrarLista('lista-materia-prima2')" onblur="ocultarLista('lista-materia-prima2')" placeholder="Ingrese nombre de material">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="elem2 column" style="max-width:200px;">
                <label class="f-white f-peq" for="responsableRetiro">Responsable:</label>
                <input type="text" class="input pd rad7" name="responsableRetiro" id="responsableRetiro" placeholder="Responsable del retiro">
            </div>

            <div class="elem2 column" style="max-width:300px;">
                <label class="f-peq f-white" for="notaMaterialEliminado">Nota:</label>
                <textarea class="input pd rad7" name="notaMaterialEliminado" id="notaMaterialEliminado" rows="1"></textarea>
            </div>
        </div>
         <!-- Estas son las opciones de materiales a elegir -->
        <div class="item-material listaFlotanteProductos" id="lista-materia-prima2"></div>
        
        <div id="lista-materiales-agregados2" class="listaMateriales">
            <!-- Este es el panel donde se agregan los materiales -->
        </div>
    </section> <!--fin campo Form-->
    <section class="containerBtns">
        <button class="btn-load rojo" type="submit" name="action" value="eliminar"><span>Eliminar del almacén</span></button>
    </section>
</form>


<style>
#lista-materia-prima2{
    display:none;
}


</style>