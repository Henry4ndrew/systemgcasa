<?php 
    require 'includes/conexion.php';

$sqlMaterial = "SELECT id_material, codigo_material, nombre_material, tipo_medida, medida1, nombre_medida1, medida2, nombre_medida2, ruta_imagen, fecha_modificacion, estado
                FROM materia_prima
                WHERE estado = 'activo'
                ORDER BY fecha_modificacion DESC";
    $resultMaterial = $conexion->query($sqlMaterial);
    $materiales = [];
    if ($resultMaterial->num_rows > 0) {
        while ($row = $resultMaterial->fetch_assoc()) {
            $materiales[] = $row;
        }
    }
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/materiaPrima.php'; ?>



<h3 class="b-naranja f-white pad-left20">Lista de materiales</h3>
<div class="b-azul pad20 cont-elemts">
    <div class="search-box">
        <div class="input-wrapper">
            <input class="input padInput" type="text" id="search-input" oninput="buscar2C3C('search-input','tablaMaterials')" placeholder="Ingrese código o nombre">
                <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
    <button class="btn-load orange" onclick="mostrarForm('formMateria')"><span>Crear material</span></button>
</div>


<table class="tablaStyle" id="tablaMaterials">
    <thead>
        <tr>
            <th>imagen</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Medida</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($materiales as $material): ?>
            <tr>
              <td>
                  <?php if (!empty($material['ruta_imagen']) && file_exists($material['ruta_imagen'])): ?>
                     <img src="<?php echo $material['ruta_imagen']; ?>" class="img-peque" alt="Imagen del producto">
                  <?php else: ?>
                     <i class="fa-regular fa-images f-grande padSpace"></i>
                  <?php endif; ?>
                </td>
                <td><?php echo $material['codigo_material']; ?></td>
                <td><?php echo $material['nombre_material']; ?></td>
                <td>
                    <div class="centrar column">
                        <p class="hora"><?php echo $material['tipo_medida']; ?></p>

                        <?php if (!empty($material['medida1'])): ?>
                            <?php echo rtrim(rtrim($material['medida1'], '0'), '.'); ?> 
                            <?php echo $material['nombre_medida1']; ?>
                        <?php endif; ?>
                    
                        <?php if ($material['tipo_medida'] === 'metro_cuadrado' && !empty($material['medida2'])): ?>
                            x <?php echo rtrim(rtrim($material['medida2'], '0'), '.'); ?> 
                            <?php echo $material['nombre_medida2']; ?>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                   <div class="centrar">
                    <button type="button" class="btn-load azul"
                        onclick='editarMaterial(<?= json_encode($material, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                        <span><i class="fa-solid fa-pencil"></i></span>
                    </button>
                   </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
   
