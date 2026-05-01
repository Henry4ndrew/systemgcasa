<form class="formStyle b-azul grande" method="post" id="formAcordeonCotiz" data-accion="guardar" onsubmit="validarFormularioAcordeon(event, 'formAcordeonCotiz')" novalidate>
    <div class="cabecera">
        <h2 class="f-center" id="txt-formAcordeonCotiz">Realizar cotizacion</h2>
        <button type="button" onclick="plop('formAcordeonCotiz')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <input type="hidden" name="idVendedor" id="idVendedor" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>
    <input type="hidden" name="idCotizacion" id="idCotizacion" readonly>
    <!-- Sección 1 (ya incluye el id="pest1" y los nombres de clase del header acordeon) -->
    <?php include 'component-search-client.php'; ?>

    <!-- Sección 2 -->
    <div class="accordion-section">

        <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest3')">
            <i class="fa-solid fa-2 circle centrar"></i>Lista de productos
        </div>
        <div class="accordion-content" id="pest3">
              <div style="margin-top:-10px; margin-left:15px; display:none;" id="campoBuscadorProdCotiz">
                <div style="display:flex; justify-content:space-between; padding-right:20px;">
                    <div>
                      <label class="f-white f-peq">Agregar Productos:</label>
                      <input id="search-products3" oninput="buscarProducto('search-products3','list-prod-details')" onfocus="mostrarLista('list-prod-details')" type="text" class="input pd" placeholder="Buscar producto">
                    </div>
                    <button type="button" style="background:none; border:none;" id="btnCloseFlotante" onclick="cerrarLisFlotanteProdCotiz()">
                        <i class="fa-solid fa-xmark f-white f-med"></i>
                    </button>
                </div>
              </div>
              <!-- Esta es la lista flotante donde aparecen los productos para agregar en la cotización -->
              <div class="item-material listaFlotanteProductos" id="list-prod-details" style="z-index:3; height:550px; padding:10px; display:none;"> </div>
              <div style="padding-right:17px;" id="lista-productos"></div>
        </div>
    </div>

    <!-- Sección 3 -->
    <div class="accordion-section">
      <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest2')"><i class="fa-solid fa-3 circle centrar"></i>Datos de la cotización</div>
        <div class="accordion-content" id="pest2">
        <div class="separador">
                <div class="mitad">
                    <div class="elem2 column radio-group">
                        <label class="f-white">¿La cotizacion tiene una fecha de caducidad?<span class="a">*</span></label>
                        <div>
                            <input type="radio" id="fechaEntregaSi2" name="fechaEntregaPregunta2" value="si" onclick="gestionarFechaEntrega2()" required>
                            <label class="f-white" for="fechaEntregaSi">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="fechaEntregaNo2" name="fechaEntregaPregunta2" value="no" onclick="gestionarFechaEntrega2()" required>
                            <label class="f-white" for="fechaEntregaNo">No</label>
                        </div> 
                    </div>
                    <div class="elem2 column" id="campo-fechaEntrega2" style="display:none;">
                       <label class="f-peq f-white" for="fechaEntrega2">Fecha de caducidad:</label>
                       <input class="input pd" type="date" id="fechaEntrega2" name="fechaCaducidad">
                    </div>
                    <?php include 'component-descuento.php'; ?>
                </div>
                <div class="mitad">
                    <div class="elem2 column">
                      <label class="f-peq f-white" for="tituloCotizacion">Título:<span class="a">*</span></label>
                      <input class="input pd" type="text" id="tituloCotizacion" name="tituloCotizacion" placeholder="Título de cotización" required>
                    </div>
                    <div class="elem2 column">
                        <label class="f-peq f-white" for="opcionesCotiz">Pie Pagina:<span class="a">*</span></label>
                        <select class="select pd" name="piePagina" id="opcionesCotiz" required>
                            <option value="">Seleccione una opción</option>
                            <option value="1">GcasaClub</option>
                            <option value="2">Monik</option>
                        </select>
                    </div>
                    <div  class="elem2 column">
                        <label class="f-peq f-white" for="cuentaBancaria">Cuenta de Banco:</label>
                        <select class="select pd" name="cuenta_bancaria" id="cuenta_bancaria">
                            <option value="">Seleccione una opción</option>
                            <?php
                            while ($fila = mysqli_fetch_assoc($resultadoCuentas)) {
                                $palabras = explode(' ', trim($fila['titularCuenta']));
                                $titularCorto = implode(' ', array_slice($palabras, 0, 2));
                                ?>
                                <option value="<?php echo $fila['id']; ?>">
                                    <?php 
                                    echo $fila['id'] . ' - ' . 
                                        $fila['nombreBanco'] . ' - ' . 
                                        $titularCorto; 
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="elem2 column">
                        <label class="f-peq f-white" for="nota">Nota.-</label>
                        <textarea class="input pd rad7" rows="4" name="nota" id="nota-cotiz" placeholder="Aparece en la cotización"></textarea>
                    </div>
                </div>

           </div> <!--cierre de separador-->
        </div> <!--cierre de accordion-content-->
    </div> <!--cierre de accordion-section-->


    <div class="contenedor-final">
        <?php include 'component-total-gral.php'; ?>
        <button onclick="cambiarDataForm('formAcordeonCotiz','guardar')" id="btn1-formAcordeonCotiz" class="btn-load verde" style=" margin: 0 auto;" type="submit"><span>Realizar cotización</span></button>  
        <button  onclick="cambiarDataForm('formAcordeonCotiz','editar')" id="btn2-formAcordeonCotiz" class="btn-load azul" style=" margin: 0 auto; display:none;" type="submit"><span>Editar cotización</span></button>  
    </div>
</form>








<!-- Formulario para cargar los detallles de un producto -->
<form class="formStyle b-azul mediano" id="formDetails" style="z-index:4;">
    <div class="cabecera">
        <h2 id="txtFormDetails" class="f-center"></h2>
        <button type="button" onclick="plop('formDetails')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <section class="campoForm" id="detallesEncontrados"> </section>
    <br>
</form>
