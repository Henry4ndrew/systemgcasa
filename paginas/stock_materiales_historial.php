<?php
require 'includes/conexion.php';

$query = "
    SELECT 
        hm.id_historial,
        hm.accion,
        hm.nota,
        hm.id_user,
        hm.responsable,
        DATE(hm.fecha) AS solo_fecha,
        TIME(hm.fecha) AS solo_hora,
        hm.fecha,
        u.nombre_usuario,
        GROUP_CONCAT(
            CONCAT_WS('|',
                hmm.id_material,
                hmm.cantidad,
                mp.codigo_material,
                mp.nombre_material,
                mp.nombre_medida1
            ) SEPARATOR ';'
        ) AS materiales
    FROM historial_materia_prima hm
    LEFT JOIN usuarios u ON hm.id_user = u.id_user
    LEFT JOIN historial_matPrim_materiales hmm 
        ON hm.id_historial = hmm.id_historial
    LEFT JOIN materia_prima mp 
        ON hmm.id_material = mp.id_material
    GROUP BY hm.id_historial
    ORDER BY hm.fecha DESC
";

$resultado = mysqli_query($conexion, $query);

$historial = [];

if ($resultado) {

    while ($row = mysqli_fetch_assoc($resultado)) {

        $materiales = [];

        if (!empty($row['materiales'])) {
            $items = explode(';', $row['materiales']);

            foreach ($items as $item) {
                $data = explode('|', $item);

                $materiales[] = [
                    'id_material' => $data[0] ?? '',
                    'cantidad' => $data[1] ?? 0,
                    'codigo' => $data[2] ?? '',
                    'nombre' => $data[3] ?? '',
                    'medida' => $data[4] ?? ''
                ];
            }
        }

        $historial[] = [
            'id_historial' => $row['id_historial'],
            'accion' => $row['accion'],
            'usuario' => $row['nombre_usuario'] ?? 'Usuario',
            'id_user' => $row['id_user'],
            'nota' => $row['nota'],
            'responsable' => $row['responsable'],
            'solo_fecha' => $row['solo_fecha'],
            'solo_hora'  => $row['solo_hora'],
            'materiales' => $materiales
        ];
    }

    mysqli_free_result($resultado);
}


function iconoAccion($accion)
{
    switch ($accion) {
        case 'Se agregó':
            return '<i class="fa-solid fa-circle-plus accion-agregar"></i>';

        case 'Se editó':
            return '<i class="fa-solid fa-pen-to-square accion-editar"></i>';

        case 'Se retiró':
            return '<i class="fa-solid fa-circle-minus accion-retirar"></i>';

        default:
            return '';
    }
}

mysqli_close($conexion);
?>



<div class="panel">
    <!-- <h3 class="b-naranja f-white pad-left20">Stock de tienda</h3> -->
  <div class="gap05">
    <button
        class="pestana f-white b-azul"
        onclick="cargarPagina('stock_materiales.php')">
        Stock Materiales
    </button>
    <button
        class="pestana f-white b-naranja activo"
        onclick="cargarPagina('stock_materiales_historial.php')">
        Historial stock Materiales
    </button>
</div>

<h3 class="b-naranja f-white pad-left20" style="padding-top:7px;">Historial - stock Materia prima</h3>

<div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input" oninput="buscar1C3C('search-input','tablaHistorialMP')" placeholder="# o usuario">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="m-left">
            <label class="f-white" for="filtroLugarVenta">Filtrar por:</label>
            <select class="select pd" id="filtroLugarVenta" onchange="filtrarHistMateriaPrima()">
                <option value="todos">Todos</option>
                <option value="Se agregó">Agregados</option>
                <option value="Se editó">Editados</option>
                <option value="Se retiró">Retirados</option>
            </select>
        </div>

        <div class="m-left bordear">
            <label class="f-white">Desde:</label>
            <input type="date" id="fechaDesde" class="select pd"
                onchange="filtrarPorFecha('tablaHistorialMP', 6)">

            <label class="f-white">Hasta:</label>
            <input type="date" id="fechaHasta" class="select pd"
                onchange="filtrarPorFecha('tablaHistorialMP', 6)">
        </div>
</div>





<table class="tablaStyle" id="tablaHistorialMP">
    <thead>
        <tr>
            <th>ID</th>
            <th>Acción</th>
            <th>Usuario</th>
            <th>Materiales afectados</th>
            <th>Responsable</th>
            <th>Nota</th>
            <th>Fecha</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($historial as $fila): ?>
        <tr>
            <td><?= $fila['id_historial'] ?></td>

            <td>
                <?= iconoAccion($fila['accion']) ?>
                <span><?= htmlspecialchars($fila['accion']) ?></span>
            </td>


            <td>
                <?= htmlspecialchars($fila['usuario']) ?><br>
            </td>

            <td>
                <button class="btn-invi" type="button" onclick="openForm(this)">Ver materiales</button>

                <form class="formStyle mediano b-azul">
                    <div class="cabecera">
                        <h2 class="f-med"><?= $fila['id_historial'] ?> - <?= htmlspecialchars($fila['accion']) ?></h2>
                        <button type="button" onclick="closeForm(this)">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="f-white f-center" style="margin-top:10px;"><?= $fila['fecha'] ?></div>
                    <div class="campoForm">
                        <?php foreach ($fila['materiales'] as $m): ?>
                                <div style="background:#f0f0f0; padding:5px; border-bottom:1px solid #ccc; justify-content:space-between; display:flex;">
                                <b><?= htmlspecialchars($m['nombre']) ?></b>
                                <span class="pad-right-20"><?= rtrim(rtrim(number_format($m['cantidad'], 2, '.', ''), '0'), '.') ?></span>
                                </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </td>
            <td>
               <?= $fila['responsable'] ?>
            </td>
            <td>
                <?= nl2br(htmlspecialchars($fila['nota'])) ?>
            </td>

            <td>
                <div class="column centrar">
                    <b><?= date('d-m-Y', strtotime($fila['solo_fecha'])) ?></b>
                    <span class="hora"><?= htmlspecialchars($fila['solo_hora']) ?></span>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<style>
.accion-agregar {
    color: #2ecc71;
    margin-right: 6px;
}
.accion-editar {
    color: #3498db;
    margin-right: 6px;
}
.accion-retirar {
    color: #e74c3c;
    margin-right: 6px;
}
</style>