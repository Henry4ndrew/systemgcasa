<?php
session_start();
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
    header("Location: ../b1t.php?p=config_data_cotiz.php");
    exit;
}

$id = $_POST['idPiePag'] ?? null;
$direccion = $_POST['direccionPiePag'] ?? null;
$celular_contacto = $_POST['celContactPiePag'] ?? null;
$celular_fabrica = $_POST['celFabPiePag'] ?? null;
$correo = $_POST['correoPiePag'] ?? null;
$direction_tienda = $_POST['direcTienda'] ?? null;
$nombreFirma = $_POST['nombreFirma'] ?? null;
$cargoFirma = $_POST['cargoFirma'] ?? null;
$directorio_destino = "../img/";
$url_firma = null;
$url_logo = null;

if (!empty($_FILES['firmaDigital']['name'])) {
    $archivo_tmp = $_FILES['firmaDigital']['tmp_name'];
    $extension = pathinfo($_FILES['firmaDigital']['name'], PATHINFO_EXTENSION);
    $timestamp = time();
    $nombre_archivo = "firmaDigital_{$id}_{$timestamp}";
    $ruta_final = $directorio_destino . $nombre_archivo . "." . $extension;
    $existing_files = glob($directorio_destino . "firmaDigital_{$id}_*.*");
    if (!empty($existing_files)) {
        foreach ($existing_files as $archivo_existente) {
            unlink($archivo_existente);
        }
    }
    if (move_uploaded_file($archivo_tmp, $ruta_final)) {
        $url_firma = $ruta_final;
        $_SESSION['mensaje'] = "Firma digital actualizada correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al subir la firma digital.";
    }
}


if (!empty($_FILES['logo']['name'])) {
    $archivo_tmp = $_FILES['logo']['tmp_name'];
    $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $timestamp = time();
    $nombre_archivo = "logo_{$id}_{$timestamp}";
    $ruta_final = $directorio_destino . $nombre_archivo . "." . $extension;
    $existing_files = glob($directorio_destino . "logo_{$id}_*.*");
    if (!empty($existing_files)) {
        foreach ($existing_files as $archivo_existente) {
            unlink($archivo_existente);
        }
    }
    if (move_uploaded_file($archivo_tmp, $ruta_final)) {
        $url_logo = $ruta_final;
        $_SESSION['mensaje'] = "Logo actualizado correctamente.";
    } else {
        $_SESSION['mensaje'] = "Error al subir el logo.";
    }
}

$sql_verificar = "SELECT COUNT(*) AS total FROM pie_pagina_cotizacion WHERE id = ?";
$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->bind_param("i", $id);
$stmt_verificar->execute();
$resultado = $stmt_verificar->get_result();
$fila = $resultado->fetch_assoc();
$stmt_verificar->close();



if ($fila['total'] > 0) {
    if ($url_firma && $url_logo) {
        $sql = "UPDATE pie_pagina_cotizacion SET direccion=?, celular_contacto=?, celular_fabrica=?, correo=?, direction_tienda=?, url_firma=?, url_logo=?, nombre_firma=?, cargo_firma=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'ssssssssi' a 'sssssssssi' (10 's' para 9 strings + 1 'i' para el id)
        $stmt->bind_param("sssssssssi", $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_firma, $url_logo, $nombreFirma, $cargoFirma, $id);
    } elseif ($url_firma) {
        $sql = "UPDATE pie_pagina_cotizacion SET direccion=?, celular_contacto=?, celular_fabrica=?, correo=?, direction_tienda=?, url_firma=?, nombre_firma=?, cargo_firma=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'ssssssssi' a 'ssssssssi' (8 's' para 8 strings + 1 'i' para el id)
        $stmt->bind_param("ssssssssi", $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_firma, $nombreFirma, $cargoFirma, $id);
    } elseif ($url_logo) {
        $sql = "UPDATE pie_pagina_cotizacion SET direccion=?, celular_contacto=?, celular_fabrica=?, correo=?, direction_tienda=?, url_logo=?, nombre_firma=?, cargo_firma=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'ssssssssi' a 'ssssssssi' (8 's' para 8 strings + 1 'i' para el id)
        $stmt->bind_param("ssssssssi", $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_logo, $nombreFirma, $cargoFirma, $id);
    } else {
        $sql = "UPDATE pie_pagina_cotizacion SET direccion=?, celular_contacto=?, celular_fabrica=?, correo=?, direction_tienda=?, nombre_firma=?, cargo_firma=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'sssssssi' a 'sssssssi' (7 's' para 7 strings + 1 'i' para el id)
        $stmt->bind_param("sssssssi", $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $nombreFirma, $cargoFirma, $id);
    }
} else {
    if ($url_firma && $url_logo) {
        $sql = "INSERT INTO pie_pagina_cotizacion (id, direccion, celular_contacto, celular_fabrica, correo, direction_tienda, url_firma, url_logo, nombre_firma, cargo_firma) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'isssssssss' a 'isssssssss' (1 'i' + 9 's' para 10 parámetros)
        $stmt->bind_param("isssssssss", $id, $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_firma, $url_logo, $nombreFirma, $cargoFirma);
    } elseif ($url_firma) {
        $sql = "INSERT INTO pie_pagina_cotizacion (id, direccion, celular_contacto, celular_fabrica, correo, direction_tienda, url_firma, nombre_firma, cargo_firma) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'issssssss' a 'issssssss' (1 'i' + 8 's' para 9 parámetros)
        $stmt->bind_param("issssssss", $id, $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_firma, $nombreFirma, $cargoFirma);
    } elseif ($url_logo) {
        $sql = "INSERT INTO pie_pagina_cotizacion (id, direccion, celular_contacto, celular_fabrica, correo, direction_tienda, url_logo, nombre_firma, cargo_firma) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'issssssss' a 'issssssss' (1 'i' + 8 's' para 9 parámetros)
        $stmt->bind_param("issssssss", $id, $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $url_logo, $nombreFirma, $cargoFirma);
    } else {
        $sql = "INSERT INTO pie_pagina_cotizacion (id, direccion, celular_contacto, celular_fabrica, correo, direction_tienda, nombre_firma, cargo_firma) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        // MODIFICADO: Cambiado de 'isssssss' a 'isssssss' (1 'i' + 7 's' para 8 parámetros)
        $stmt->bind_param("isssssss", $id, $direccion, $celular_contacto, $celular_fabrica, $correo, $direction_tienda, $nombreFirma, $cargoFirma);
    }
}

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "Datos actualizados correctamente.";
} else {
    $_SESSION['mensaje'] = "Error al actualizar datos: " . $stmt->error;
}

$stmt->close();
$conexion->close();
header("Location: ../b1t.php?p=config_data_cotiz.php");
exit;
?>