<?php
session_start(); 
require '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {





    if (isset($_POST['action']) && $_POST['action'] == 'registrar') {
    $id_prod = $_POST['id_prod'];
    $medida_disp = $_POST['medida_disp'];
    $detail_prod = $_POST['detail_prod'];
    $price_prod = $_POST['price_prod'];
    
    if (!empty($id_prod) && !empty($medida_disp) && !empty($price_prod)) {

        $conexion->autocommit(false);

        try {
            // 1. Insertar detalle
            $sql = "INSERT INTO detalle_producto (codigo, medida, detalle, precio_unitario) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssd", $id_prod, $medida_disp, $detail_prod, $price_prod);
            $stmt->execute();

            // 2. Obtener el ID generado
            $id_detalle = $conexion->insert_id;

            // 3. Generar codigo_detalle
            $codigo_detalle = $id_prod . '_' . $id_detalle;

            // 4. Actualizar el registro recién creado
            $updateDetalle = $conexion->prepare("
                UPDATE detalle_producto 
                SET codigo_detalle = ? 
                WHERE id_detalle = ?
            ");
            $updateDetalle->bind_param("si", $codigo_detalle, $id_detalle);
            $updateDetalle->execute();

            // 5. Actualizar fecha en tabla padre
            $update = $conexion->prepare("
                UPDATE lista_productos 
                SET ultima_actualizacion = NOW() 
                WHERE codigo = ?
            ");
            $update->bind_param("s", $id_prod);
            $update->execute();

            $conexion->commit();

            $_SESSION['mensaje'] = "Detalle de producto agregado exitosamente.";

        } catch (Exception $e) {
            $conexion->rollback();
            $_SESSION['mensaje'] = "Error: " . $e->getMessage();
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

