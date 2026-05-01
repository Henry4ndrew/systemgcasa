
  <input type="hidden" name="id_pago" id="id_pago"> <!--para realizar una edición-->

    <input type="hidden" name="id_venta" id="id_venta_cobro"> 

      <div class="elem2 column">
          <label class="f-peq f-white" for="tipoPago">Tipo de pago: <span class="a">*</span></label>
          <select class="select pd" id="tipoPago" name="tipoPago" required>
              <option value="" disabled selected>Seleccione una opción</option>
              <option value="efectivo">Efectivo</option>
              <option value="qr">QR</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="billetera_movil">Billetera móvil (consume lo nuestro)</option>
              <option value="otro">Otro</option>
          </select>
      </div>
      <div class="flex-between" style="width:96%; margin:auto;">
        <div class="column s-elem w46">
          <label class="f-peq f-white" for="anticipoVenta">Anticipo:<span class="a">*</span></label>
          <input class="input pd" type="number" id="anticipoVenta" name="anticipoVenta" oninput="calcularSaldo('anticipoVenta','saldo-cobro')" step="0.01" min="0" required>
        </div>
        <div class="column s-elem w46">
          <label class="f-peq f-white">Cambio:</label>
          <h3 class="f-white" id="cambioCobro">0</h3>
        </div>
      </div>
      <div class="elem2 column">
          <label class="f-peq f-white" for="fechaSigPago">Fecha Sig. pago:</label>
          <div style="display:flex; gap:10px;" id="campoFechaSigPago">
              <input type="date" class="input pd" id="fechaSigPago" name="fechaSigPago" required>
              <button type="button" class="btn-load gold" onclick="limpiarFecha('fechaSigPago')"><span><i class="fa-solid fa-rotate-right"></i></span></button>
          </div>
      </div>
      <div class="elem2 column">
          <label class="f-peq f-white" class="f-peq f-white">Saldo:</label>
          <input class="input-none f-white f-grande f-center" type="text" name="saldo-cobro" id="saldo-cobro" readonly required>
      </div>


<style>
  .s-elem{
    padding:7px;
  background:rgba(15, 6, 54, 0.4);
  border-radius:var(--rad);
  margin-bottom:5px;
  }
</style>