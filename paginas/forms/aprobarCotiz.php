
<form action="actions/aprobarCotizacion-cobrar.php" class="formStyle b-azul grande" method="POST" id="formAprobarCotiz">
    <div class="cabecera">
      <h2 id="txt-formAprobarCotiz"></h2>
      <button type="button" onclick="plop('formAprobarCotiz')">
         <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <br>
    <input type="hidden" id="idVenta-aprobarCotiz" name="id_venta_cotiz" readonly>
    <input type="hidden" name="idVendedor" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>

    <section class="separador">
        <div class="mitad">
            <div class="elem2 column">
                <label class="f-peq f-white" for="ambiente_venta">Lugar de venta:<span class="asterisco">*</span></label>
                    <select class="select pd" name="ambiente_venta" id="ambiente_venta" required>
                        <option value="" selected disabled>Seleccione una opción</option>
                        <option value="Fabrica">Fábrica</option>
                        <option value="Tienda">Tienda</option>
                    </select>
            </div>
           <?php include 'component-venta-fechaEntrega.php'; ?>
        </div>
        <div class="mitad">
            <?php include 'component-cobro.php'; ?>
        </div>
    </section>

    <div class="contenedor-final">
       <p class="total-general f-white pad10">total venta: <span id="totalVenta-aprCotiz"></span> Bs</p>
      <button class="btn-load verde" style=" margin: 0 auto;" id="btn1-formCliente" name="action" value="registrar" type="submit"><span>Aprobar cotización</span></button>
    </div>
</form>