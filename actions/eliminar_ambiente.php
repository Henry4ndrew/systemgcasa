<?php
session_start();
require '../includes/conexion.php';
if (isset($_POST['id_lugar'])) {
    $id = $_POST['id_lugar'];
    $sql = "DELETE FROM ambiente WHERE id_lugar = ?";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param('i', $id); 
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Ambiente eliminado correctamente.";
            header("Location: ../b1t.php?p=config_options.php");
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el Ambiente.";
            header("Location: ../b1t.php?p=config_options.php");
            exit;
        }
    } else {
        $_SESSION['mensaje'] = "Error en la preparación de la consulta.";
        header("Location: ../b1t.php?p=config_options.php");
        exit;
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=config_options.php");
    exit;
}
$conexion->close();
?>

