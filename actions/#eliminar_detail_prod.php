<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_detalle_prod = $_POST['id_detalle_prod'];
    if (!empty($id_detalle_prod)) {
        $sql = "DELETE FROM detalle_producto WHERE id_detalle_prod = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("i", $id_detalle_prod);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Detalle de producto eliminado exitosamente.";
            } else {
                $_SESSION['mensaje'] = "Error al eliminar detalle de producto.";
            }
            $stmt->close();
        } else {
            $_SESSION['mensaje'] = "Error al preparar la consulta.";
        }
    } else {
        $_SESSION['mensaje'] = "No se ha proporcionado un ID válido para eliminar.";
    }
    header("Location: ../b1t.php?p=art_productos.php");
    exit;
}
$conexion->close();
?>
