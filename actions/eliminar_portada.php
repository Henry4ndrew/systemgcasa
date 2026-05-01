<?php
session_start();
require '../includes/conexion.php';
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = $_POST['id'];
    $query = "SELECT ruta_img FROM portada WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ruta_img = $row['ruta_img'];
        if (file_exists($ruta_img)) {
            unlink($ruta_img); 
        }
        $deleteQuery = "DELETE FROM portada WHERE id = ?";
        $deleteStmt = $conexion->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);
        if ($deleteStmt->execute()) {
            $_SESSION['mensaje'] = "Portada eliminada correctamente";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar la portada";
        }
    } else {
        $_SESSION['mensaje'] = "No se encontró la portada";
    }
    header("Location: ../b1t.php?p=web_portada.php");
    exit;
}
$conexion->close();
?>
