<div class="elem2 column radio-group">
    <label class="f-white">¿La venta realizada tiene una fecha de entrega?<span class="a">*</span></label>
    <div>
        <input type="radio" id="fechaEntregaSi" name="fechaEntregaPregunta" value="si" onclick="gestionarFechaEntrega()" required>
        <label class="f-white" for="fechaEntregaSi">Sí</label>
    </div>
    <div>
        <input type="radio" id="fechaEntregaNo" name="fechaEntregaPregunta" value="no" onclick="gestionarFechaEntrega()" required>
        <label class="f-white" for="fechaEntregaNo">No</label>
    </div> 
</div>
<div class="elem2 column" id="campo-fechaEntrega" style="display:none;">
    <label class="f-peq f-white" for="fechaEntrega">Fecha de entrega:</label>
    <input class="input pd" type="date" id="fechaEntrega" name="fechaEntrega">
</div>