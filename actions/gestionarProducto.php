<?php
session_start(); 
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // 📌 CAPTURA DE DATOS
    // =========================
    $accion = $_POST['action'] ?? '';
    $codigo = $_POST['codeProd'];
    $nombre = $_POST['nameProd'];
    $categoria = $_POST['categProd'];
    $caracteristicas = $_POST['charProd'] ?? null;
    $tiendaVirtual = $_POST['disponible'];
    $imagenes = $_FILES['imagen'] ?? null;
    $directorio = '../products/';

    try {
        $conexion->autocommit(false);

        // =====================================================
        // 🟢 SECCIÓN: REGISTRAR PRODUCTO
        // =====================================================
        if ($accion === 'registrar') {

            $sql = "INSERT INTO lista_productos 
                    (codigo, nombre, categoria, caracteristicas, tienda_virtual) 
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssss', $codigo, $nombre, $categoria, $caracteristicas, $tiendaVirtual);
            $stmt->execute();
        }

        // =====================================================
        // 🔵 SECCIÓN: EDITAR PRODUCTO
        // =====================================================
        elseif ($accion === 'editar') {

            $oldCode = $_POST['idProd'];

            // 🔹 Si cambia el código
            if ($oldCode !== $codigo) {

                // 1. Actualizar código en tabla padre
                $sqlUpdateCode = "UPDATE lista_productos SET codigo = ? WHERE codigo = ?";
                $stmtCode = $conexion->prepare($sqlUpdateCode);
                $stmtCode->bind_param('ss', $codigo, $oldCode);
                $stmtCode->execute();

                // 2. Actualizar FK en tabla hija
                $sqlUpdateHija = "UPDATE detalle_producto SET codigo = ? WHERE codigo = ?";
                $stmtHija = $conexion->prepare($sqlUpdateHija);
                $stmtHija->bind_param('ss', $codigo, $oldCode);
                $stmtHija->execute();

                // 3. Reconstruir codigo_detalle
                $sqlUpdateDetalle = "
                    UPDATE detalle_producto 
                    SET codigo_detalle = CONCAT(?, '_', id_detalle)
                    WHERE codigo = ?
                ";
                $stmtDetalle = $conexion->prepare($sqlUpdateDetalle);
                $stmtDetalle->bind_param('ss', $codigo, $codigo);
                $stmtDetalle->execute();
            }

            // 🔹 Actualizar demás datos
            $sql = "UPDATE lista_productos 
                    SET nombre = ?, categoria = ?, caracteristicas = ?, tienda_virtual = ? 
                    WHERE codigo = ?";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssss', $nombre, $categoria, $caracteristicas, $tiendaVirtual, $codigo);
            $stmt->execute();
        }

        // =====================================================
        // 🔴 VALIDACIÓN DE ACCIÓN
        // =====================================================
        else {
            throw new Exception("Acción no válida.");
        }

        // =====================================================
        // 🖼️ SECCIÓN: SUBIDA DE IMÁGENES
        // =====================================================
        if ($imagenes && isset($imagenes['tmp_name'])) {

            $sqlImagen = "INSERT INTO imagenes (codigo, ruta_imagen) VALUES (?, ?)";
            $stmtImagen = $conexion->prepare($sqlImagen);

            foreach ($imagenes['tmp_name'] as $key => $tmpName) {

                if ($imagenes['error'][$key] === UPLOAD_ERR_OK) {

                    $nombreArchivo = uniqid() . '-' . basename($imagenes['name'][$key]);
                    $rutaArchivo = $directorio . $nombreArchivo;

                    if (move_uploaded_file($tmpName, $rutaArchivo)) {
                        $stmtImagen->bind_param('ss', $codigo, $rutaArchivo);
                        $stmtImagen->execute();
                    } else {
                        throw new Exception('Error al mover el archivo: ' . $imagenes['name'][$key]);
                    }
                }
            }
        }

        // =========================
        // ✅ CONFIRMAR TRANSACCIÓN
        // =========================
        $conexion->commit();

        $_SESSION['mensaje'] = ($accion === 'registrar') 
            ? "Producto registrado con éxito." 
            : "Producto editado con éxito.";

    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();

    } finally {
        $conexion->autocommit(true);
    }

} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
}

header("Location: ../b1t.php?p=art_productos.php");
exit;
?>