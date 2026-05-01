<?php
session_start();
require '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['seleccionarImagen'])) {
    $idsImagenes = $_POST['seleccionarImagen'];
    $idsImagenesStr = implode(",", array_map('intval', $idsImagenes));
    $sqlSelect = "SELECT ruta_imagen, codigo FROM imagenes WHERE id_imagen IN ($idsImagenesStr)";
    $resultadoSelect = $conexion->query($sqlSelect);
    if ($resultadoSelect->num_rows > 0) {
        $codigosAActualizar = [];
        while ($row = $resultadoSelect->fetch_assoc()) {
            $rutaImagen = $row['ruta_imagen'];
            $codigo = $row['codigo'];
            if (!in_array($codigo, $codigosAActualizar)) {
                $codigosAActualizar[] = $codigo;
            }
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        // Eliminar las imágenes de la base de datos
        $sqlDelete = "DELETE FROM imagenes WHERE id_imagen IN ($idsImagenesStr)";
        if ($conexion->query($sqlDelete)) {

            $stmtUpdate = $conexion->prepare("UPDATE lista_productos SET ultima_actualizacion = NOW() WHERE codigo = ?");
            foreach ($codigosAActualizar as $codigoActualizar) {
                $stmtUpdate->bind_param("s", $codigoActualizar);
                $stmtUpdate->execute();
            }
            $stmtUpdate->close();

            $_SESSION['mensaje'] = "Imágenes eliminadas y fecha de actualización actualizada correctamente.";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar imágenes de la base de datos.";
        }
    } else {
        $_SESSION['mensaje'] = "No se encontraron imágenes para eliminar.";
    }
} else {
    $_SESSION['mensaje'] = "No se seleccionaron imágenes.";
}

header("Location: ../b1t.php?p=art_productos.php");
exit;
?>

