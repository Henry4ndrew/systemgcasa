<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar datos
    $idCliente = isset($_POST['id_cliente']) ? (int) $_POST['id_cliente'] : 0;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;

    // Validación básica
    if ($idCliente <= 0) {
        $_SESSION['mensaje'] = "Cliente inválido.";
        header("Location: ../b1t.php?p=clientes.php");
        exit;
    } elseif ($estado !== 'activo' && $estado !== 'inactivo') {
        $_SESSION['mensaje'] = "Valor de estado no válido.";
        header("Location: ../b1t.php?p=clientes.php");
        exit;
    } else {
        // Cambiar estado
        $sql = "UPDATE cartera_clientes SET estado = ? WHERE id_cliente = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('si', $estado, $idCliente);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Estado del cliente actualizado a '$estado'.";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el cliente: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error al preparar la consulta: " . $conexion->error;
        }

        header("Location: ../b1t.php?p=clientes.php");
        exit;
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=clientes.php");
    exit;
}
$conexion->close();
exit;
?>


