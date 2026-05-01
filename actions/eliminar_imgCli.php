<?php
session_start();
require '../includes/conexion.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $consulta = $conexion->prepare("SELECT url_img FROM img_section_clientes WHERE id = ?");
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $consulta->store_result();
    $consulta->bind_result($url_img);
    if ($consulta->num_rows > 0) {
        $consulta->fetch();
        $ruta_completa = '../' . $url_img;
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }
        $eliminar = $conexion->prepare("DELETE FROM img_section_clientes WHERE id = ?");
        $eliminar->bind_param("i", $id);
        $eliminar->execute();
        if ($eliminar->affected_rows > 0) {
            $_SESSION['mensaje'] = "La imagen ha sido eliminada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar la imagen.";
        }
    } else {
        $_SESSION['mensaje'] = "No se encontró la imagen.";
    }
    $conexion->close();
    header("Location: ../b1t.php?p=web_data.php");
    exit;
} else {
    $_SESSION['mensaje'] = "No se ha proporcionado un ID válido.";
    header("Location: ../b1t.php?p=web_data.php");
    exit;
}
?>
