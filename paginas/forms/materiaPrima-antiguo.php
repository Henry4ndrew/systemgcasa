<form action="actions/gestionar_materiaPrima.php" class="formStyle b-azul mediano" id="formMateria" method="POST">
    <div class="cabecera">
        <h2 class="f-med f-white" id="txt-formMateria">Crear materia prima</h2>
        <button type="button" onclick="plop('formMateria')">
             <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <p class="f-white f-center" style="margin:5px; font-size:0.7rem;">Ej: 1 rollo de Tela contiene 130 metros</p>
    <div class="separador">
        <div class="mitad">
            <input type="text" name="id_material" id="id_material" hidden>
            <div class="elem2 column">
                <label class="f-peq f-white" for="medida_material">Unidad de medida:</label>
                <input class="input pd" type="text" name="medida_material" id="medida_material" placeholder="Ej: Rollo, Galón, Cono, Juego" required>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white" for="nombre_material">Nombre del material: <span class="f-peq-1">(Nombre único)</span></label>
                <input class="input pd" type="text" name="nombre_material" id="nombre_material" placeholder="Ej: Tela, Fibra" required>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white" for="contiene_material">Contiene:</label>
                <input class="input pd" oninput="soloNumeros('contiene_material')" type="text" placeholder="Dato numérico" name="contiene_material" id="contiene_material" required>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white" for="medida_contenido_material">Medida del contenido:</label>
                <input class="input pd" type="text" name="medida_contenido_material" id="medida_contenido_material" placeholder="Ej: Metros, Kilos, litros" required>
            </div>
        </div> <!--fin mitad-->
        <div class="mitad">
            aqui va un label para imagen
        </div>
    </div> <!--fin separador-->

    <section class="containerBtns">
        <button id="btn1-formMateria" class="btn-load verde" type="submit" name="action" value="registrar"><span>Registrar material</span></button>
        <button id="btn2-formMateria" class="btn-load azul" type="submit" name="action" value="editar"><span>Guardar edición</span></button>
    </section>
</form>

