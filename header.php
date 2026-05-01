<?php
session_start();

if (isset($_SESSION['mensaje'])) {
    echo "<div class='notification b-gold'>{$_SESSION['mensaje']}</div>";
    unset($_SESSION['mensaje']);
}

// Tiempo máximo de inactividad (en segundos)
$tiempo_maximo = 1800; // 30 minutos

if (isset($_SESSION['ultimo_acceso']) && (time() - $_SESSION['ultimo_acceso']) > $tiempo_maximo) {
    // Destruir sesión
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
// Actualizar último acceso
$_SESSION['ultimo_acceso'] = time();
// Redirigir si no hay usuario
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}
// Datos del usuario
$datos_usuario = [
    'id_usuario'      => $_SESSION['id_usuario'],
    'nombre_usuario'  => $_SESSION['nombre_usuario'],
    'permiso'         => $_SESSION['permiso']
];
$nombre = $datos_usuario['nombre_usuario'];
$permiso = $datos_usuario['permiso'];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <!-- libreria para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Para texto -->
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/31.css">
    <link rel="stylesheet" href="css/46f.css">
     <link rel="stylesheet" href="css/sys11.css">
    <script>
        window.addEventListener("load", function () {
        document.body.style.visibility = "visible";
        });
    </script>
</head>




<header class="navbar b-azul">
    <div class="containerUser b-naranja">
        <i class="fa-solid fa-user-circle centrar b-azul"></i>
        <div class="datosUser">
            <p><?php echo htmlspecialchars($nombre); ?></p>
            <p><?php echo htmlspecialchars($permiso); ?></p>
        </div>
    </div><!--fin containerUser--->

    <img src="img/logoGcasaclub.avif" alt="logo" class="logo">

    <button class="btn-load orange" onclick="redireccionar('actions/logout.php')">
        <span><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</span>
    </button>
</header>


<script>
    function redireccionar(url) {
        window.location.href = url;
    }
    //muestra y oculta un id recibido
    function plop(idCampo) {
      const campo = document.getElementById(idCampo);
        if (!campo) return;
        if (campo.style.display === "none" || campo.style.display === "") {
            campo.style.display = "block";
        } else {
            campo.style.display = "none";
        }
    }
    //muestra/oculta y reseta un form
    function plop2(idForm) {
      document.getElementById(idForm).reset();
      plop(idForm);
    }
</script>

<style>


/* header */
.navbar {
  box-sizing:border-box;
  padding: 0.2rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  z-index:3;
}
.containerUser {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 50px;
    padding: 6px 15px 6px 6px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    max-width: 220px;
}

.containerUser i.fa-user-circle {
    font-size: 2rem;
    color: white;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    border: 2px solid rgba(255, 255, 255, 0.8);
    margin:-10px;
}
.datosUser {
    display: flex;
    flex-direction: column;
    line-height: 1.3;
    padding:0 7px;
}
.datosUser p {
    margin: 0;
}
.datosUser p:first-child {
    font-weight: 600;
    font-size: 0.9rem;
}
.datosUser p:last-child {
    font-weight: 500;
    font-size: 0.8rem;
    text-transform: capitalize;
    opacity: 0.9;
}

.logo{
    width:150px;
}
@media (max-width: 768px) {
    .logo{
        display:none
    }
}








/* notificaion general de las sesiones */
.notification {
  color: white;
  padding: 15px 20px;
  border-radius: var(--rad2);
  position: fixed;
  bottom: 80px;
  left: 50%;
  transform: translateX(-50%) translateY(50px);
  opacity: 0;
  z-index: 1000;
  animation: notificar 4s ease forwards;
  box-shadow: var(--s2);
  border: none;
  background: linear-gradient(135deg, #1e3a5f, #2c5282, #4a7bac, #2c5282, #1e3a5f);
  background-size: 400% 400%;
  font-weight: 500;
  min-width: 300px;
  text-align: center;
  backdrop-filter: blur(10px);
  text-shadow: var(--txt-sh);
  animation: notificar 4s ease forwards, metalShine 6s ease-in-out infinite;
}
.notification.b-gold {
  background: linear-gradient(135deg, #daa520, #ffd700, #b8860b, #ffdf00, #daa520);
  background-size: 400% 400%;
  animation: notificar 4s ease forwards, goldShine 5s ease-in-out infinite;
}
.notification i {
  font-size: 1.5rem;
  margin-right: 10px;
  text-shadow: var(--txt-sh);
}
/* Animación de notificación */
@keyframes notificar {
  0% {
    opacity: 0;
    transform: translateX(-50%) translateY(50px) scale(0.9);
  }
  15% {
    opacity: 1;
    transform: translateX(-50%) translateY(0) scale(1);
  }
  85% {
    opacity: 1;
    transform: translateX(-50%) translateY(0) scale(1);
  }
  100% {
    opacity: 0;
    transform: translateX(-50%) translateY(50px) scale(0.9);
  }
}
/* Efecto de metal brillante para azul */
@keyframes metalShine {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
/* Efecto de oro reluciente */
@keyframes goldShine {
  0% {
    background-position: 0% 50%;
    filter: brightness(1);
  }
  25% {
    filter: brightness(1.2);
  }
  50% {
    background-position: 100% 50%;
    filter: brightness(1.1);
  }
  75% {
    filter: brightness(1.3);
  }
  100% {
    background-position: 0% 50%;
    filter: brightness(1);
  }
}

</style>