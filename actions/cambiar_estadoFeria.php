<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idFeria = isset($_POST['id_feria2']) ? (int) $_POST['id_feria2'] : 0;
    $estado = $_POST['estado'] ?? null;
    if ($idFeria <= 0) {
        $_SESSION['mensaje'] = "Feria inválida.";
    } 
    elseif ($estado !== 'activo' && $estado !== 'inactivo') {
        $_SESSION['mensaje'] = "Estado no válido.";
    } 
    else {
        $sql = "UPDATE ferias SET estado = ? WHERE id_feria = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('si', $estado, $idFeria);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Estado de la feria actualizado a '$estado'.";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error en la consulta: " . $conexion->error;
        }
    }
} else {
    $_SESSION['mensaje'] = "Método no permitido.";
}
$conexion->close();
header("Location: ../b1t.php?p=config_options.php");
exit;
?>