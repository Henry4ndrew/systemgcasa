<input type="hidden" name="id_cliente" id="id_cliente" readonly>
<div class="separador">
    <div class="mitad">
        <div class="elem2 column">
            <label for="nameCliente" class="f-peq f-white">Nombre:<spam class="a">*</spam></label>
            <input class="input pd" type="text" name="nameCliente" id="nameCliente" required>
        </div>
        <div class="elem2 column">
            <label for="empresaCliente" class="f-peq f-white">Empresa:</label>
            <input class="input pd" type="text" id="empresaCliente" name="empresaCliente">
        </div>
        <div class="elem2 column">
            <label for="nit" class="f-peq f-white">NIT:</label>
            <input class="input pd" type="text" name="nit" id="nit">
        </div>
        <div class="elem2 column">
            <label for="carnetCliente" class="f-peq f-white">Carnet C.I:</label>
            <input class="input pd" type="text" name="carnetCliente" id="carnetCliente" placeholder="Documento de identidad">
        </div>
        <div class="elem2 column">
            <label for="departamento" class="f-peq f-white">Departamento:<spam class="a">*</spam></label>
            <select class="select pd" id="departamento" name="departamento" required>
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="la paz">La Paz</option>
                <option value="santa cruz">Santa Cruz</option>
                <option value="cochabamba">Cochabamba</option>
                <option value="tarija">Tarija</option>
                <option value="potosi">Potosí</option>
                <option value="sucre">Sucre</option>
                <option value="oruro">Oruro</option>
                <option value="beni">Beni</option>
                <option value="pando">Pando</option>
            </select>
        </div>
    </div>
    <div class="mitad">
        <div class="elem2 column">
            <label for="correo" class="f-peq f-white">Correo electrónico:</label>
            <input class="input pd" type="email" id="correo" name="correo" placeholder="Ej: correo_@gmail.com">
        </div>
        <div class="elem2 column">
            <label for="celCliente" class="f-peq f-white">Celular personal:</label>
            <input class="input pd" type="number" name="celCliente" id="celCliente">
        </div>
        <div class="elem2 column">
            <label for="celEmpresa" class="f-peq f-white">Celular de empresa:</label>
            <input class="input pd" type="number" name="celEmpresa" id="celEmpresa">
        </div>
        <div class="elem2 column">
            <label for="detailCLienteProd" class="f-peq f-white">Nota:</label>
            <textarea class="input pd rad7" id="detailCLienteProd" name="note_client" placeholder="Nota acerca del cliente" rows="6" maxlength="1000"></textarea>
        </div>
    </div>
</div>