


<form action="actions/gestionarProducto.php" class="formStyle b-azul grande" id="formProducto" method="POST" enctype="multipart/form-data">
    <div class="cabecera">
      <h2 id="txt-formProducto">Agregar producto</h2>
      <button type="button" onclick="plop('formProducto')">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <br>
    <!--Al editar aquí se envía el codigo anterior-->
    <input type="text" name="idProd" id="idProd" hidden readonly>

  <section class="separador campoForm">
    <div class="mitad">
       <div class="elem2 column">
          <label class="f-peq f-white">Codigo:</label>
              <!--Al editar aquí se recibe el codigo nuevo-->
          <input class="input pd" type="text" name="codeProd" id="codeProd" required>
       </div>
       <div class="elem2 column">
          <label class="f-peq f-white">Nombre:</label>
          <input  class="input pd" type="text" name="nameProd" id="nameProd" required>
      </div>
      <div class="elem2 column">
        <label class="f-peq f-white" for="categProd">Categoria:</label>
        <select class="select pd" id="categProd" name="categProd" required>
          <option value="default" selected>Seleccione una opción</option>
          <option value="hotelera">Hotelera</option>
          <option value="hogar">Hogar</option>
          <option value="hospitalaria">Hospitalaria</option>
          <option value="institucional">Institucional</option>
          <option value="otros">Otros</option>
        </select>
      </div>
      <div class="elem2 column">
        <label class="f-peq f-white" for="charProd">Características:</label>
        <textarea class="input pd rad7" id="charProd" name="charProd" placeholder="Aquí van las características" rows="6" maxlength="1000"></textarea>
      </div>  
    </div>
    <div class="mitad">   
            <div class="elem2 column centrar">
                <p class="f-peq f-gold" style="margin:0;">Imagenes permitidas: .png, .jpg, jpeg</p>
                <label for="imagen" id="areaSelectorImg" class="btn-load gold">
                    <span class="f-peq"><i class="fas fa-file-image f-med"></i> Agregar imagenes</span>
                </label>
                <input type="file" id="imagen" name="imagen[]" style="display:none;" onchange="previsualizarImagenes()" multiple accept=".jpg,.jpeg,.png,image/jpeg,image/png">
            </div> 

            <div id="imagePreviewContainer" class="containerVistaPrevia"></div>  

            <div class="elem2 column">
            <label class="f-peq f-white" for="disponible">Tienda virtual:</label>
            <select class="select pd" id="disponible" name="disponible" required>
                <option value="" selected>Seleccione una opción</option>
                <option value="si">si</option>
                <option value="no">no</option>
            </select>
        </div>    
    </div>
  </section>
    <section class="containerBtns">
        <button id="btn1-formProducto" class="btn-load verde" name="action" value="registrar" type="submit">
            <span>Registrar producto</span>
        </button>
        <button id="btn2-formProducto" class="btn-load azul" name="action" value="editar" type="submit" style="display:none">
            <span>Guardar edición</span>
        </button>
     </section>
</form>

  











