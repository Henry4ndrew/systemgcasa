<?php
    require 'includes/conexion.php';
    $query = "SELECT id, titulo, descripcion, archivo_pdf FROM documentos_pdf";
    $resultado = $conexion->query($query);
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addPdf.php' ?>

<div style="min-width:500px;">
    <h3 class="b-naranja f-white pad-left20">Documentos pdf publicados</h3>
    <div class="b-azul pad20 cont-elemts">
        <button class="btn-load orange" onclick="mostrarFormPdf('formAddPdf')"><span>Agregar pdf</span></button>
    </div>
</div>


<table class="tablaStyle">
    <thead>
        <tr>
            <th>Título</th>
            <th><div class="m-right180">Descripción</div></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td>
                    <div class="centrar">
                        <a class="btn-load rojo" href="actions/eliminarPdf.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este PDF?');"><span><i class="fa-solid fa-trash"></i></span></a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>