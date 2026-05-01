<?php
session_start();
date_default_timezone_set('America/La_Paz');
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nameCliente']) ? trim($_POST['nameCliente']) : null;
    $nit = isset($_POST['nit']) ? trim($_POST['nit']) : null;
    $carnet_ci = isset($_POST['carnetCliente']) ? trim($_POST['carnetCliente']) : null;
    $departamento = isset($_POST['departamento']) ? trim($_POST['departamento']) : null;
    $celular = isset($_POST['celCliente']) ? trim($_POST['celCliente']) : null;
    $celularEmpresa = isset($_POST['celEmpresa']) ? trim($_POST['celEmpresa']) : null;
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : null;
    $empresa = isset($_POST['empresaCliente']) ? trim($_POST['empresaCliente']) : null;
    $nota = isset($_POST['note_client']) ? trim($_POST['note_client']) : null;
    $action = isset($_POST['action']) ? $_POST['action'] : null;

    if (empty($nombre)) {
        $_SESSION['mensaje'] = "El nombre es obligatorio.";
        header("Location: ../b1t.php?p=clientes.php");
        exit;
    }
    try {
        if ($action === 'registrar') {

            $fecha_registro = date('Y-m-d H:i:s');

            $sql = "INSERT INTO cartera_clientes (nombre, nit, carnet_ci, departamento, celular, cel_empresa, correo, empresa, nota, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssssssssss', $nombre, $nit, $carnet_ci, $departamento, $celular, $celularEmpresa, $correo, $empresa, $nota, $fecha_registro);
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Cliente registrado exitosamente.";
            } else {
                $_SESSION['mensaje'] = "Error al registrar el cliente: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($action === 'editar') {
            $id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : null;
            
            if (!$id_cliente) {
                $_SESSION['mensaje'] = "ID de cliente no proporcionado.";
                header("Location: ../b1t.php?p=clientes.php");
                exit;
            }
        
            $campos = [];
            $valores = [];
            $tipos = '';
            
            $datos = [
                "nombre" => $nombre,
                "nit" => $nit,
                "carnet_ci" => $carnet_ci,
                "departamento" => $departamento,
                "celular" => $celular,
                "cel_empresa" => $celularEmpresa,
                "correo" => $correo,
                "empresa" => $empresa,
                "nota" => $nota
            ];
            
            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = ?";
                $valores[] = $valor;
                $tipos .= 's';
            }
            
        
            if (count($campos) > 0) {
                $sql = "UPDATE cartera_clientes SET " . implode(', ', $campos) . " WHERE id_cliente = ?";
                $valores[] = $id_cliente;
                $tipos .= 'i';
        
                try {
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param($tipos, ...$valores);
        
                    if ($stmt->execute()) {
                        $_SESSION['mensaje'] = "Cliente editado exitosamente.";
                    } else {
                        $_SESSION['mensaje'] = "Error al editar el cliente: " . $stmt->error;
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
                }
            } else {
                $_SESSION['mensaje'] = "No se han realizado cambios en los datos del cliente.";
            }
        }
              
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error de base de datos: " . $e->getMessage();
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
}
header("Location: ../b1t.php?p=clientes.php");
exit;
$conexion->close();
?>
