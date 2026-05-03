<?php
session_start();
require '../includes/conexion.php'; 
date_default_timezone_set('America/La_Paz');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // 📌 CAPTURA DE DATOS
    // =========================
    $accion = $_POST['action'] ?? '';
    $id_feria = $_POST['id_feria'] ?? '';
    $nombre_feria = trim($_POST['nombre_feria'] ?? '');

    // Validación básica
    if (empty($nombre_feria)) {
        $_SESSION['mensaje'] = "El nombre de la feria es obligatorio.";
        header("Location: ../b1t.php?p=config_options.php");
        exit;
    }

    try {
        // =========================
        // 🔐 INICIAR TRANSACCIÓN
        // =========================
        $conexion->begin_transaction();

        // =========================
        // ➕ REGISTRAR
        // =========================
        if ($accion === 'registrar') {

            // Verificar duplicado
            $stmt = $conexion->prepare("SELECT id_feria FROM ferias WHERE nombre_feria = ?");
            $stmt->bind_param("s", $nombre_feria);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                throw new Exception("Ya existe una feria con ese nombre.");
            }
            $stmt->close();

            // Insertar
            $stmt = $conexion->prepare("INSERT INTO ferias (nombre_feria) VALUES (?)");
            $stmt->bind_param("s", $nombre_feria);

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar la feria.");
            }

            $_SESSION['mensaje'] = "Feria registrada correctamente.";
            $stmt->close();
        }

        // =========================
        // ✏️ EDITAR
        // =========================
        elseif ($accion === 'editar') {

            if (empty($id_feria)) {
                throw new Exception("ID de feria no válido.");
            }

            // Verificar duplicado (excluyendo el mismo ID)
            $stmt = $conexion->prepare("SELECT id_feria FROM ferias WHERE nombre_feria = ? AND id_feria != ?");
            $stmt->bind_param("si", $nombre_feria, $id_feria);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                throw new Exception("Ya existe otra feria con ese nombre.");
            }
            $stmt->close();

            // Actualizar
            $stmt = $conexion->prepare("UPDATE ferias SET nombre_feria = ? WHERE id_feria = ?");
            $stmt->bind_param("si", $nombre_feria, $id_feria);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar la feria.");
            }

            $_SESSION['mensaje'] = "Feria actualizada correctamente.";
            $stmt->close();
        }

        else {
            throw new Exception("Acción no válida.");
        }

        // =========================
        // ✅ CONFIRMAR TRANSACCIÓN
        // =========================
        $conexion->commit();

    } catch (Exception $e) {

        // ❌ REVERTIR TRANSACCIÓN
        $conexion->rollback();

        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    }

    $conexion->close();
    header("Location: ../b1t.php?p=config_options.php");
    exit;
}
?>