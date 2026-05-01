<?php 
    require 'includes/conexion.php';
    date_default_timezone_set('America/La_Paz');

    // Consulta corregida para obtener solo ventas con saldo pendiente
$query_pendientes = "SELECT 
    p.id_pago, 
    p.id_venta, 
    p.id_user, 
    p.tipo_pago, 
    p.anticipo, 
    p.saldo, 
    p.fecha_sig_pago,
    p.fecha_pago_actual,
    DATE(p.fecha_pago_actual) AS solo_fecha,
    TIME(p.fecha_pago_actual) AS solo_hora,
    v.total_venta, 
    u.nombre_usuario AS nombre_usuario,
    c.id_cliente,
    c.nombre AS cliente_nombre,
    c.empresa AS cliente_empresa,
    CASE 
        WHEN p.fecha_sig_pago < CURDATE() THEN 'vencido'
        WHEN DATEDIFF(p.fecha_sig_pago, CURDATE()) <= 3 THEN 'proximo'
        ELSE 'lejano'
    END AS estado_pago
FROM pagos p
INNER JOIN (
    -- Subconsulta para obtener el último pago de cada venta CON SALDO PENDIENTE
    SELECT p2.id_venta, MAX(p2.fecha_pago_actual) as ultima_fecha_pago
    FROM pagos p2
    WHERE p2.id_venta IN (
        -- Solo ventas que todavía tienen saldo pendiente
        SELECT DISTINCT id_venta 
        FROM pagos 
        WHERE saldo > 0
    )
    GROUP BY p2.id_venta
) ultimos_pagos ON p.id_venta = ultimos_pagos.id_venta 
    AND p.fecha_pago_actual = ultimos_pagos.ultima_fecha_pago
LEFT JOIN ventas v ON p.id_venta = v.id_venta
LEFT JOIN cartera_clientes c ON v.id_cliente = c.id_cliente
LEFT JOIN usuarios u ON p.id_user = u.id_user
WHERE p.saldo > 0  -- Asegurar que el pago mostrado tenga saldo pendiente
ORDER BY p.fecha_pago_actual DESC";
    
    $resultado_pendientes = mysqli_query($conexion, $query_pendientes);

// Crear un arreglo para almacenar todos los resultados
$pagos_pendientes = array();

while($row = mysqli_fetch_assoc($resultado_pendientes)) {
    $pagos_pendientes[] = $row;
}


// Función para formatear montos y decimales usados en la consulta
function formatearMonto($monto) {
    $numero = floatval($monto);
    $formateado = number_format($numero, 2, '.', '');
    if (strpos($formateado, '.') !== false) {
        $formateado = rtrim($formateado, '0');
        $formateado = rtrim($formateado, '.');
    }
    return $formateado;
}
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/formCobro.php' ?>
<?php include 'forms/component-panel-verCliente.php'?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Cobros pendientes</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input class="input padInput" type="text" id="search-input" placeholder="Ingrese nombre o código" oninput= "buscar2C3C('search-input', 'tabla-cobros');">
                    <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <div class="filtro-box">
            <label for="filtro-estado">Filtrar por estado:</label>
                <select id="filtro-estado" onchange="filtrarTablaPorEstado()">
                    <option value="">Todos los cobros</option>
                    <option value="proximo">Próximos</option>
                    <option value="vencido">Vencidos</option>
                    <option value="lejano">Lejanos</option>
                </select>
        </div>
    </div>
</div>


   <table id="tabla-cobros" class="tablaStyle top105">
    <thead>
        <tr>
            <th>Cobrar</th>
            <th>Cliente</th>
            <th>Empresa</th>
            <th>Total Venta</th>
            <th>Saldo Pendiente</th>
            <th>Próximo Pago</th>
            <th>Estado</th>
            <th>Usuario</th>
            <th>Último Pago Realizado</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($pagos_pendientes) > 0): ?>
            <?php foreach($pagos_pendientes as $pago): ?>
                <?php
                // Determinar la clase CSS según el estado
                $clase_estado = '';
                switch($pago['estado_pago']) {
                    case 'vencido': $clase_estado = 'vencido'; break;
                    case 'proximo': $clase_estado = 'proximo'; break;
                    case 'lejano': $clase_estado = 'lejano'; break;
                }
                
                // Formatear fechas
                $fecha_sig_pago = date('d-m-Y', strtotime($pago['fecha_sig_pago']));
                $ultimo_pago = date('d-m-Y', strtotime($pago['fecha_pago_actual']));
                
                // Formatear montos con la nueva función
                $total_venta = formatearMonto($pago['total_venta']);
                $saldo = formatearMonto($pago['saldo']);
                
                // Para el botón necesitamos el saldo sin formato para el JavaScript
                $saldo_para_js = $pago['saldo']; // Usar el valor original sin formatear
                ?>
                
                <tr class="<?php echo $clase_estado; ?>">
                    <td>
                        <button class='btn-invi' 
                            data-cobro='<?php echo htmlspecialchars(json_encode($pago), ENT_QUOTES, 'UTF-8'); ?>'
                            onclick="mostrarformCobro(this)">
                            Realizar Pago
                        </button>
                    </td>
                    <td>
                        <button type="button" onclick="mostrarCliente('<?php echo htmlspecialchars($pago['id_cliente']); ?>')" class="btn-invi">
                            <?php echo htmlspecialchars($pago['cliente_nombre']); ?>
                        </button>
                    </td>
                    <td><p class="f-peq"><?php echo htmlspecialchars($pago['cliente_empresa']); ?></p></td>
                    <td><?php echo $total_venta; ?> Bs</td>
                    <td><?php echo $saldo; ?> Bs</td>
                    <td><b> <?php echo $fecha_sig_pago; ?> </b></td>
                    <td>
                        <span class="badge estado-<?php echo $pago['estado_pago']; ?>">
                            <?php echo ucfirst($pago['estado_pago']); ?>
                        </span>
                    </td>
                    <td><p class="hora"><?php echo htmlspecialchars($pago['nombre_usuario']); ?></p></td>
                    <td>
                        <div class='column centrar'>
                            <b> <?php echo $ultimo_pago; ?> </b>
                            <span class="hora"><?php echo htmlspecialchars($pago['solo_hora']); ?></hora>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($pago['id_pago']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" style="text-align: center;">
                    No hay pagos pendientes.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<br>

    <?php
    if(count($pagos_pendientes) > 0) {
        $vencidos = array_filter($pagos_pendientes, function($pago) {
            return $pago['estado_pago'] == 'vencido';
        });
        
        $proximos = array_filter($pagos_pendientes, function($pago) {
            return $pago['estado_pago'] == 'proximo';
        });
        
        echo "<div class='pad20 b-blank mediano'>";
        echo "<p><strong>Resumen de pagos pendientes:</strong></p>";
        echo "<p>Total ventas con saldo pendiente: " . count($pagos_pendientes) . "</p>";
        echo "<p>Vencidos: " . count($vencidos) . "</p>";
        echo "<p>Próximos a vencer (3 días o menos): " . count($proximos) . "</p>";
        echo "</div>";
    }
    ?>

<style>
tr.vencido {
    background-color: #f8d7da;
    color: #ca0014ff;
}

tr.lejano {
    background-color: #d4edda;
    color: #0f8029ff; 
}
</style>

<section class="formStyle b-gold muy-grande" id="campoDetailVenta">
    <div class="cabecera">
      <h2 class="f-white" id="txtFormUser">Detalles de venta #<span id="num_venta"></span></h2>
      <button type="button" onclick="plop('campoDetailVenta')">
            <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  
     <div class="campoForm" id="detail-venta-content"></div>

</section>




