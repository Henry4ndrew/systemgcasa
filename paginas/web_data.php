<!-- Aquí mostramos datos de la empresa y tambien la cartera de clientes -->
<?php
   require 'includes/conexion.php';
    $sql = "SELECT facebook, instagram, tiktok, celular_tienda, celular_fabrica, direccion_fabrica, email, gps_tienda, gps_fabrica FROM datos WHERE id = 1";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        $fila = [
            'facebook' => '',
            'instagram' => '',
            'tiktok' => '',
            'celular_tienda' => '',
            'celular_fabrica' => '',
            'direccion_fabrica' => '',
            'email' => '',
            'gps_tienda' => '',
            'gps_fabrica' => ''
        ];
    }

    $sqlClients = "SELECT id, url_img FROM img_section_clientes";
    $resultadoClients = $conexion->query($sqlClients);
?>




<?php include 'includes/permisos.php' ?>

<section class="panel-2elem">
<h3 class="b-naranja f-white pad-left20">Datos usados en la página web</h3>
</section>



<div class="cont-2elem f-justify">

    <form action="actions/updateSocial.php" method="post" class="bloqueSolo b-azul" style="display:block;" enctype="multipart/form-data">
        <h2 class="f-gold f-med pad20 f-justify">Datos generales</h2>     
        <div class="elem centrar">
            <i class="fab fa-facebook f-med icon-lateral f-white"></i>
            <input class="input padInput inputLateral" type="url" id="facebook" name="facebook" placeholder="Enlace de Facebook" value="<?php echo htmlspecialchars($fila['facebook']); ?>" required>
        </div>
        <div class="elem centrar">
            <i class="fab fa-instagram f-med icon-lateral f-white"></i>
            <input class="input padInput inputLateral" type="url" id="instagram" name="instagram" placeholder="Enlace de Instagram" value="<?php echo htmlspecialchars($fila['instagram']); ?>">
        </div>
        <div class="elem centrar">
            <i class="fab fa-tiktok f-med icon-lateral f-white"></i>
            <input class="input padInput inputLateral" type="url" id="tiktok" name="tiktok" placeholder="Enlace de Tiktok" value="<?php echo htmlspecialchars($fila['tiktok']); ?>">
        </div>
        <div class="elem centrar">
            <i class="fas fa-envelope f-med icon-lateral f-white"></i>
            <input class="input padInput inputLateral" type="email" id="correoElectronico" name="correoElectronico" placeholder="Dirección de correo electrónico" value="<?php echo htmlspecialchars($fila['email']); ?>">
        </div>
        <fieldset>
        <legend class="f-white">Datos de la Tienda:</legend>
            <div class="elem centrar">
                <i class="fab fa-whatsapp f-med icon-lateral f-white"></i>
                <input class="input padInput inputLateral" type="tel" id="celularTienda" oninput="soloNumeros('celularTienda')" name="celularTienda" placeholder="Número de celular de la tienda" value="<?php echo htmlspecialchars($fila['celular_tienda']); ?>">
            </div>

            <div class="elem centrar">
                <i class="fa-solid fa-location-dot f-med icon-lateral f-white"></i>
                <input class="input padInput inputLateral" type="url" id="enlaceGPSTienda" name="enlaceGPSTienda" placeholder="Enlace gps de la Tienda" value="<?php echo htmlspecialchars($fila['gps_tienda']); ?>">
            </div>
        </fieldset>
        <fieldset>
        <legend class="f-white">Datos de la Fábrica:</legend>
            <div class="elem centrar">
                <i class="fab fa-whatsapp f-med icon-lateral f-white"></i>
                <input class="input padInput inputLateral" type="tel" id="celularFabrica" oninput="soloNumeros('celularFabrica')" name="celularFabrica" placeholder="123-456-7890" value="<?php echo htmlspecialchars($fila['celular_fabrica']); ?>">
            </div>

            <div class="elem centrar">
                <i class="fa-solid fa-location-dot f-med icon-lateral f-white"></i>
                <input class="input padInput inputLateral" type="url" id="enlaceGPSFabrica" name="enlaceGPSFabrica" placeholder="https://goo.gl/maps/xyz" value="<?php echo htmlspecialchars($fila['gps_fabrica']); ?>">
            </div> 

            <div class="elem centrar">
                <i class="fa-solid fa-pen-to-square f-med icon-lateral f-white"></i>
                <textarea class="input padInput inputLateral" type="text" id="direccionFabrica" name="direccionFabrica" placeholder="Calle Ficticia 123, Ciudad, País"><?php echo htmlspecialchars($fila['direccion_fabrica']); ?></textarea>
            </div>
      
        </fieldset>
        
        <div class="containerBtns">
           <button type="submit" class="btn-load gold"><span>Actualizar Datos</span></button>
        </div>
    </form>



    <section class="bloqueSolo b-azul">
        <div class="column centrar">
          <h2 class="f-gold f-med pad20">Cartera de clientes visibles en la web</h2>
          <button type="button" class="btn-load gold" onclick="mostrarMiniForm('form_sectClient')"><span>Agregar imágenes</span></button>     
        </div>
        <br>
            <div class="campoForm2">
            <div class="galeria-container">
                <?php
                $html_imagenes = '';
                    if ($resultadoClients->num_rows > 0) {
                        while ($row = $resultadoClients->fetch_assoc()) {
                            $html_imagenes .= '
                                <div class="imagen-container centrar">
                                    <img src="' . $row['url_img'] . '" alt="' . $row['url_img'] . '" style="max-width: 150px;">
                                    <button class="btn-load rojo btn-esquina" onclick="eliminarImagen(' . $row['id'] . ')"><span><i class="fa-solid fa-trash"></i></span></button>
                                </div>';
                        }
                    } else {
                        $html_imagenes = 'No hay imágenes disponibles.';
                    }
                    echo $html_imagenes;
                ?>
            </div>
            </div>
    </section>

</div>









<form action="actions/insertar_imagen.php" id="form_sectClient" class="formStyle pequeno b-azul" method="POST" enctype="multipart/form-data">
    <div class="cabecera">
        <h2 class="f-med f-white" id="txtFormPortada">Agregar Imagen/logo</h2>
        <button type="button" onclick="plop('form_sectClient')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <br>
    <div class="elem centrar">
            <label for="imagenCli" id="areaImgCli" class="square column centrar f-white">
                <i class="fa-solid fa-image"></i>Seleccionar imagen
            </label>
        <input type="file" id="imagenCli" name="imagenCli" style="display:none;" onchange="previsualizarImagen('imagenCli', 'areaImgCli')" accept="image/*">
    </div>

    <div class="containerBtns">
        <button type="submit" class="btn-load gold"><span>Agregar imagen</span></button>
    </div>
</form>

