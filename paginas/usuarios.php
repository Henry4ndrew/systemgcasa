<?php
    require 'includes/conexion.php';
    $sql = "SELECT id_user, nombre_usuario, celular, permiso, estado 
        FROM usuarios 
        ORDER BY (estado = 'inactivo'), nombre_usuario ASC";
    $result = $conexion->query($sql);
    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row; 
        }
    }
?>



<?php include 'includes/permisos.php' ?>
<?php include 'forms/addUsers.php'; ?>

<div class="panel">
    <h3 class="b-naranja f-white pad-left20">Usuarios del sistema</h3>
    <div class="b-azul pad20 cont-elemts">
        <div class="search-box">
            <div class="input-wrapper">
                <input oninput="buscar1C2C('search-user', 'tablaUsers')" class="input padInput" type="text" id="search-user" placeholder="Ingrese # o nombre">
                    <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <button onclick="mostrarForm('formUser')" class="btn-load orange"><span>Agregar usuario</span></button>
    </div>
</div>

<table class="tablaStyle top105" id="tablaUsers">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Celular</th>
            <th>Permiso</th>
            <th><span class="centrar">Acciones</span></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $usuario): ?>
        <tr>
            <td><?= htmlspecialchars($usuario['id_user']) ?></td>
            <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
            <td><?= htmlspecialchars($usuario['celular']) ?></td>
            <td><?= htmlspecialchars($usuario['permiso']) ?></td>
            <td>
              <form action="actions/cambiar_estadoUser.php" class="formFunctions" method="post" onsubmit="return confirm('¿Desea cambiar el estado?');">
                <input type="hidden" name="id_user" value="<?= htmlspecialchars($usuario['id_user']) ?>" readonly>
                <input type="hidden" name="estado" class="estado-input" value="<?= $usuario['estado'] === 'activo' ? 'activo' : 'inactivo' ?>" readonly>

                <button type="button" class="btn-load azul" 
                    onclick="editarUser('<?= $usuario['id_user'] ?>', 
                              '<?= htmlspecialchars($usuario['nombre_usuario']) ?>', 
                              '<?= htmlspecialchars($usuario['celular']) ?>','<?= htmlspecialchars($usuario['permiso']) ?>')">
                      <span><i class="fa-solid fa-pencil"></i></span>
                </button>  

                <label class="switch">
                  <input type="checkbox" class="estado-checkbox" <?= $usuario['estado'] === 'activo' ? 'checked' : '' ?> onchange="stateSwitch(this)">
                  <span class="fondo-switch"></span>
                  <span class="label-text">
                    <?= $usuario['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                  </span>
                </label>
                
              </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>





