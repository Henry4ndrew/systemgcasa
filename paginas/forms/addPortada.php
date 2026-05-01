
<form action="actions/gestionarPortada.php" class="formStyle mediano b-azul" id="formPortada" method="POST" enctype="multipart/form-data" onsubmit="return validarImagen('imagen', 'btn1-formPortada', 'btn2-formPortada')">
    <div class="cabecera">
        <h2 id="txt-formPortada">Agregar portada</h2>
        <button type="button" onclick="plop('formPortada')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
     <br>
        <input type="hidden" id="idPortada" name="idPortada" readonly>
        <div class="separador">
            <div class="mitad">
                <div class="elem centrar">
                        <label for="imagen" id="areaImg" class="square column centrar f-white">
                            <i class="fa-solid fa-image"></i>Seleccionar imagen *
                        </label>
                    <input type="file" id="imagen" name="imagen" style="display:none;" onchange="previsualizarImagen('imagen', 'areaImg')" accept="image/*">
                </div>
            </div>
            <div class="mitad">
                <div class="elem2 column">
                    <label class="f-peq f-white" for="title">Título:</label>
                    <input class="input pd" id="title" type="text" name="title" placeholder="Título">
                </div>
                <div class="elem2 column">
                    <label class="f-peq f-white" for="desc">Descripción:</label>
                    <textarea class="input pd rad7" id="desc" name="desc" rows="4" maxlength="1000"></textarea>
                </div>
            </div>
        </div>
 
    <section class="containerBtns">
         <button type="submit" id="btn1-formPortada" name="action" value="guardar" class="btn-load verde"><span>Guardar Portada</span></button>
         <button type="submit" id="btn2-formPortada" name="action" value="editar" class="btn-load azul" style="display:none"><span>Guardar edición</span></button>
    </section>
</form>
