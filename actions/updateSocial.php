<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $tiktok = $_POST['tiktok'];
    $celularTienda = $_POST['celularTienda'];
    $celularFabrica = $_POST['celularFabrica'];
    $direccionFabrica = $_POST['direccionFabrica'];
    $correoElectronico = $_POST['correoElectronico'];
    $enlaceGPSTienda = $_POST['enlaceGPSTienda'];
    $enlaceGPSFabrica = $_POST['enlaceGPSFabrica'];

    $sql = "UPDATE datos
            SET facebook = ?, instagram = ?, tiktok = ?, celular_tienda = ?, celular_fabrica = ?, 
                direccion_fabrica = ?, email = ?, gps_tienda = ?, gps_fabrica = ?
            WHERE id = 1";  
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("sssssssss", $facebook, $instagram, $tiktok, $celularTienda, $celularFabrica, 
                          $direccionFabrica, $correoElectronico, $enlaceGPSTienda, $enlaceGPSFabrica);
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Datos actualizados correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar los datos.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error en la preparación de la consulta de actualización.";
    }
    header("Location: ../b1t.php?p=web_data.php");
    exit;
}
$conexion->close();
?>
