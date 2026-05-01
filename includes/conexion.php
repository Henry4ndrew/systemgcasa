<?php
    $servidor = "localhost";
    $usuario = "root"; 
    $contrasena = ""; 
    $base_datos = "casa_club";
    $puerto = 3306; 
    $conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos, $puerto);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
?>

