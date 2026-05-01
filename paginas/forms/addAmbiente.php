
<form action="actions/add_ambiente.php" class="formStyle b-azul pequeno" id="formAmbiente" method="POST">
    <div class="cabecera">
        <h2 class="f-med" id="txtFormUser">Ambiente de venta</h2>
        <button type="button" onclick="plop('formAmbiente')">
             <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <br>
    <div class="elem2 column">
        <label class="f-peq f-white" for="ambiente">Ambiente:</label>
        <input type="text" class="input pd" name="ambiente" id="ambiente" required>
    </div>

    <section class="containerBtns">
        <button id="btnAddMedida" class="btn-load verde" type="submit"><span>Agregar ambiente</span></button>
    </section>
</form>
