<?php
require 'includes/conexion.php';

$sql = "SELECT 
            am.id_almacen,
            am.id_material,
            am.cantidad,
            am.fecha_modificacion,
            mp.codigo_material,
            mp.nombre_material,
            mp.tipo_medida,
            mp.medida1,
            mp.nombre_medida1,
            mp.medida2,
            mp.ruta_imagen,
            mp.nombre_medida2
        FROM almacen_materiales am
        INNER JOIN materia_prima mp ON am.id_material = mp.id_material
        ORDER BY am.fecha_modificacion DESC";

$result = $conexion->query($sql);
$materiales = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $materiales[] = $row;
    }
}
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addMaterial_almacen.php' ?>
<?php include 'forms/editCantMaterial_almacen.php' ?>
<?php include 'forms/retirarMaterial_almacen.php' ?>


<div class="panel">
    <!-- <h3 class="b-naranja f-white pad-left20">Stock de tienda</h3> -->
  <div class="gap05">
    <button
        class="pestana f-white b-naranja activo"
        id="stock_materiales_almacen"
        onclick="cargarPagina('stock_materiales.php')">
        Stock Materiales
    </button>
    <button
        class="pestana f-white b-azul"
        id="stock_materiales_historial"
        onclick="cargarPagina('stock_materiales_historial.php')">
        Historial stock Materiales
    </button>
</div>

<h3 class="b-naranja f-white pad-left20" style="padding-top:7px;">Stock de Materia prima</h3>
<div class="b-azul pad20 flex-between" style="padding-right:50px;">
    <div class="cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input" oninput="buscar3C4C('search-input','tabla-materials')" placeholder="Ingrese código o material">
                    <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
       <button class="btn-load orange" onclick="mostrarFormBuscador('formAddMateriaPrima','lista-materiales-agregados')"><span>Agregar material</span></button>
    </div>
   <button class="btn-load orange" onclick="mostrarFormBuscador('formRetirarMateriaPrima','lista-materiales-agregados2')"><span>Retirar material</span></button>
</div>



<table class="tablaStyle" id="tabla-materials">
    <thead>
        <tr>
            <th>imagen</th>
            <th>Cantidad</th>
            <th>Código</th>
            <th>Material</th>
            <th>Medida</th>
            <th><span class="centrar">Acciones</span></th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($materiales)): ?>
            <?php foreach($materiales as $material): ?>
                    <?php 
                        $cantidadMejorada = rtrim(rtrim($material['cantidad'], '0'), '.');
                    ?>
                <tr>
                    <td>
                        <?php if (!empty($material['ruta_imagen']) && file_exists($material['ruta_imagen'])): ?>
                            <img src="<?php echo $material['ruta_imagen']; ?>" class="img-peque" alt="Imagen del producto">
                        <?php else: ?>
                            <i class="fa-regular fa-images f-grande padSpace"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <b id="cant-m-<?php echo htmlspecialchars($material['id_almacen']); ?>">
                            <?php echo htmlspecialchars($cantidadMejorada); ?>
                        </b>
                    </td>
                        <td><?php echo htmlspecialchars($material['codigo_material']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($material['nombre_material']); ?>
                    </td>
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
                                    onclick='editarCantMaterial(<?php 
                                        echo json_encode([
                                            "id_almacen" => $material["id_almacen"],
                                            "id_detalle" => $material["id_material"],
                                            "cantidad" => $material["cantidad"],
                                            "codigo_material" => $material["codigo_material"],
                                            "nombre_material" => $material["nombre_material"],
                                            "tipo_medida" => $material["tipo_medida"],
                                            "medida1" => $material["medida1"],
                                            "nombre_medida1" => $material["nombre_medida1"],
                                            "medida2" => $material["medida2"],
                                            "nombre_medida2" => $material["nombre_medida2"],
                                            "ruta_imagen" => $material["ruta_imagen"]
                                        ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG); 
                                    ?>)'>
                                    <span><i class="fa-solid fa-pencil"></i></span>
                                </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No hay materiales registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>



