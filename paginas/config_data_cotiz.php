<?php
  require 'includes/conexion.php';

// Fetch data for both IDs
$sqlPiePagina = "SELECT * FROM pie_pagina_cotizacion WHERE id IN (1, 2)";
$resultadoPiePagina = $conexion->query($sqlPiePagina);

// Initialize both data arrays with default values
$datosPiePagina1 = [
    'direccion' => '',
    'celular_contacto' => '',
    'celular_fabrica' => '',
    'correo' => '',
    'direction_tienda' => '',
    'url_firma' => 'img/iconGalery.avif',
    'url_logo' => 'img/iconGalery.avif',
    'nombre_firma' => '',
    'cargo_firma'=> ''
];

$datosPiePagina2 = [
    'direccion' => '',
    'celular_contacto' => '',
    'celular_fabrica' => '',
    'correo' => '',
    'direction_tienda' => '',
    'url_firma' => 'img/iconGalery.avif',
    'url_logo' => 'img/iconGalery.avif',
    'nombre_firma' => '',
    'cargo_firma'=> ''
];

if ($resultadoPiePagina->num_rows > 0) {
    while($row = $resultadoPiePagina->fetch_assoc()) {
        if ($row['id'] == 1) {
            $datosPiePagina1 = array_merge($datosPiePagina1, $row);
        } elseif ($row['id'] == 2) {
            $datosPiePagina2 = array_merge($datosPiePagina2, $row);
        }
    }
}
?>


<?php include 'includes/permisos.php' ?>

<h3 class="b-naranja f-white pad-left20" style="min-width:500px;">Pie de página de cotizaciones</h3>




