<?php
session_start();
require '../includes/conexion.php';
date_default_timezone_set('America/La_Paz');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = $_POST['codigo'] ?? null;
    $idDetalle = $_POST['idDetalle'] ?? null;
    $idFeria = $_POST['idFeria'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;

    // Validar que todos los datos estén presentes
    if (!$codigo || !$idDetalle || !$idFeria || $cantidad === null) {
        $_SESSION['mensaje'] = "Datos incompletos. Faltan campos requeridos.";
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: ../b1t.php?p=stock_ferias.php");
        exit;
    }

    // Validar que la cantidad sea un número válido
    if (!is_numeric($cantidad) || $cantidad < 0) {
        $_SESSION['mensaje'] = "La cantidad debe ser un número válido mayor o igual a 0.";
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: ../b1t.php?p=stock_ferias.php");
        exit;
    }

    // Convertir a tipos apropiados
    $codigo = $conexion->real_escape_string($codigo);
    $idDetalle = (int)$idDetalle;
    $idFeria = (int)$idFeria;
    $cantidad = (int)$cantidad;

    // Primero verificar si el registro existe con el id_feria específico
    $queryCheck = "SELECT id_almacen FROM almacen_ferias 
                   WHERE codigo = ? AND id_detalle = ? AND id_feria = ?";
    
    $stmtCheck = $conexion->prepare($queryCheck);
    $stmtCheck->bind_param("sii", $codigo, $idDetalle, $idFeria);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows === 0) {
        $_SESSION['mensaje'] = "No se encontró el producto en la feria especificada.";
        $_SESSION['mensaje_tipo'] = "error";
        $stmtCheck->close();
        $conexion->close();
        header("Location: ../b1t.php?p=stock_ferias.php");
        exit;
    }
    $stmtCheck->close();

    // Actualizar cantidad y fecha, incluyendo id_feria en la condición
    $query = "UPDATE almacen_ferias 
              SET cantidad = ?, fecha_modificacion = NOW()
              WHERE codigo = ? AND id_detalle = ? AND id_feria = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("isii", $cantidad, $codigo, $idDetalle, $idFeria);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = "Cantidad actualizada correctamente para la feria.";
            $_SESSION['mensaje_tipo'] = "exito";
        } else {
            $_SESSION['mensaje'] = "No se realizaron cambios. La cantidad es la misma o el producto no existe.";
            $_SESSION['mensaje_tipo'] = "info";
        }
    } else {
        $_SESSION['mensaje'] = "Error al actualizar: " . $stmt->error;
        $_SESSION['mensaje_tipo'] = "error";
    }

    $stmt->close();
    $conexion->close();

    header("Location: ../b1t.php?p=stock_ferias.php");
    exit;
}
?>