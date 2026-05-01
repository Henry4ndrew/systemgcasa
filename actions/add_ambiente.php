<?php
session_start(); 
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ambiente_disp = $_POST['ambiente'];
    if (!empty($ambiente_disp)) {
        $sql = "INSERT INTO ambiente (lugar) VALUES (?)";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $ambiente_disp);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Ambiente agregado correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al agregar el ambiente.";
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error al preparar la consulta.";
        }
    } else {
        $_SESSION['mensaje'] = "El campo de ambiente no puede estar vacío.";
    }
    header("Location: ../b1t.php?p=config_options.php");
    exit;
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=config_options.php");
    exit;
}
$conexion->close();
?>
