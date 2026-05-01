<?php
session_start();
require '../includes/conexion.php';
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM medida WHERE id = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('i', $id); 
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Medida eliminada correctamente.";
            header("Location: ../b1t.php?p=config_options.php");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al eliminar la medida.";
            header("Location: ../b1t.php?p=config_options.php");
            exit;
        }
    } else {
        $_SESSION['mensaje'] = "Error en la preparación de la consulta.";
        header("Location: ../b1t.php?p=config_options.php");
        exit;
    }
} else {
    // Si no se ha enviado el ID
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=config_options.php");
    exit;
}

// Cerrar la conexión
$conexion->close();
?>

