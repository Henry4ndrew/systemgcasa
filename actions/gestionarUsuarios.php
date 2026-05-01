<?php
session_start(); 
require '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; 
    $idUser = $_POST['idUser'] ?? null; 
    $nombreUsuario = $_POST['nameUser'] ?? '';
    $contrasena = $_POST['passwordUser'] ?? '';
    $celular = $_POST['celUser'] ?? '';
    $permiso = $_POST['permiso'] ?? '';

    if (empty($permiso)) {
        $_SESSION['mensaje'] = "El permiso es obligatorio.";
        header("Location: ../b1t.php?p=usuarios.php");
        exit;
    }

    if ($action === 'registrar') {
        if (empty($nombreUsuario) || empty($contrasena)) {
            $_SESSION['mensaje'] = "El nombre de usuario y la contraseña son obligatorios.";
            header("Location: ../b1t.php?p=usuarios.php");
            exit;
        }

        // Verificar si el nombre de usuario ya existe
        try {
            $sqlCheck = "SELECT id_user FROM usuarios WHERE nombre_usuario = ?";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->bind_param('s', $nombreUsuario);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            
            if ($stmtCheck->num_rows > 0) {
                $_SESSION['mensaje'] = "Error: El nombre de usuario ya existe. Por favor elija otro.";
                $stmtCheck->close();
                header("Location: ../b1t.php?p=usuarios.php");
                exit;
            }
            $stmtCheck->close();
            
        } catch (mysqli_sql_exception $e) {
            $_SESSION['mensaje'] = "Error al verificar el usuario: " . $e->getMessage();
            header("Location: ../b1t.php?p=usuarios.php");
            exit;
        }

        // Registrar el nuevo usuario
        $contrasenaCifrada = password_hash($contrasena, PASSWORD_BCRYPT);
        try {
            $sql = "INSERT INTO usuarios (nombre_usuario, contrasena, celular, permiso)
                    VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('ssss', $nombreUsuario, $contrasenaCifrada, $celular, $permiso);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Usuario registrado con éxito.";
            } else {
                $_SESSION['mensaje'] = "Error al registrar el usuario: " . $stmt->error;
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            $_SESSION['mensaje'] = "Error al registrar el usuario: " . $e->getMessage();
        }

    } elseif ($action === 'editar') {
        $idUser = $_POST['idUser']; 
        
        // Verificar si el nombre de usuario ya existe (excluyendo el usuario actual)
        if (!empty($nombreUsuario)) {
            try {
                $sqlCheck = "SELECT id_user FROM usuarios WHERE nombre_usuario = ? AND id_user != ?";
                $stmtCheck = $conexion->prepare($sqlCheck);
                $stmtCheck->bind_param('si', $nombreUsuario, $idUser);
                $stmtCheck->execute();
                $stmtCheck->store_result();
                
                if ($stmtCheck->num_rows > 0) {
                    $_SESSION['mensaje'] = "Error: El nombre de usuario ya existe. Por favor elija otro.";
                    $stmtCheck->close();
                    header("Location: ../b1t.php?p=usuarios.php");
                    exit;
                }
                $stmtCheck->close();
                
            } catch (mysqli_sql_exception $e) {
                $_SESSION['mensaje'] = "Error al verificar el usuario: " . $e->getMessage();
                header("Location: ../b1t.php?p=usuarios.php");
                exit;
            }
        }

        // Actualizar otros datos si se modificaron
        $nombreUsuario = !empty($nombreUsuario) ? $nombreUsuario : null;
        $contrasena = !empty($contrasena) ? password_hash($contrasena, PASSWORD_BCRYPT) : null;
        $celular = !empty($celular) ? $celular : null;
        $permiso = !empty($permiso) ? $permiso : null;

        // Preparar la actualización
        $sqlUpdate = "UPDATE usuarios SET 
                      nombre_usuario = COALESCE(?, nombre_usuario),
                      contrasena = COALESCE(?, contrasena),
                      celular = COALESCE(?, celular),
                      permiso = COALESCE(?, permiso)
                      WHERE id_user = ?";

        $stmt = $conexion->prepare($sqlUpdate);
        $stmt->bind_param('ssssi', $nombreUsuario, $contrasena, $celular, $permiso, $idUser);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Usuario actualizado con éxito.";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar el usuario: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $_SESSION['mensaje'] = "Método de solicitud no permitido.";
}
header("Location: ../b1t.php?p=usuarios.php");
exit;
$conexion->close(); 
?>