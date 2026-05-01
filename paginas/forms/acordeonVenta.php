
<form action="actions/registrarVenta.php" class="formStyle b-azul grande" method="post" id="formAcordeonVentas" onsubmit="validarFormularioAcordeon(event, 'formAcordeonVentas')" novalidate>
    <div class="cabecera">
        <h2 class="f-center">Registrar venta</h2>
        <button type="button" onclick="plop('formAcordeonVentas')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <input type="hidden" name="idVendedor" id="idVendedor" value="<?php echo $_SESSION['id_usuario']; ?>" readonly>
    
    <!-- Sección 1 (ya incluye el id="pest1" y los nombres de clase del header acordeon) -->
    <?php include 'component-search-client.php'; ?>

    <!-- Sección 2 -->
    <div class="accordion-section">
      <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest2')"><i class="fa-solid fa-2 circle centrar"></i>Datos de la venta</div>
        <div class="accordion-content" id="pest2">
        <div class="separador">
                <div class="mitad">
                    <?php include 'component-venta-fechaEntrega.php'; ?>
                </div>


                <div class="mitad">
                    <div class="elem2 column">
                        <label class="f-peq f-white" for="ambiente_venta">Lugar de venta:<span class="a">*</span></label>
                        <select class="select pd" name="ambiente_venta" id="ambiente_venta" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="Fabrica">Fábrica</option>
                            <option value="Tienda">Tienda</option>
                        </select>
                    </div>
                    <div class="elem2 column">
                            <label class="f-peq f-white" for="nota">Nota.-</label>
                            <textarea class="input pd rad7" name="nota" id="nota" rows="4"></textarea>
                    </div>
                </div>
           </div> <!--cierre de separador-->
        </div> <!--cierre de accordion-content-->
    </div> <!--cierre de accordion-section-->

    <!-- Sección 3 -->
    <div class="accordion-section">
        <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest3')"><i class="fa-solid fa-3 circle centrar"></i>Lista de productos</div>
        <div class="accordion-content" id="pest3">
                <div style="padding-right:17px;" id="lista-productos"></div>
        </div>
    </div>

    <!-- Sección 4 -->
    <div class="accordion-section">
        <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest4')"><i class="fa-solid fa-4 circle centrar"></i>Realizar cobro</div>
        <div class="accordion-content" id="pest4">
             <div class="separador">
                  <div class="mitad">
                    <?php include 'component-descuento.php'; ?>
                 </div>
                 <div class="mitad">
                    <!-- Aqui esta el id del usuario que hace la venta -->
                    <?php include 'component-cobro.php'; ?>
                 </div>
             </div>
        </div>
    </div>

    <div class="contenedor-final">
       <?php include 'component-total-gral.php'; ?>
       <button class="btn-load verde" style="margin: 0 auto;" type="submit"><span>Realizar venta</span></button>   
    </div>
</form>

