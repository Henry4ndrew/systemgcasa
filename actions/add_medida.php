<?php
session_start(); 
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $medida_disp = $_POST['medida_disp'];
    if (!empty($medida_disp)) {
        $sql = "INSERT INTO medida (medida_disp) VALUES (?)";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $medida_disp);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Medida agregada correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al agregar la medida.";
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error al preparar la consulta.";
        }
    } else {
        $_SESSION['mensaje'] = "El campo de medida no puede estar vacío.";
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
