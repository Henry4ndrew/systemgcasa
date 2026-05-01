<?php 
    require 'includes/conexion.php';
    date_default_timezone_set('America/La_Paz');

    $query_historial = "SELECT 
        p.id_pago, 
        p.id_venta, 
        p.id_user, 
        p.tipo_pago, 
        p.anticipo, 
        p.saldo, 
        p.fecha_sig_pago,
        c.id_cliente,
        DATE(p.fecha_pago_actual) AS solo_fecha,
        TIME(p.fecha_pago_actual) AS solo_hora,
        u.nombre_usuario AS nombre_usuario,
        c.nombre AS cliente_nombre,
        v.total_venta,
        c.empresa AS cliente_empresa
    FROM pagos p
    LEFT JOIN ventas v ON p.id_venta = v.id_venta
    LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
    LEFT JOIN usuarios u ON p.id_user = u.id_user
    ORDER BY p.fecha_pago_actual DESC";
    
    $resultado_historial = mysqli_query($conexion, $query_historial);
    $historial = [];
    if ($resultado_historial) { 
        while ($fila = mysqli_fetch_assoc($resultado_historial)) {
            $historial[] = $fila;
        }
        mysqli_free_result($resultado_historial);
    }
    mysqli_close($conexion);

?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/component-panel-verCliente.php'?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Historial de cobros</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input" placeholder="Ingrese nombre de cliente" oninput= "buscar2C3C('search-input', 'tablaHistCobros');">
                    <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
    </div>
</div>


<table class="tablaStyle top105" id="tablaHistCobros">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Empresa</th>
            <th>Anticipo</th>
            <th>Saldo</th>
            <th>Total venta</th>
            <th>Cobrado por</th>
            <th>Fecha último pago</th>
            <th>Detalle de venta</th>
        </tr>
    </thead>
   <tbody>
<?php foreach ($historial as $fila): ?>
    <tr>
        <td><?= $fila['id_pago'] ?></td>
        <td>
            <button type="button" onclick="mostrarCliente('<?= htmlspecialchars($fila['id_cliente']) ?>')" class="btn-invi">
                <?= $fila['cliente_nombre'] ?>
            </button>
        </td>
        <td><?= $fila['cliente_empresa'] ?></td>
        <td>
            <div class="centrar column">
              <p><?= $fila['anticipo'] ?></p>
              <p class="hora"><?= $fila['tipo_pago'] ?></p>
            </div>
        </td>
        <td><?= $fila['saldo'] ?></td>
        <td><b><?= $fila['total_venta'] ?></b></td>
        <td><p class="hora"><?= $fila['nombre_usuario'] ?></p></td>
        <td>
            <div class='column centrar'>
                <b><?= date('d-m-Y', strtotime($fila['solo_fecha'])) ?></b>
                <span class='hora'><?= $fila['solo_hora'] ?></span>
            </div>
        </td>
        <td>
            <button type='button' class='btn-invi' onclick="mostrarDetalleVenta('<?= $fila['id_venta'] ?>')">
                Ver detalles de la venta
            </button>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>
</table>




<section class="formStyle b-azul muy-grande" id="campoDetailVenta">
    <div class="cabecera">
      <h2 class="f-white" id="txtFormUser">Detalles de venta #<span id="num_venta"></span></h2>
      <button type="button" onclick="plop('campoDetailVenta')">
            <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  
     <div class="campoForm" id="detail-venta-content"></div>

</section>