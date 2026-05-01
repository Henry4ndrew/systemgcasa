<?php 
    require 'includes/conexion.php';

    $sql = "SELECT * FROM medida";
    $result = $conexion->query($sql);

    $sqlAmbiente = "SELECT * FROM ambiente";
    $resultAmbiente = $conexion->query($sqlAmbiente);

    $stockMin= "SELECT stockMinimo FROM datos LIMIT 1";
    $resultStockMin = $conexion->query($stockMin);
    if ($resultStockMin && $row = $resultStockMin->fetch_assoc()) {
        $stock_minimo = $row['stockMinimo'];
    } else {
        $stock_minimo = 0;
    }
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addAmbiente.php'; ?>
<?php include 'forms/addMedida.php'; ?>

<h3 class="b-naranja f-white pad-left20" style="min-width:500px;">Agregar opciones</h3>


    <!-- Bloque 1 -->
    <form action="actions/stockMinimo.php" method="post" class="bloqueSolo b-azul pad20 column centrar gap05" style="margin:10px auto; display:none;">
        <p class="f-white f-peq centrar gap05"><i class="fa-solid fa-bell f-grande f-gold"></i>  Cant. mínima para notificar en los Almacenes stock</p>
        <br>
        <div style="display:flex; gap:10px">
            <h2 class="f-gold f-med">Stock Minimo:</h2> <input type="text" class="input f-center" style="width:75px;" id="input-sotckMinimo" oninput="soloNumeros('input-sotckMinimo')" name="stockMinimo" value="<?php echo $stock_minimo; ?>" class="miniInput"> <button type="submit" class="btn-load gold"><i class="bi bi-bell"></i><span>Actualizar</span></button>
        </div>
    </form>
 <!--fin de los 2 bloques ===========================-->




















<div class="cont-2elem f-justify">

        <div class="bloqueSolo b-azul pad20" style="display:none">
            <div class="centrar column pad20 gap05">
                <h2 class="f-white">Ambientes de venta</h2>
                <button type="button" class="btn-load gold" onclick="plop('formAmbiente')">
                <span><i class="fa-solid fa-house"></i> Agregar</span>
                </button>
           </div>

            <table class="tablaStyle">
                <thead>
                    <tr>
                        <th>Ambiente</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultAmbiente->num_rows > 0) {
                        while ($row = $resultAmbiente->fetch_assoc()) { 
                            $id_lugar = $row['id_lugar'];
                            $lugar_disp = $row['lugar'];
                            echo "<tr>
                                    <td>{$lugar_disp}</td>
                                    <td>
                                        <form action='actions/eliminar_ambiente.php' method='POST' class='centrar'>
                                            <input type='hidden' name='id_lugar' value='{$id_lugar}'>
                                            <button type='submit' class='btn-load rojo' onclick='return confirm(\"¿Estás seguro de eliminar este ambiente?\")'>
                                               <span><i class='fa-solid fa-trash'></i></span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No se encontraron ambientes.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
    </div>

            






        <div class="bloqueSolo b-azul pad20">
            <div class="centrar column pad20 gap05">
                <h2 class="f-white">Medidas (Detalle prod.)</h2>
                <button type="button" class="btn-load gold" onclick="plop('formMedida')">
                <span><i class="fa-solid fa-tags"></i> Agregar medida</span>
            </button>
           </div>

         <div style="max-height:50vh; overflow-y:auto; padding:15px;">
            <table class="tablaStyle">
                <thead>
                    <tr>
                        <th>Medida</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Mostrar cada fila
                        while ($row = $result->fetch_assoc()) {
                            $medida_id = $row['id'];
                            $medida_disp = $row['medida_disp'];
                            echo "<tr>
                                    <td>{$medida_disp}</td>
                                    <td>
                                        <form action='actions/eliminar_medida.php' method='POST' class='centrar'>
                                            <input type='hidden' name='id' value='{$medida_id}'>
                                            <button type='submit' class='btn-load rojo'  onclick='return confirm(\"¿Estás seguro de eliminar esta medida?\")'><span><i class='fa-solid fa-trash'></i></span></button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No se encontraron medidas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>






        
</div>