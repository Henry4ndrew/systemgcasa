
<form action="actions/realizarCobro.php" class="formStyle b-azul grande" method="POST" id="formCobro">
    <div class="cabecera">
      <h2 id="txt-formCobro">Realizar cobro</h2>
      <button type="button" onclick="plop('formCobro')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
      <input type="hidden" name="idVendedor" id="idVendedor" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>
    <br>

     <section class="separador campoForm">
         <div class="mitad">
            <?php include 'component-cobro.php'; ?>
         </div>
         <div class="mitad">
              <div id="datos-venta-cobro" class="datos-venta-info">
                  <!-- Los datos se insertarán dinámicamente aquí -->
               </div>
         </div>
      </section>

     <!-- Al realizar cobro es necesaro que vea uan de las 2 acciones, en el form acordeon enviar un input hidden con action=registrar -->
    <section class="containerBtns">
        <button id="btn1-formCobro" class="btn-load verde" type="submit" name="action" value="registrar"><span>Realizar cobro</span></button>
        <button id="btn2-formCobro" class="btn-load azul" type="submit" name="action" value="editar" style="display:none;"><span>Guardar edición</span></button>
    </section>
</form>



