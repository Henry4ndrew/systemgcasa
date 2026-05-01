<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'];

    // Validación básica (imagen solo obligatoria al registrar)
    if (
        empty($_POST['titularCuenta']) || 
        empty($_POST['numeroCuenta']) || 
        empty($_POST['nombreBanco']) || 
        empty($_POST['fechaCaducidadQR'])
    ) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: ../b1t.php?p=config_accounts.php");
        exit;
    }

    if ($action == "registrar" && empty($_FILES['imagenQR']['name'])) {
        $_SESSION['mensaje'] = "La imagen QR es obligatoria.";
        header("Location: ../b1t.php?p=config_accounts.php");
        exit;
    }

    $titularCuenta = trim($_POST['titularCuenta']);
    $numeroCuenta = trim($_POST['numeroCuenta']);
    $nombreBanco = trim($_POST['nombreBanco']);
    $fechaCaducidadQR = $_POST['fechaCaducidadQR'];
    $titularQrTienda = $_POST['titularQrTienda'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {

        // 🔥 Si es titular = si → resetear todos
        if ($titularQrTienda === "si") {
            $conexion->query("UPDATE cuentas_bancarias SET titular = 'no'");
        }

        // =========================
        // 🔹 REGISTRAR
        // =========================
        if ($action == "registrar") {

            $extension = pathinfo($_FILES['imagenQR']['name'], PATHINFO_EXTENSION);
            $nombreUnico = substr(md5(uniqid()), 0, 10) . "." . $extension;
            $rutaImagenQR = "../img/" . $nombreUnico;

            move_uploaded_file($_FILES['imagenQR']['tmp_name'], $rutaImagenQR);

            $sql = "INSERT INTO cuentas_bancarias 
                    (titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR, titular) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssss", $titularCuenta, $numeroCuenta, $nombreBanco, $rutaImagenQR, $fechaCaducidadQR, $titularQrTienda);

            $stmt->execute();
            $stmt->close();

            $_SESSION['mensaje'] = "Cuenta bancaria registrada con éxito.";
        }

        // =========================
        // 🔹 EDITAR
        // =========================
        if ($action == "editar") {

            $id = $_POST['id_cuenta'];

            // Obtener imagen actual
            $sqlImg = "SELECT imagenQR FROM cuentas_bancarias WHERE id = ?";
            $stmtImg = $conexion->prepare($sqlImg);
            $stmtImg->bind_param("i", $id);
            $stmtImg->execute();
            $resultado = $stmtImg->get_result();
            $fila = $resultado->fetch_assoc();
            $imagenActual = $fila['imagenQR'];
            $stmtImg->close();

            $rutaImagenQR = $imagenActual;

            // Si sube nueva imagen
            if (!empty($_FILES['imagenQR']['name'])) {

                $extension = pathinfo($_FILES['imagenQR']['name'], PATHINFO_EXTENSION);
                $nombreUnico = substr(md5(uniqid()), 0, 10) . "." . $extension;
                $nuevaRuta = "../img/" . $nombreUnico;

                move_uploaded_file($_FILES['imagenQR']['tmp_name'], $nuevaRuta);

                // Eliminar imagen anterior
                if (file_exists($imagenActual)) {
                    unlink($imagenActual);
                }

                $rutaImagenQR = $nuevaRuta;
            }

            $sql = "UPDATE cuentas_bancarias 
                    SET titularCuenta=?, numeroCuenta=?, nombreBanco=?, imagenQR=?, fechaCaducidadQR=?, titular=? 
                    WHERE id=?";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssssi", $titularCuenta, $numeroCuenta, $nombreBanco, $rutaImagenQR, $fechaCaducidadQR, $titularQrTienda, $id);

            $stmt->execute();
            $stmt->close();

            $_SESSION['mensaje'] = "Cuenta actualizada correctamente.";
        }

        // Confirmar todo
        $conexion->commit();

    } catch (Exception $e) {
        // Revertir todo si algo falla
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    }

    $conexion->close();
    header("Location: ../b1t.php?p=config_accounts.php");
    exit;
}
?>