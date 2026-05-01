<?php
session_start();
require '../includes/conexion.php'; 
date_default_timezone_set('America/La_Paz');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Iniciar transacción
    $conexion->begin_transaction();

    try {

        // Recibir los arrays del formulario
        $codigos = $_POST['codigo'] ?? [];
        $idsDetalle = $_POST['id_detalle_producto'] ?? [];
        $cantidades = $_POST['cantidad_producto'] ?? [];

        // Validación básica
        if (count($codigos) !== count($idsDetalle) || count($codigos) !== count($cantidades)) {
            throw new Exception("Datos inválidos recibidos.");
        }

        // Recorrer los productos enviados
        for ($i = 0; $i < count($codigos); $i++) {

            $codigo = $conexion->real_escape_string($codigos[$i]);
            $id_detalle = (int)$idsDetalle[$i];
            $cantidad = (int)$cantidades[$i];

            if ($cantidad <= 0) continue;

            // Verificar si existe
            $sqlCheck = "SELECT id_almacen, cantidad 
                         FROM almacen 
                         WHERE codigo = '$codigo' AND id_detalle = $id_detalle
                         LIMIT 1";

            $resCheck = $conexion->query($sqlCheck);
            if (!$resCheck) {
                throw new Exception("Error en SELECT: " . $conexion->error);
            }

            if ($resCheck->num_rows > 0) {
                // Actualizar
                $row = $resCheck->fetch_assoc();
                $nuevaCantidad = $row['cantidad'] + $cantidad;

                $sqlUpdate = "UPDATE almacen 
                              SET cantidad = $nuevaCantidad, 
                                  fecha_modificacion = NOW()
                              WHERE id_almacen = {$row['id_almacen']}";

                if (!$conexion->query($sqlUpdate)) {
                    throw new Exception("Error al actualizar: " . $conexion->error);
                }

            } else {
                // Insertar nuevo
                $sqlInsert = "INSERT INTO almacen (codigo, id_detalle, cantidad, fecha_modificacion)
                              VALUES ('$codigo', $id_detalle, $cantidad, NOW())";

                if (!$conexion->query($sqlInsert)) {
                    throw new Exception("Error al insertar: " . $conexion->error);
                }
            }
        }

        // Si todo estuvo OK → confirmar
        $conexion->commit();
        $_SESSION['mensaje'] = "Productos registrados correctamente.";
        $_SESSION['mensaje_tipo'] = "exito";

    } catch (Exception $e) {

        // Algo falló → revertir todo
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error al registrar los productos: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "error";
    }

    // Cerrar conexión
    $conexion->close();

    // Redirigir
    header("Location: ../b1t.php?p=stock_productos.php");
    exit;
}
?>
