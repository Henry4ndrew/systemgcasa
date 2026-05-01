<?php
    $servidor = "localhost";
    $usuario = "tu_usuario"; 
    $contrasena = "tu_password"; 
    $base_datos = "tu_base_de_datos"; 

    $conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
?>
