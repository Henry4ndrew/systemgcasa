<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['titularCuenta']) || empty($_POST['numeroCuenta']) || empty($_POST['nombreBanco']) || empty($_POST['fechaCaducidadQR']) || empty($_FILES['imagenQR']['name'])) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios.";
        header("Location: ../b1t.php?p=config_accounts.php");
        exit;
    }
    $titularCuenta = trim($_POST['titularCuenta']);
    $numeroCuenta = trim($_POST['numeroCuenta']);
    $nombreBanco = trim($_POST['nombreBanco']);
    $fechaCaducidadQR = $_POST['fechaCaducidadQR'];
    $directorio = "../img/";
    
    $extension = pathinfo($_FILES['imagenQR']['name'], PATHINFO_EXTENSION);
    $nombreUnico = substr(md5(uniqid()), 0, 10) . "." . $extension;
    $rutaImagenQR = $directorio . $nombreUnico;
    move_uploaded_file($_FILES['imagenQR']['tmp_name'], $rutaImagenQR);
    
    $sql = "INSERT INTO cuentas_bancarias (titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $titularCuenta, $numeroCuenta, $nombreBanco, $rutaImagenQR, $fechaCaducidadQR);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Cuenta bancaria registrada con éxito.";
    } else {
        $_SESSION['mensaje'] = "Error al guardar los datos: " . $stmt->error;
    }
    $stmt->close();
    $conexion->close();
    header("Location: ../b1t.php?p=config_accounts.php");
    exit;
}
?>
