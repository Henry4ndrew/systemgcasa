<?php 
    require 'includes/conexion.php';
    $query = "SELECT id, ruta_img, titulo, descripcion FROM portada";
    $result = $conexion->query($query);
    if (!$result) {
        die("Error en la consulta: " . $conexion->error);
    }
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addPortada.php' ?>


<div style="min-width:500px;">
    <h3 class="b-naranja f-white pad-left20">Gestionar portadas de la página web</h3>
    <div class="b-azul pad20 cont-elemts">
        <button class="btn-load orange" onclick="mostrarForm('formPortada')"><span>Agregar portada</span></button>
    </div>
</div>

<table class="tablaStyle">
<thead>
    <tr>
        <th>Imagen</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Acción</th>
    </tr>
</thead>
<tbody>
    <?php
        if ($result->num_rows > 0) {
            $rows = '';
            while ($row = $result->fetch_assoc()) {
                $rows .= "<tr>
                    <td><div class='centrar'><img src='" . str_replace('../', '', htmlspecialchars($row['ruta_img'])) . "' class='imgTabla' alt='" . str_replace('../', '', htmlspecialchars($row['ruta_img'])) . "'></div></td>
                    <td>" . htmlspecialchars($row['titulo']) . "</td>
                    <td><div class='limitarContenido'>" . htmlspecialchars($row['descripcion']) . "</div></td>
                                    <td>
                        <form action='actions/eliminar_portada.php' method='post' class='formFunctions' onsubmit=\"return confirm('¿Estás seguro de que deseas eliminar esta portada?');\">

                        
                            <button class='btn-load azul centrarIcon' type='button'
                                onclick=\"recojerDatosPortada(
                                '{$row['id']}',
                                '" . addslashes($row['ruta_img']) . "',
                                '" . addslashes($row['titulo']) . "',
                                '" . addslashes($row['descripcion']) . "'
                                )\">
                                <span><i class='fa-solid fa-pencil'></i></<span>
                            </button>


                            <input value='" . $row['id'] . "' name='id'type='hidden' readonly>
                            <button class='btn-load rojo' type='submit'><span><i class='fa-solid fa-trash'></i></span></button>
                        </form>
                    </td>
                    </tr>";
            }
            echo $rows;
        } else {
            echo "<tr><td colspan='4'>No hay datos disponibles.</td></tr>";
        }
    ?>
</tbody>
</table>



<style>
    .imgTabla{
        width:120px;
        height:80px;
        object-fit:cover;
    }
    .limitarContenido{
        max-height: 100px;
        overflow-y: auto;
    }
</style>