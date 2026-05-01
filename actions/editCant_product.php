<?php
session_start();
require '../includes/conexion.php';
date_default_timezone_set('America/La_Paz');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = $_POST['codigo'] ?? null;
    $idDetalle = $_POST['idDetalle'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;

    if (!$codigo || !$idDetalle || $cantidad === null) {
        $_SESSION['mensaje'] = "Datos incompletos.";
        header("Location: ../b1t.php?p=stock_productos.php");
        exit;
    }

    // Actualizar cantidad y fecha
    $query = "UPDATE almacen 
              SET cantidad = ?, fecha_modificacion = NOW()
              WHERE codigo = ? AND id_detalle = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("iss", $cantidad, $codigo, $idDetalle);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Cantidad actualizada correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();

    header("Location: ../b1t.php?p=stock_productos.php");
    exit;
}
