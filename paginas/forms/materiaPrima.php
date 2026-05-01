<form action="actions/gestionar_materiaPrima.php" class="formStyle b-azul grande" id="formMateria" method="POST" enctype="multipart/form-data">
    <div class="cabecera">
        <h2 class="f-med f-white" id="txt-formMateria">Crear materia prima</h2>
        <button type="button" onclick="plop('formMateria')">
             <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <br>
    <div class="separador campoForm">
        <div class="mitad">
            <input type="hidden" name="id_material" id="id_material">
            <div class="elem2 column">
                <label class="f-peq f-white" for="codigo_material">Codigo de material:</label>
                <input class="input pd" id="codigo_material" type="text" name="codigo_material" required>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white" for="nombre_material">Nombre del material:</label>
                <input class="input pd" type="text" name="nombre_material" id="nombre_material" placeholder="Ej: Tela, Fibra" required>
            </div>
            <div class="elem centrar">
                    <label for="imagenMaterial" id="areaImgMaterial" class="square column centrar f-white">
                        <i class="fa-solid fa-image"></i>Seleccionar imagen 
                    </label>
                <input type="file" id="imagenMaterial" name="imagenMaterial" style="display:none;" onchange="previsualizarImagen('imagenMaterial', 'areaImgMaterial')" accept="image/*">
            </div>
        </div> <!--fin mitad-->
        <div class="mitad">
             <div class="elem2 column">
                <label class="f-peq f-white" for="tipo_medida">Tipo de medida:</label>
                <select class="select pd" name="tipo_medida" id="tipo_medida" onchange="crearInputMedida('tipo_medida', 'mostrarMaterial_medida')" required>
                    <option value="">Seleccionar una medida</option>
                    <option value="unidad">Unidad</option>
                    <option value="longitud">Longitud</option>
                    <option value="metro_cuadrado">Metro cuadrado</option>
                </select>
            </div>
            <div id="mostrarMaterial_medida"></div>
        </div>
    </div> <!--fin separador-->

    <section class="containerBtns">
        <button id="btn1-formMateria" class="btn-load verde" type="submit" name="action" value="registrar"><span>Registrar material</span></button>
        <button id="btn2-formMateria" class="btn-load azul" type="submit" name="action" value="editar"><span>Guardar edición</span></button>
    </section>
</form>

