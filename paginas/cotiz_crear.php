<?php
include 'includes/conexion.php';
$sqlCuentas = "SELECT id, titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR 
        FROM cuentas_bancarias";
$resultadoCuentas = mysqli_query($conexion, $sqlCuentas);
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/acordeonCotiz.php' ?>




<h3 class="b-naranja f-white pad-left20">Realizar cotización</h3>
<div class="b-azul pad20 cont-elemts">
    <div class="search-box">
        <div class="input-wrapper">
            <input class="input padInput" type="text" id="search-input" oninput="buscarProducto('search-input','resultadoBusqueda')" placeholder="Ingrese nombre o código">
                <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
<button class="btn-load orange" onclick="mostrarFormAcodeonExistente(); limpiarFormAcordeon();">
    <span>Datos de la cotizacion</span>
</button>
</div>








<div class="panelProductos wrap" id="resultadoBusqueda"></div>



<!-- Formulario para cargar los detallles de un producto -->
<form class="formStyle b-azul mediano" id="formDetails" style="z-index:3;">
    <div class="cabecera">
        <h2 id="txtFormDetails" class="f-center"></h2>
        <button type="button" onclick="plop('formDetails')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <section class="campoForm" id="detallesEncontrados"> </section>
    <br>
</form>









