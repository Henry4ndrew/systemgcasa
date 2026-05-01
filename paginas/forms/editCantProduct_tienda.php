<form action="actions/editCant_product_tienda.php" method="post" id="formCantProductTienda" class="formStyle mediano b-azul">
  <div class="cabecera">
     <h2>Editar cantidad - Tienda</h2>
     <button type="button" onclick="plop('formCantProductTienda')">
        <i class="fa-solid fa-xmark"></i>
     </button>
  </div>
  <section class="campoForm">
    <div class="elem2 column">
        <label class="f-peq f-white">Producto:</label>
        <div id="producto-seleccionado" class="itemMaterial pad10 f-peq item-agregado">
    </div>
    <input type="text" id="materia-idAlmacen" name="idalmacen" hidden readonly>
    <div class="elem3" style="justify-content:center;">
        <label class="f-peq f-white">Cantidad:</label>
        <input type="text" name="cantidad" id="cantidad-prod-actual" class="soloInput f-center input-cantidad" oninput="soloNumeros2()">
    </div>
  </section>
  <section class="containerBtns">
        <button type="submit" class="btn-load azul"><span>Guardar edición</span></button>
  </section>
</form>
<style>
.img-peq{
  width:80px; height:80px; object-fit:cover; margin:auto 0;
}
</style>