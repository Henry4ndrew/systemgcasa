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



    $sqlFerias = "SELECT * FROM ferias ORDER BY nombre_feria ASC";
    $resultFerias = $conexion->query($sqlFerias);

    $ferias = [];
    if ($resultFerias && $resultFerias->num_rows > 0) {
        while ($row = $resultFerias->fetch_assoc()) {
            $ferias[] = $row;
        }
    }
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addAmbiente.php'; ?>
<?php include 'forms/addMedida.php'; ?>
<form action="actions/gestionarFerias.php" method="post" class="formStyle b-azul mediano" id="formFerias">
    <div class="cabecera">
      <h2 class="f-white" id="txt-formFerias">Formulario de ferias</h2>
      <button type="button" onclick="plop('formFerias')">
            <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <input type="hidden" id="id-feria" name="id_feria" readonly>
    <br>
    <section class="separador campoForm">
           <div class="elem2 column">
             <label class="f-peq f-white" for="nombre_feria">Nombre de la feria:</label>
             <input class="input pd" type="text" id="nombre_feria" name="nombre_feria" placeholder="Ingrese el nombre de la feria" required>
          </div>
    </section>
    <section class="containerBtns">
         <button class="btn-load verde" id="btn1-formFerias" name="action" value="registrar" type="submit"><span>Registrar feria</span></button>
         <button class="btn-load azul" id="btn2-formFerias" style="display:none" name="action" value="editar" type="submit"><span>Guardar edición</span></button>
    </section>
</form>




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

        <!-- <div class="bloqueSolo b-azul pad20" style="display:none"> 
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
                    <php //===================AGREGAR ? PARA QUE FUNCIONE EL FETCH_ASSOC
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
    </div> -->





    <div class="bloqueSolo b-azul pad20">
            <div class="centrar column pad20 gap05">
                <h2 class="f-white">Ferias registradas</h2>
                <button type="button" class="btn-load gold" onclick="mostrarForm('formFerias')">
                    <span><i class="fa-solid fa-plus"></i> Agregar feria</span>
                </button>
            </div>
        <table class="tablaStyle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la feria</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ferias)): ?>
                    <?php foreach ($ferias as $feria): ?>
                        <tr>
                            <td><?php echo $feria['id_feria']; ?></td>
                            <td><?php echo htmlspecialchars($feria['nombre_feria']); ?></td>
                            <td>
                                <form action="actions/cambiar_estadoferia.php" class="formFunctions" method="post" onsubmit="return confirm('¿Desea cambiar el estado?');">
                                    <input type="hidden" name="id_feria2" value="<?= htmlspecialchars($feria['id_feria']) ?>" readonly>
                                    <input type="hidden" name="estado" class="estado-input" value="<?= $feria['estado'] === 'activo' ? 'activo' : 'inactivo' ?>" readonly>
                                        <button type="button" class="btn-load azul" 
                                            onclick="editarFeria('<?php echo $feria['id_feria']; ?>','<?php echo addslashes($feria['nombre_feria']); ?>')">
                                            <span><i class="fa-solid fa-pencil"></i></span>
                                        </button> 

                                    <label class="switch">
                                        <input type="checkbox" class="estado-checkbox" <?= $feria['estado'] === 'activo' ? 'checked' : '' ?> onchange="stateSwitch(this)">
                                        <span class="fondo-switch"></span>
                                        <span class="label-text">
                                            <?= $feria['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </label>
                                    
                                </form>
                            </td>




                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No hay ferias registradas</td>
                    </tr>
                <?php endif; ?>
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