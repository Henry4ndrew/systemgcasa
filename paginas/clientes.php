<?php
    require 'includes/conexion.php';

    $sql = "SELECT id_cliente, nombre, nit, carnet_ci, departamento, celular,
        cel_empresa, correo, empresa, nota, fecha_registro,
        DATE(fecha_registro) AS solo_fecha,
        TIME(fecha_registro) AS solo_hora,
        estado 
    FROM cartera_clientes
    WHERE estado = 'activo'
    ORDER BY fecha_registro DESC";
        
    $result = $conexion->query($sql);
    $clientes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row; 
        }
    }
?>

<?php include 'includes/permisos.php' ?>
<?php include 'forms/addCliente.php'; ?>


<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Lista de clientes</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input oninput="buscar2C3C('search-clientes', 'tablaClientes')" class="input padInput" type="text" id="search-clientes" placeholder="Ingrese Nombre o #">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <button class="btn-load orange" onclick="mostrarForm('formCliente')"><span>Registrar cliente</span></button>
        
        
        <!-- Botón de exportar con formulario -->
        <form id="formExportarPDF" action="functions/exportar_clientes_pdf.php" method="post" target="_blank" style="display: inline;">
            <input type="hidden" name="clientes_seleccionados" id="clientesSeleccionados">
            <button type="button" class="btn-load orange" onclick="exportarSeleccionados()">
                <span><i class="fa-solid fa-file-pdf"></i> Exportar seleccionados</span>
            </button>
        </form>
    </div>
</div>

<table class="tablaStyle top105" id="tablaClientes">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="checkAll" onchange="toggleAllCheckboxes(this)">
            </th>
            <th>#</th>
            <th>Nombre</th>
            <th>Celular personal</th>
            <th>Celular empresa</th>
            <th>Empresa</th>
            <th>Departamento</th>
            <th><div class="f-center">Fecha Registro</div></th>
            <th><span class="centrar">Acciones</span></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente): ?>
            <tr>
                <!-- Checkbox para selección -->
                <td>
                    <input type="checkbox" class="cliente-checkbox" 
                           name="cliente_<?= $cliente['id_cliente']; ?>" 
                           value="<?= $cliente['id_cliente']; ?>"
                           data-nombre="<?= htmlspecialchars($cliente['nombre']); ?>"
                           data-celular="<?= htmlspecialchars($cliente['celular']); ?>"
                           data-empresa="<?= htmlspecialchars($cliente['empresa']); ?>"
                           data-departamento="<?= htmlspecialchars($cliente['departamento']); ?>">
                </td>
                <td><?= htmlspecialchars($cliente['id_cliente']); ?></td>
                <td>
                    <button type="button" onclick="mostrarCliente('<?= htmlspecialchars($cliente['id_cliente']); ?>')" class="btn-invi">
                        <?= htmlspecialchars($cliente['nombre']); ?>
                    </button>
                </td>
                <td><?= htmlspecialchars($cliente['celular']); ?></td>
                <td><?= htmlspecialchars($cliente['cel_empresa']); ?></td>
                <td><?= htmlspecialchars($cliente['empresa']); ?></td>
                <td><?= htmlspecialchars($cliente['departamento']); ?></td>
                <td>
                    <div class="column centrar">
                        <b><?= date('d-m-Y', strtotime($cliente['solo_fecha'])) ?></b>
                        <span class="hora"><?= htmlspecialchars($cliente['solo_hora']) ?></span>
                    </div>
                </td>
                <td>
                    <form action="actions/cambiar_estadoCliente.php" class="formFunctions" method="post" onsubmit="return confirm('¿Desea cambiar el estado?');">
                        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']); ?>">
                        <input type="hidden" name="estado" class="estado-input" value="<?= $cliente['estado'] === 'activo' ? 'activo' : 'inactivo' ?>" readonly>
                        <button type="button" 
                                onclick="editarCliente(<?= htmlspecialchars(json_encode($cliente), ENT_QUOTES, 'UTF-8'); ?>)"
                                class="btn-load azul">
                            <span><i class="fa-solid fa-pencil"></i></span>
                        </button>
                        <label class="switch">
                            <input type="checkbox" class="estado-checkbox" <?= $cliente['estado'] === 'activo' ? 'checked' : '' ?> onchange="stateSwitch(this)">
                            <span class="fondo-switch"></span>
                            <span class="label-text">
                                <?= $cliente['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </label>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>






<!-- Panel para verl os detalles de un Cliente -->
<section class="formStyle b-gold mediano" id="campoDetailCliente">
    <div class="cabecera">
      <h2 class="f-white" id="txtFormCliente">Cliente</h2>
      <button type="button" onclick="plop('campoDetailCliente')">
            <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

     <div class="campoForm" id="detail-venta-cliente"></div>   

</section>

