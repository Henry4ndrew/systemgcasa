<?php
session_start();
require '../includes/conexion.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['imagenCli']) && $_FILES['imagenCli']['error'] == 0) {
        $nombre_archivo = $_FILES['imagenCli']['name'];
        $ruta_temporal = $_FILES['imagenCli']['tmp_name'];
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $nombre_archivo_final = uniqid() . '.' . $extension;
        $directorio_destino = '../img/' . $nombre_archivo_final;
        if (move_uploaded_file($ruta_temporal, $directorio_destino)) {
            $url_img = 'img/' . $nombre_archivo_final;
            $sql = "INSERT INTO img_section_clientes (url_img) VALUES ('$url_img')";
            if ($conexion->query($sql) === TRUE) {
                $_SESSION['mensaje'] = 'Imagen cargada y registrada correctamente.';
            } else {
                $_SESSION['mensaje'] = 'Error al insertar la imagen en la base de datos: ' . $conexion->error;
            }
        } else {
            $_SESSION['mensaje'] = 'Error al mover la imagen al directorio.';
        }
    } else {
        $_SESSION['mensaje'] = 'No se ha seleccionado ninguna imagen o ha ocurrido un error en la carga.';
    }
    $conexion->close();
    header("Location: ../b1t.php?p=web_data.php");
    exit;
}
?>
