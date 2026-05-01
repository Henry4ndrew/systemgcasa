<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['codigoProd'])) {
    $codigo = $_POST['codigoProd'];
    try {
        $conexion->autocommit(false);
        $sqlImagenes = "SELECT ruta_imagen FROM imagenes WHERE codigo = ?";
        $stmtImagenes = $conexion->prepare($sqlImagenes);
        $stmtImagenes->bind_param("s", $codigo);
        $stmtImagenes->execute();
        $resultado = $stmtImagenes->get_result();
        while ($fila = $resultado->fetch_assoc()) {
            $rutaArchivo = $fila['ruta_imagen'];
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
        }
        $sqlDeleteImagenes = "DELETE FROM imagenes WHERE codigo = ?";
        $stmtDeleteImagenes = $conexion->prepare($sqlDeleteImagenes);
        $stmtDeleteImagenes->bind_param("s", $codigo);
        $stmtDeleteImagenes->execute();
        $sql = "DELETE FROM lista_productos WHERE codigo = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $conexion->commit();
        $_SESSION['mensaje'] = "Producto eliminado con éxito.";
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error al eliminar el producto: " . $e->getMessage();
    } finally {
        $conexion->autocommit(true);
    }
} else {
    $_SESSION['mensaje'] = "Error: Código de producto no válido.";
}
header("Location: ../b1t.php?p=art_productos.php");
exit;
?>
