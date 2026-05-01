<?php
    $servidor = "localhost";
    $usuario = "gcasaclub_g"; 
    $contrasena = "v84NF-K_rE87"; 
    $base_datos = "gcasaclub_casa_club"; 
    $conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    } else {
        //echo "Conexión exitosa a la base de datos '$base_datos' en el servidor '$servidor'.";
    }
?>
