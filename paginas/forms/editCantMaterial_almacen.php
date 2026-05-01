<form action="actions/editCant_material.php" method="post" id="formCantMateria" class="formStyle mediano b-azul">
  <div class="cabecera">
     <h2>Editar cantidad</h2>
     <button type="button" onclick="plop('formCantMateria')">
        <i class="fa-solid fa-xmark"></i>
     </button>
  </div>
   <input type="hidden" name="idVendedor4" id="idVendedor4" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>
  <section class="campoForm">
    <div class="elem2 column">
        <label class="f-peq f-white">Material:</label>
        <div id="materia-prima-seleccionada"></div>
    </div>
    <input type="hidden" id="materia-idAlmacen" name="idalmacen" readonly>
    <div class="elem3" style="justify-content:center;">
        <label class="f-peq f-white">Cantidad:</label>
        <input type="hidden" name="cantidadAnterior" id="cantidad-m-anterior" class=" f-center input-cantidad" readonly>
        <input type="text" name="cantidad" id="cantidad-m-actual" class="soloInput f-center input-cantidad" oninput="soloNumeros2()">
    </div>
  </section>
  <section class="containerBtns">
        <button type="submit" class="btn-load azul"><span>Guardar edición</span></button>
  </section>
</form>
