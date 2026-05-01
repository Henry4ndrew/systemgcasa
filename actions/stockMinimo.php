<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stockMinimo = $_POST['stockMinimo'];
    if (is_numeric($stockMinimo)) {
        $sql_update = "UPDATE datos SET stockMinimo = ? LIMIT 1";
        $stmt = $conexion->prepare($sql_update);
        $stmt->bind_param("i", $stockMinimo);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Stock mínimo actualizado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar el stock mínimo.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "El valor ingresado no es válido.";
    }
    header("Location: ../b1t.php?p=config_options.php");
    exit;
}
?>
