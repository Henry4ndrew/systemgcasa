<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = isset($_POST['titlePdf']) ? trim($_POST['titlePdf']) : '';
    $descripcion = isset($_POST['descPdf']) ? trim($_POST['descPdf']) : '';

    if (empty($titulo) || !isset($_FILES['doc_pdf']) || $_FILES['doc_pdf']['error'] != 0) {
        $_SESSION['mensaje'] = "Todos los campos son obligatorios y debe adjuntar un PDF válido.";
        header("Location: ../b1t.php?p=web_pdf.php");
        exit;
    }

    $directorio = "../pdf/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = time() . "_" . basename($_FILES['doc_pdf']['name']);
    $rutaArchivo = $directorio . $nombreArchivo;

    if (move_uploaded_file($_FILES['doc_pdf']['tmp_name'], $rutaArchivo)) {
        $stmt = $conexion->prepare("INSERT INTO documentos_pdf (titulo, descripcion, archivo_pdf) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $titulo, $descripcion, $nombreArchivo);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "PDF guardado exitosamente.";
        } else {
            $_SESSION['mensaje'] = "Error al guardar el PDF.";
        }

        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "Error al subir el archivo.";
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no válido.";
}

$conexion->close();
header("Location: ../b1t.php?p=web_pdf.php");
exit;

?>
