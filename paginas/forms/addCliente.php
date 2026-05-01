
<form action="actions/gestionarCliente.php" class="formStyle b-azul grande" method="POST" id="formCliente">
    <div class="cabecera">
      <h2 id="txt-formCliente">Registrar cliente</h2>
      <button type="button" onclick="plop('formCliente')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <br>

    <section class="campoForm">
    
      <?php include 'component-cliente.php'; ?>
       
    </section>

    <section class="containerBtns">
      <button class="btn-load verde" id="btn1-formCliente" name="action" value="registrar" type="submit"><span>Registrar cliente</span></button>
      <button class="btn-load azul" id="btn2-formCliente" style="display:none" name="action" value="editar" type="submit"><span>Guardar edición</span></button>
    </section>
</form>



