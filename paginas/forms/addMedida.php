<form action="actions/add_medida.php" class="formStyle b-azul pequeno" id="formMedida" method="POST">
    <div class="cabecera">
        <h2 class="f-med" id="txtFormUser">Agregar medida</h2>
        <button type="button" onclick="plop('formMedida')">
             <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <br>
    <div class="elem2 column">
        <label class="f-peq f-white" for="medida_disp">Medida:</label>
        <input  class="input pd" type="text" name="medida_disp" id="medida_disp" required>
    </div>

    <section class="containerBtns">
       <button id="btnAddMedida" class="btn-load verde" type="submit"><span>Agregar medida</span></button>
    </section>
</form>
