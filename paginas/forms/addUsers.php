
<form action="actions/gestionarUsuarios.php" class="formStyle grande b-azul" id="formUser" onsubmit="return validarFormConstrasena()" method="POST" enctype="multipart/form-data">
    <div class="cabecera">
      <h2 id="txt-formUser">Registrar usuario</h2>
      <button type="button" onclick="plop('formUser')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <input type="text" name="idUser" id="idUser" hidden readonly>
<br>
<section class="separador campoForm">
    <div class="mitad">
        <div class="elem2 column">
            <label class="f-peq f-white">Usuario:<span class="a">*</span></label>
            <input type="text" class="input pd" name="nameUser" id="nameUser" placeholder="Nombre de usuario..." required>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Contraseña:<span class="a">*</span></label>
            <input type="password" class="input pd" name="passwordUser" id="passwordUser" placeholder="Para entrar al sistema" required>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Confirmación:<span class="a">*</span></label>
            <input type="password" class="input pd" name="confirmPasswordUser" id="confirmPasswordUser" placeholder="Confirma tu contraseña" required>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white" for="permiso">Permiso:<span class="a">*</span></label>
            <select  class="select pd" name="permiso" id="permiso" required>
                <option value="">Seleccione una opción</option>
                <option value="ventas">Ventas</option>
                <option value="administrador">Administrador</option>
            </select>
        </div>
    </div>
    <div class="mitad col-rev">
        <div class="elem2 column centrar">
           <i class="fa fa-user iconSolo f-white centrar"></i>
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Celular:</label>
            <input type="number" class="input pd" name="celUser" id="celUser">
        </div>
    </div>
</section>
    <section class="containerBtns">
        <button id="btn1-formUser" class="btn-load verde" type="submit" name="action" value="registrar" style="display:block"><span>Registrar usuario</span></button>
        <button id="btn2-formUser" class="btn-load azul" type="submit" name="action" value="editar" style="display:none"><span>Guardar edición</span></button>
    </section>
</form>


