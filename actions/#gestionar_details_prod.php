<?php
session_start(); 
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'registrar') {
        $id_prod = $_POST['id_prod'];
        $medida_disp = $_POST['medida_disp'];
        $detail_prod = $_POST['detail_prod'];
        $price_prod = $_POST['price_prod'];
        
        // Verificar que los campos obligatorios no estén vacíos
        if (!empty($id_prod) && !empty($medida_disp) && !empty($price_prod)) {
            $sql = "INSERT INTO detalle_producto (codigo, medida, detalle, precio_unitario) 
                    VALUES (?, ?, ?, ?)";
            if ($stmt = $conexion->prepare($sql)) {
                $stmt->bind_param("sssd", $id_prod, $medida_disp, $detail_prod, $price_prod);
                if ($stmt->execute()) {
                    
                    $update = $conexion->prepare("UPDATE lista_productos SET ultima_actualizacion = NOW() WHERE codigo = ?");
                    $update->bind_param("s", $id_prod);
                    $update->execute();
                    $update->close();

                    $_SESSION['mensaje'] = "Detalle de producto agregado exitosamente.";
                } else {
                    $_SESSION['mensaje'] = "Error al agregar detalle de producto.";
                }
                $stmt->close();
            } else {
                $_SESSION['mensaje'] = "Error al preparar la consulta.";
            }
        } else {
            $_SESSION['mensaje'] = "Por favor, complete todos los campos obligatorios.";
        }
    }
     elseif (isset($_POST['action']) && $_POST['action'] == 'editar') {
        $id_detalle = $_POST['id_prod']; 
        $medida_disp = $_POST['medida_disp'];
        $detail_prod = $_POST['detail_prod'];
        $price_prod = $_POST['price_prod'];
        
        if (!empty($id_detalle) && !empty($medida_disp) && !empty($price_prod)) {
            $sql = "UPDATE detalle_producto SET medida = ?, detalle = ?, precio_unitario = ? WHERE id_detalle = ?";
            if ($stmt = $conexion->prepare($sql)) {
                $stmt->bind_param("ssdi", $medida_disp, $detail_prod, $price_prod, $id_detalle);
                if ($stmt->execute()) {

                    $codigoQuery = $conexion->prepare("SELECT codigo FROM detalle_producto WHERE id_detalle = ?");
                    $codigoQuery->bind_param("i", $id_detalle);
                    $codigoQuery->execute();
                    $codigoQuery->store_result();
                    if ($codigoQuery->num_rows > 0) {
                        $codigoQuery->bind_result($codigoProd);
                        $codigoQuery->fetch();

                        $update = $conexion->prepare("UPDATE lista_productos SET ultima_actualizacion = NOW() WHERE codigo = ?");
                        $update->bind_param("s", $codigoProd);
                        $update->execute();
                        $update->close();
                    }
                    $codigoQuery->close();

                    $_SESSION['mensaje'] = "Detalle de producto actualizado exitosamente.";
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar detalle de producto.";
                }
                $stmt->close();
            } else {
                $_SESSION['mensaje'] = "Error al preparar la consulta.";
            }
        } else {
            $_SESSION['mensaje'] = "Por favor, complete todos los campos obligatorios.";
        }
    }
    header("Location: ../b1t.php?p=art_productos.php");
    exit;
}
$conexion->close();
?>

