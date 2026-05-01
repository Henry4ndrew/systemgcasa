    <form action="actions/guardarCuentaBank.php" method="post" class="formStyle mediano b-azul" id="formBank" enctype="multipart/form-data">
        <div class="cabecera">
            <h2 id="txtFormUser">Agregar cuenta de Banco</h2>
            <button type="button" onclick="plop('formBank')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <br>
         <div class="elem2 column">
            <label class="f-peq f-white" for="titularCuenta">Nombre del titular:</label>
            <input class="input pd" type="text" id="titularCuenta" name="titularCuenta" placeholder="Ingrese el nombre del titular" required>
        </div>
           <div class="elem2 column">
            <label class="f-peq f-white" for="numeroCuenta">Número de cuenta:</label>
            <input class="input pd" type="text" id="numeroCuenta" name="numeroCuenta" placeholder="Ingrese el número de cuenta" required>
         </div>
         <div class="elem2 column">
            <label class="f-peq f-white" for="nombreBanco">Nombre del banco:</label>
            <input class="input pd" type="text" id="nombreBanco" name="nombreBanco" placeholder="Ingrese el nombre del banco" required>
         </div>
           <div class="elem2 column">
            <label class="f-peq f-white" for="imagenQR">Imagen QR:</label>
            <input class="select pd" type="file" id="imagenQR" name="imagenQR" accept="image/*" required>
         </div>
           <div class="elem2 column">
            <label class="f-peq f-white" for="fechaCaducidadQR">Fecha de caducidad QR:</label>
            <input class="input pd" type="date" id="fechaCaducidadQR" name="fechaCaducidadQR" required>
         </div>
        <div class="containerBtns">
            <button class="btn-load verde" type="submit"><span>Guardar</span></button>
        </div>
    </form>