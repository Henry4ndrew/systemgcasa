<?php
session_start();
require '../includes/conexion.php';
date_default_timezone_set('America/La_Paz');

$action = $_POST['action'] ?? '';
$idVendedor     = $_POST['idVendedor'];
$idVenta        = $_POST['id_venta'];
$tipoPago       = $_POST['tipoPago'];
$anticipo       = $_POST['anticipoVenta'];
$saldo          = $_POST['saldo-cobro'];
$fechaSigPago   = $_POST['fechaSigPago'];
    if ($action === "registrar") {

            $sql = "INSERT INTO pagos (id_venta, tipo_pago, anticipo, saldo, fecha_sig_pago, id_user, fecha_pago_actual)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("isddsi",
                $idVenta,
                $tipoPago,
                $anticipo,
                $saldo,
                $fechaSigPago,
                $idVendedor
            );
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Cobro registrado correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al registrar el cobro: " . $stmt->error;
            }
            $stmt->close();
    }
    if ($action === "editar") {
            $idPago = $_POST['id_pago'] ?? 0;
            $sql = "UPDATE pagos 
                    SET tipo_pago = ?, anticipo = ?, saldo = ?, fecha_sig_pago = ?, id_user = ?, fecha_pago_actual = NOW()
                    WHERE id_pago = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sddsis",
                $tipoPago,
                $anticipo,
                $saldo,
                $fechaSigPago,
                $idVendedor,
                $idPago
            );
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Cobro actualizado correctamente.";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el cobro: " . $stmt->error;
            }
            $stmt->close();
    }
$conexion->close();
header("Location: ../b1t.php?p=cobros.php");
exit;
?>
