<?php
require 'includes/conexion.php';
$query = "
    SELECT 
        v.id_venta, 
        v.id_cliente, 
        c.nombre AS nombre_cliente, 
        v.fecha_entrega, 
        DATE(v.fecha_venta) AS solo_fecha,
        TIME(v.fecha_venta) AS solo_hora, 
        v.total_venta,
        v.lugar_venta,
        v.id_user, 
        u.nombre_usuario
    FROM ventas v
    LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON v.id_user = u.id_user
    ORDER BY v.fecha_venta DESC
";
$resultado = mysqli_query($conexion, $query);
$ventas = [];
if ($resultado) {
    $ventas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_free_result($resultado);
}
mysqli_close($conexion);
?>




<?php include 'includes/permisos.php' ?>
<?php include 'forms/component-panel-verCliente.php'?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Historial de ventas</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input" oninput="buscar1C2C('search-input','tablaHistorialVentas')" placeholder="Cliente o #">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="m-left">
            <label class="f-white" for="filtroLugarVenta">Filtrar por lugar de venta:</label>
            <select class="select pd" id="filtroLugarVenta" onchange="filtrarLugarVenta()">
                <option value="todos">Todos</option>
                <option value="Fabrica">Fábrica</option>
                <option value="Tienda">Tienda</option>
            </select>
        </div>

        <div class="m-left bordear">
            <label class="f-white">Desde:</label>
            <input type="date" id="fechaDesde" class="select pd"
                onchange="filtrarPorFecha('tablaHistorialVentas', 2)">

            <label class="f-white">Hasta:</label>
            <input type="date" id="fechaHasta" class="select pd"
                onchange="filtrarPorFecha('tablaHistorialVentas', 2)">
        </div>
    </div>
</div>


<table class="tablaStyle col2-peq top105" id="tablaHistorialVentas">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Fecha Venta</th>
            <th>Fecha Entrega</th>
            <th>Total Venta</th>
            <th>Usuario</th>
            <th>Detalles de la venta</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas as $fila): ?>
            <tr>
                <td id="v-id-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <?= htmlspecialchars($fila['id_venta']) ?>
                </td>

                <td id="v-cli-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <input type="hidden" id="id_cliente_<?= htmlspecialchars($fila['id_venta']) ?>" 
                           value="<?= htmlspecialchars($fila['id_cliente']) ?>">
                    <button type="button" onclick="mostrarCliente('<?= htmlspecialchars($fila['id_cliente']) ?>')" class="btn-invi">
                        <?= htmlspecialchars($fila['nombre_cliente']) ?>
                    </button>
                    <div id="cli_<?= htmlspecialchars($fila['id_venta']) ?>" style="display:none;"></div>
                </td>

                <td id="v-fecha-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <div class="column centrar">
                        <b><?= date('d-m-Y', strtotime($fila['solo_fecha'])) ?></b>
                        <span class="hora"><?= htmlspecialchars($fila['solo_hora']) ?></span>
                    </div>
                </td>

                <td id="v-entrega-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <?= htmlspecialchars($fila['fecha_entrega']) ?>
                </td>

                <td id="v-total-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <div class="centrar column">
                      <b><?= htmlspecialchars($fila['total_venta']) ?></b>
                      <p class="hora"><?= htmlspecialchars($fila['lugar_venta']) ?></p>
                    </div>
                </td>

                <td id="v-user-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <p class="hora"><?= htmlspecialchars($fila['nombre_usuario']) ?></p>
                </td>

                <td id="v-detalle-<?= htmlspecialchars($fila['id_venta']) ?>">
                    <button type="button" class="btn-invi" onclick="mostrarDetalleVenta('<?= htmlspecialchars($fila['id_venta']) ?>')">
                        Ver detalles de la venta
                    </button>
                    <button type="button" class="btn-invi" style="margin-left:8px;" onclick="imprimirVenta('<?= htmlspecialchars($fila['id_venta']) ?>', this)">
                        Imprimir
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<!-- Panel para ver los detalles de una venta -->
<section class="formStyle b-azul muy-grande" id="campoDetailVenta">
    <div class="cabecera">
      <h2 class="f-white" id="txtFormUser">Detalles de venta #<span id="num_venta"></span></h2>
      <button type="button" onclick="plop('campoDetailVenta')">
            <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  
     <div class="campoForm" id="detail-venta-content"></div>

</section>
<style>
    .btn-spinner {
    display: inline-block;
    width: 11px;
    height: 11px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin-btn 0.7s linear infinite;
    margin-right: 6px;
    vertical-align: middle;
}
@keyframes spin-btn {
    to { transform: rotate(360deg); }
}
</style>





