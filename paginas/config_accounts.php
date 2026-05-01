<?php
require 'includes/conexion.php';

$sqlCuentas = "SELECT id, titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR, titular FROM cuentas_bancarias";
$accounts = $conexion->query($sqlCuentas);
$cuentas = [];
if ($accounts->num_rows > 0) {
    while ($fila = $accounts->fetch_assoc()) {
        $cuentas[] = $fila;
    }
}
?>


<?php include 'includes/permisos.php' ?>
<?php include 'forms/addBank.php' ?>

<h3 class="b-naranja f-white pad-left20">Cuentas bancarias QR</h3>
<div class="b-azul pad20 cont-elemts">
    <button class="btn-load orange" onclick="mostrarForm('formBank');"><span>Agregar código QR</span></button>
</div>

     <table class="tablaStyle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titular</th>
                    <th>Número de Cuenta</th>
                    <th>Banco</th>
                    <th>QR</th>
                    <th>Fecha de Caducidad</th>
                    <th>Qr tienda</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuentas as $cuenta): ?>
                    <tr>
                        <td><?= htmlspecialchars($cuenta['id']) ?></td>
                        <td><span class="hora"><?= htmlspecialchars($cuenta['titularCuenta']) ?></span></td>
                        <td><span class="hora"><?= htmlspecialchars($cuenta['numeroCuenta']) ?></span></td>
                        <td><?= htmlspecialchars($cuenta['nombreBanco']) ?></td>
                        <td>
                            <?php $imagen = str_replace('../', '', $cuenta['imagenQR']);?>
                            <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($imagen) ?>" style="width:100px;">
                            <p class="hora" id="qr-<?= htmlspecialchars($cuenta['id']) ?>" style="display:none"><?= htmlspecialchars($imagen) ?></p>
                        </td>
                        <td><b class="centrar"><?= date('d-m-Y', strtotime($cuenta['fechaCaducidadQR'])) ?></b></td>
                        <td><span class="centrar"><?= htmlspecialchars($cuenta['titular']) ?></span></td>
                        <td>
                            <div class="separador">
                                <button type="button" 
                                        onclick="editarCuenta(<?= htmlspecialchars(json_encode($cuenta), ENT_QUOTES, 'UTF-8'); ?>)"
                                        class="btn-load azul">
                                    <span><i class="fa-solid fa-pencil"></i></span>
                                </button>
                                <!-- Formulario para eliminar cuenta -->
                                <form action="actions/eliminar_cuentaBank.php" method="POST" class="centrar" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta cuenta?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($cuenta['id']) ?>">
                                    <input type="hidden" name="imagenQR" value="<?= htmlspecialchars($cuenta['imagenQR']) ?>">
                                    <button type="submit" class="btn-load rojo">
                                    <span><i class="fa-solid fa-trash"></i></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>