<div class="cont-2elem f-justify">

    <form action="actions/updatePiePag_Cotiz.php" method="post" class="bloqueSolo b-azul" style="display:block;" enctype="multipart/form-data">
        <h2 class="f-gold f-med pad20 f-justify">Gcasaclub</h2>

        <input type="text" name="idPiePag" value="1" hidden readonly>
        <fieldset>
        <legend class="f-white">Datos de la Fábrica:</legend>
            <div class="elem2 column">
                <label class="f-peq f-white" for="direccionPiePag">Dirección de fábrica:</label>
                <textarea class="input pd rad7" id="direccionPiePag" rows="3" maxlength="1000" name="direccionPiePag"><?php echo htmlspecialchars($datosPiePagina1['direccion']); ?></textarea>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white" for="celFabPiePag">Celular de fábrica:</label>
                 <input class="input pd" type="text" id="celFabPiePag" oninput="soloNumeros('celFabPiePag')" name="celFabPiePag" value="<?php echo htmlspecialchars($datosPiePagina1['celular_fabrica']); ?>">
            </div>
        </fieldset>

        <fieldset>
        <legend class="f-white">Datos Generales:</legend>   
        <!-- Agrear validación de datos numericos -->
        <div class="elem2 column">
            <label class="f-peq f-white" for="celContactPiePag">Celular contacto:</label>
            <input class="input pd" type="text" id="celContactPiePag" oninput="soloNumeros('celContactPiePag')"  name="celContactPiePag"  value="<?php echo htmlspecialchars($datosPiePagina1['celular_contacto']); ?>">
        </div>    
        <div class="elem2 column">
            <label class="f-peq f-white" for="correoPiePag">Correo:</label>
            <input class="input pd" type="email" id="correoPiePag" name="correoPiePag" value="<?php echo htmlspecialchars($datosPiePagina1['correo']); ?>">
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white" for="direcTienda">Dirección de tienda:</label>
            <textarea class="input pd rad7" id="direcTienda" name="direcTienda" rows="3" maxlength="1000"><?php echo htmlspecialchars($datosPiePagina1['direction_tienda']); ?></textarea>
        </div>
        <div class="separador">
            <div class="mitad elem2 column">
                <label class="f-peq f-white" for="LogoInput">Actualizar Logo:</label>
                <div class="column elem centrar">
                        <label for="LogoInput" id="logo_image" class="square column centrar f-white" style="width:170px;">
                            <i class="fa-solid fa-image"></i>Seleccionar imagen 
                        </label>
                    <input type="file" id="LogoInput" name="logo" style="display:none;" onchange="previsualizarImagen('LogoInput','logo_image')" accept="image/*">
                </div>
            </div>
            <div class="column mitad">
                <label class="f-peq f-white" for="">Logo actual:</label>
                <img src="<?php echo htmlspecialchars(str_replace('../', '', $datosPiePagina1['url_logo'])); ?>" alt="Logo" class="img-intoForm">
            </div>
        </div>
        </fieldset>


        <fieldset>
        <legend class="f-white">Datos de la Firma:</legend>       
        <div class="elem2 column">
            <label class="f-peq f-white" for="name_firma">Nombre de firma:</label>
            <input class="input pd" type="text" id="name_firma" name="nombreFirma" value="<?php echo htmlspecialchars($datosPiePagina1['nombre_firma']); ?>">
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white" for="cargo_firma">Cargo de firma:</label>
            <input class="input pd" type="text" id="cargo_firma" name="cargoFirma" value="<?php echo htmlspecialchars($datosPiePagina1['cargo_firma']); ?>">
        </div>
        <div class="separador">
            <div class="mitad elem2 column">
                <label class="f-peq f-white" for="firmaDigInput">Imagen de la firma:</label>
                <div class="column elem centrar">
                        <label for="firmaDigInput" id="firmaDigital"  class="square column centrar f-white" style="width:170px;">
                            <i class="fa-solid fa-image"></i>Seleccionar imagen 
                        </label>
                    <input type="file" id="firmaDigInput" name="firmaDigital" style="display:none;" onchange="previsualizarImagen('firmaDigInput', 'firmaDigital')" accept="image/*">
                </div>
            </div>
            <div class="column mitad">
                <label class="f-peq f-white" for="">Firma actual:</label>
                <img src="<?php echo htmlspecialchars(str_replace('../', '', $datosPiePagina1['url_firma'])); ?>" alt="Logo" class="img-intoForm">
            </div>
        </div>
        </fieldset>


        <div class="containerBtns">
           <button type="submit" class="btn-load gold"><span>Actualizar Datos</span></button>
        </div>
    </form>




    <form action="actions/updatePiePag_Cotiz.php" method="post" class="bloqueSolo b-naranja" style="display:block;" enctype="multipart/form-data">
        <h2 class="f-white f-med pad20 f-justify">Monik</h2>  

        <input type="text" name="idPiePag" value="2" hidden readonly>
        <fieldset>
        <legend class="f-white">Datos de la Fábrica:</legend>
            <div class="elem2 column">
                <label class="f-peq f-white">Dirección de fábrica:</label>
                <textarea class="input pd rad7" id="direccionPiePag" rows="3" maxlength="1000" name="direccionPiePag"><?php echo htmlspecialchars($datosPiePagina2['direccion']); ?></textarea>
            </div>
            <div class="elem2 column">
                <label class="f-peq f-white">Celular de fábrica:</label>
                 <input class="input pd" type="text" id="celFabPiePag2" oninput="soloNumeros('celFabPiePag2')" name="celFabPiePag" value="<?php echo htmlspecialchars($datosPiePagina2['celular_fabrica']); ?>">
            </div>
        </fieldset>

        <fieldset>
        <legend class="f-white">Datos Generales:</legend>   
        <!-- Agrear validación de datos numericos -->
        <div class="elem2 column">
            <label class="f-peq f-white">Celular contacto:</label>
            <input class="input pd" type="text" id="celContactPiePag2" oninput="soloNumeros('celContactPiePag2')" name="celContactPiePag"  value="<?php echo htmlspecialchars($datosPiePagina2['celular_contacto']); ?>">
        </div>    
        <div class="elem2 column">
            <label class="f-peq f-white">Correo:</label>
            <input class="input pd" type="email" id="correoPiePag" name="correoPiePag" value="<?php echo htmlspecialchars($datosPiePagina2['correo']); ?>">
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Dirección de tienda:</label>
            <textarea class="input pd rad7" id="direcTienda" name="direcTienda" rows="3" maxlength="1000"><?php echo htmlspecialchars($datosPiePagina2['direction_tienda']); ?></textarea>
        </div>
        <div class="separador">
            <div class="mitad elem2 column">
                <label class="f-peq f-white" for="LogoInput2">Actualizar Logo:</label>
                <div class="column elem centrar">
                        <label for="LogoInput2" id="logo_image2" class="square column centrar f-white" style="width:170px;">
                            <i class="fa-solid fa-image"></i>Seleccionar imagen 
                        </label>
                    <input type="file" id="LogoInput2" name="logo" style="display:none;" onchange="previsualizarImagen('LogoInput2','logo_image2')" accept="image/*">
                </div>
            </div>
            <div class="mitad">
                <label class="f-peq f-white">Logo actual:</label>
                <img src="<?php echo htmlspecialchars(str_replace('../', '', $datosPiePagina2['url_logo'])); ?>" alt="Logo" class="img-intoForm">
            </div>
        </div>
        </fieldset>


        <fieldset>
        <legend class="f-white">Datos de la Firma:</legend>       
        <div class="elem2 column">
            <label class="f-peq f-white">Nombre de firma:</label>
            <input class="input pd" type="text" id="name_firma" name="nombreFirma" value="<?php echo htmlspecialchars($datosPiePagina2['nombre_firma']); ?>">
        </div>
        <div class="elem2 column">
            <label class="f-peq f-white">Cargo de firma:</label>
            <input class="input pd" type="text" id="cargo_firma" name="cargoFirma" value="<?php echo htmlspecialchars($datosPiePagina2['cargo_firma']); ?>">
        </div>
        <div class="separador">
            <div class="mitad elem2 column">
                <label class="f-peq f-white" for="firmaDigInput2">Imagen de la firma:</label>
                <div class="column elem centrar">
                        <label for="firmaDigInput2" id="firmaDigital2"  class="square column centrar f-white" style="width:170px;">
                            <i class="fa-solid fa-image"></i>Seleccionar imagen 
                        </label>
                    <input type="file" id="firmaDigInput2" name="firmaDigital" style="display:none;" onchange="previsualizarImagen('firmaDigInput2', 'firmaDigital2')" accept="image/*">
                </div>
            </div>
            <div class="mitad">
                <label class="f-peq f-white" for="">Firma actual:</label>
                <img src="<?php echo htmlspecialchars(str_replace('../', '', $datosPiePagina2['url_firma'])); ?>" alt="Logo" class="img-intoForm">
            </div>
        </div>
        </fieldset>


        <div class="containerBtns">
           <button type="submit" class="btn-load gold"><span>Actualizar Datos</span></button>
        </div>
    </form>

</div>


<style>
    .img-intoForm{
        max-width:100%;
        max-height:160px;
        object-fit:cover;
    }
</style>