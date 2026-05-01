<?php
session_start(); 
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['action']) ? $_POST['action'] : '';
    $codigo = $_POST['codeProd'];
    $nombre = $_POST['nameProd'];
    $categoria = $_POST['categProd'];
    $caracteristicas = isset($_POST['charProd']) ? $_POST['charProd'] : null;
    $tiendaVirtual = $_POST['disponible'];
    $imagenes = isset($_FILES['imagen']) ? $_FILES['imagen'] : null;
    $directorio = '../products/';
    try {
        $conexion->autocommit(false);
        if ($accion === 'registrar') {
            $sql = "INSERT INTO lista_productos (codigo, nombre, categoria, caracteristicas, tienda_virtual) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssss', $codigo, $nombre, $categoria, $caracteristicas, $tiendaVirtual);
            $stmt->execute();
        } elseif ($accion === 'editar') {
            
            
            $oldCode = $_POST['idProd'];
            if ($oldCode !== $codigo) {
                $sqlUpdateCode = "UPDATE lista_productos SET codigo = ? WHERE codigo = ?";
                $stmtCode = $conexion->prepare($sqlUpdateCode);
                $stmtCode->bind_param('ss', $codigo, $oldCode);
                $stmtCode->execute();
            }

                
                
            
            $sql = "UPDATE lista_productos SET nombre = ?, categoria = ?, caracteristicas = ?, tienda_virtual = ? WHERE codigo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('sssss', $nombre, $categoria, $caracteristicas, $tiendaVirtual, $codigo);
            $stmt->execute();
        } else {
            throw new Exception("Acción no válida.");
        }
        if ($imagenes) {
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
        $conexion->commit();
        $_SESSION['mensaje'] = ($accion === 'registrar') ? "Producto registrado con éxito." : "Producto editado con éxito.";
        
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
