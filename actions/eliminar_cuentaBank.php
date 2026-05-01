<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $imagenQR = $_POST['imagenQR'];
    if (file_exists($imagenQR)) {
        unlink($imagenQR); 
    }
    $sql = "DELETE FROM cuentas_bancarias WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Cuenta bancaria eliminada con éxito.";
    } else {
        $_SESSION['mensaje'] = "Error al eliminar la cuenta bancaria: " . $stmt->error;
    }
    $stmt->close();
    $conexion->close();
    header("Location: ../b1t.php?p=config_accounts.php");
    exit;
}
?>
