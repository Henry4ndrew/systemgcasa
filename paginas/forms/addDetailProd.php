<?php

$medidas = [];
$sql = "SELECT id, medida_disp FROM medida ORDER BY medida_disp";
$result = $conexion->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $medidas[] = $row; 
    }
}
?>

<form action="actions/gestionar_details_prod.php" class="formStyle b-azul mediano" id="formDetailProd" method="POST" style="z-index:6;">
    <div class="cabecera">
      <h2 class="f-med f-center f-white" id="txt-formDetailProd">Agregar detalles al producto</h2>
      <button type="button" onclick="plop('formDetailProd')">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <input type="text" name="id_prod" id="id_prod_detail" hidden readonly>
    <br>
    <div class="elem2 column">
        <label class="f-peq f-white" for="medida_disp">Medida:<span class="asterisco">*</span></label>
        <select class="select pd" name="medida_disp" id="medida_disp" required>
            <option value="">Seleccione una opción</option> <!-- Opción predeterminada -->
            <?php if (empty($medidas)) { ?>
                <option value="">No hay medidas disponibles</option>
            <?php } else {
                // Mostrar las opciones del combo box
                foreach ($medidas as $medida) {
                    echo "<option value='{$medida['medida_disp']}'>{$medida['medida_disp']}</option>";
                }
            } ?>
        </select>
    </div>
    <div class="elem2 column">
        <label class="f-peq f-white" for="detail_prod">Detalle:</label>
         <textarea class="input pd rad7" id="detail_prod" name="detail_prod"></textarea>
    </div>
    <div class="elem2 column">
      <label class="f-peq f-white" for="price_prod">Precio unitario:<span class="asterisco">*</span></label>
      <input class="input pd" type="text" oninput="soloNumeros('price_prod')" id="price_prod" name="price_prod" required>
    </div>
    <section class="containerBtns">
        <button id="btn1-formDetailProd" class="btn-load verde" type="submit" name="action" value="registrar" style="display:block"><span>Registrar medida</span></button>
        <button id="btn2-formDetailProd" class="btn-load azul" type="submit" name="action" value="editar" style="display:none"><span>Guardar edición</span></button>
    </section>
</form>