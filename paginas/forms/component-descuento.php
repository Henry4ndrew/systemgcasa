<div class="elem2 column">
        <label class="f-peq f-white" for="tipoDescuento">Tipo de descuento</label>
        <select class="select pd" id="tipoDescuento" name="tipoDescuento" onchange="mostrarDesc('inputMonto','inputPorcentaje')">
            <option value="">Seleccionar solo si existe descuento</option>
            <option value="monto">Bs</option>
            <option value="porcentaje">%</option>
        </select>
</div>
<div class="elem2 column">
    <label class="f-peq f-white">Monto a descontar:</label>
    <input class="input pd" type="number" id="inputMonto" name="descuentoMonto" placeholder="Monto Bs" style="display: none;" oninput="calcularTotalconDescuento();">  
    <input class="input pd" type="number" id="inputPorcentaje" name="descuentoPorcentaje" placeholder="porcentaje" style="display: none;" oninput="calcularTotalconDescuento();">
</div>