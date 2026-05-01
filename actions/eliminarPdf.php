<?php
session_start();
require '../includes/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT archivo_pdf FROM documentos_pdf WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $archivoPdf = $fila['archivo_pdf'];
        $rutaArchivo = "../pdf/" . $archivoPdf;
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        $queryDelete = "DELETE FROM documentos_pdf WHERE id = ?";
        $stmtDelete = $conexion->prepare($queryDelete);
        $stmtDelete->bind_param("i", $id);
        if ($stmtDelete->execute()) {
            $_SESSION['mensaje'] = "PDF eliminado correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el PDF de la base de datos.";
        }
        $stmtDelete->close();
    } else {
        $_SESSION['mensaje'] = "El PDF no fue encontrado en la base de datos.";
    }

    $stmt->close();
} else {
    $_SESSION['mensaje'] = "ID no válido.";
}

$conexion->close();
header("Location: ../b1t.php?p=web_pdf.php");
exit;
?>
