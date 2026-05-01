<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forzar cast a int para mayor seguridad
    $userId = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null; 

    // Validación básica
    if ($userId <= 0) {
        $_SESSION['mensaje'] = "Usuario inválido.";
        header("Location: ../b1t.php?p=usuarios.php");
    } elseif ($estado !== 'activo' && $estado !== 'inactivo') {
        $_SESSION['mensaje'] = "Valor de estado no válido.";
        header("Location: ../b1t.php?p=usuarios.php");
    } else {
        $sql = "UPDATE usuarios SET estado = ? WHERE id_user = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('si', $estado, $userId);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Estado del usuario actualizado a '$estado'.";
                header("Location: ../b1t.php?p=usuarios.php");
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el usuario: " . $stmt->error;
                header("Location: ../b1t.php?p=usuarios.php");
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error al preparar la consulta: " . $conexion->error;
            header("Location: ../b1t.php?p=usuarios.php");
        }
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=usuarios.php");
}
$conexion->close();
exit;
header("Location: ../b1t.php?p=usuarios.php");
?>


