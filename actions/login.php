<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $conexion->real_escape_string(trim($_POST['usuario']));
    $contrasena = $conexion->real_escape_string(trim($_POST['contrasena']));

    if (empty($usuario) || empty($contrasena)) {
        $_SESSION['mensaje'] = "Por favor, complete todos los campos.";
        header("Location: ../index.php");
        exit;
    }
    // Agregamos "AND estado = 'activo'" en la consulta
    $sql = "SELECT id_user, nombre_usuario, contrasena, permiso, estado 
            FROM usuarios 
            WHERE nombre_usuario = ? AND estado = 'activo'";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        if (password_verify($contrasena, $fila['contrasena'])) {
            $_SESSION['id_usuario'] = $fila['id_user'];
            $_SESSION['nombre_usuario'] = $fila['nombre_usuario'];
            $_SESSION['permiso'] = $fila['permiso'];
            session_write_close();

            if ($fila['permiso'] === 'administrador') {
                header("Location: ../b1t.php?p=dashboard.php");
            } elseif ($fila['permiso'] === 'ventas') {
                header("Location: ../b1t.php?p=dashboard.php");
            }
            exit;
        } else {
            $_SESSION['mensaje'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['mensaje'] = "Usuario inhabilitado.";
    }
    header("Location: ../index.php");
    exit;
}
?>
