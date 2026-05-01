<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_almacen'])) {
        $id_almacen = intval($_POST['id_almacen']);
        $sql = "DELETE FROM almacen_tienda WHERE id_almacen = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_almacen);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Producto eliminado correctamente del almacén - Tienda.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el almacén - Tienda.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "No se recibió el ID del almacén.";
    }
} else {
    $_SESSION['mensaje'] = "Método no permitido.";
}
header("Location: ../b1t.php?p=stock_tienda.php");
exit;
?>
