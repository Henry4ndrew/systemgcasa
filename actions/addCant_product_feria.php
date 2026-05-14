<?php
session_start();
require '../includes/conexion.php'; 
date_default_timezone_set('America/La_Paz');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Iniciar transacción
    $conexion->begin_transaction();

    try {

        // Recibir el id_feria del formulario
        $id_feria = isset($_POST['feria_id']) ? (int)$_POST['feria_id'] : 0;

        // Validar que se haya seleccionado una feria
        if ($id_feria <= 0) {
            throw new Exception("Debe seleccionar una feria válida.");
        }

        // Recibir los arrays del formulario
        $codigos = $_POST['codigo'] ?? [];
        $idsDetalle = $_POST['id_detalle_producto'] ?? [];
        $cantidades = $_POST['cantidad_producto'] ?? [];

        // Validación básica
        if (count($codigos) !== count($idsDetalle) || count($codigos) !== count($cantidades)) {
            throw new Exception("Datos inválidos recibidos.");
        }

        // Validar que no esté vacío
        if (empty($codigos)) {
            throw new Exception("No se recibieron productos para registrar.");
        }

        // Recorrer los productos enviados
        for ($i = 0; $i < count($codigos); $i++) {

            $codigo = $conexion->real_escape_string($codigos[$i]);
            $id_detalle = (int)$idsDetalle[$i];
            $cantidad = (int)$cantidades[$i];

            if ($cantidad <= 0) continue;

            // Verificar si existe el producto en almacen_ferias con el mismo código, id_detalle e id_feria
            $sqlCheck = "SELECT id_almacen, cantidad 
                         FROM almacen_ferias 
                         WHERE codigo = '$codigo' 
                         AND id_detalle = $id_detalle 
                         AND id_feria = $id_feria
                         LIMIT 1";

            $resCheck = $conexion->query($sqlCheck);
            if (!$resCheck) {
                throw new Exception("Error en SELECT: " . $conexion->error);
            }

            if ($resCheck->num_rows > 0) {
                // Actualizar cantidad existente
                $row = $resCheck->fetch_assoc();
                $nuevaCantidad = $row['cantidad'] + $cantidad;

                $sqlUpdate = "UPDATE almacen_ferias 
                              SET cantidad = $nuevaCantidad, 
                                  fecha_modificacion = NOW()
                              WHERE id_almacen = {$row['id_almacen']}";

                if (!$conexion->query($sqlUpdate)) {
                    throw new Exception("Error al actualizar: " . $conexion->error);
                }

            } else {
                // Insertar nuevo registro con id_feria
                $sqlInsert = "INSERT INTO almacen_ferias (codigo, id_detalle, id_feria, cantidad, fecha_modificacion)
                              VALUES ('$codigo', $id_detalle, $id_feria, $cantidad, NOW())";

                if (!$conexion->query($sqlInsert)) {
                    throw new Exception("Error al insertar: " . $conexion->error);
                }
            }
        }

        // Si todo estuvo OK → confirmar
        $conexion->commit();
        $_SESSION['mensaje'] = "Productos registrados correctamente en la feria.";
        $_SESSION['mensaje_tipo'] = "exito";

    } catch (Exception $e) {

        // Algo falló → revertir todo
        $conexion->rollback();
        $_SESSION['mensaje'] = "Error al registrar los productos: " . $e->getMessage();
        $_SESSION['mensaje_tipo'] = "error";
    }

    $conexion->close();
    header("Location: ../b1t.php?p=stock_ferias.php");
    exit;
}
?>