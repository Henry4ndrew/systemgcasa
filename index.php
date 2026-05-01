<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Tangerine&family=Mulish&display=swap" rel="stylesheet">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>


    <form action="actions/login.php" method="post">

        <img src="img/logoGcasaclub.avif" alt="logo G casa club" class="logo-login">

        <?php
        if (isset($_SESSION['mensaje'])) {
            echo "<div class='notification'>{$_SESSION['mensaje']}</div>";
            unset($_SESSION['mensaje']);
        }
        ?>

        <div class="elem centrar">
            <i class="fas fa-user icon-lateral"></i>
            <input class="input" type="text" placeholder="Usuario" name="usuario" required>
        </div>

        <div class="elem centrar">
            <i class="fas fa-lock icon-lateral"></i>
            <input class="input" type="password" placeholder="Contraseña" name="contrasena" required>
        </div>

        <button class="btn-load orange" type="submit">
            <span>Iniciar sesión</span>
        </button>

    </form>

</body>
</html>


<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Mulish', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url('img/fondoInicioSesion.avif') no-repeat center center fixed;
    background-size: cover;
    padding: 20px;
    position: relative;
}

/* Capa de superposición para mejorar legibilidad */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

form {
    background: 
        /* Fondo base con gradiente */
        linear-gradient(135deg, #1e3a5f, #2c5282, #4a7bac, #2c5282, #1e3a5f),
        /* Capa brillante animada */
        linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.1) 25%, 
            rgba(255, 255, 255, 0.2) 50%, 
            rgba(255, 255, 255, 0.1) 75%, 
            transparent 100%);
    background-size: cover, 200% 100%;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 450px;
    position: relative;
    z-index: 2;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

/* Crear elemento pseudo para la animación de brillo */
form::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        rgba(255, 255, 255, 0.2),
        rgba(255, 255, 255, 0.1),
        transparent
    );
    animation: brilloMovimiento 3s infinite linear;
    z-index: 1;
}

@keyframes brilloMovimiento {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.logo-login {
    display: block;
    width: 280px;
    margin: 0 auto 30px;
    object-fit: cover;
    border: 2px solid white;
    background: rgb(0, 0, 0, 0.5);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    padding:0 10px;
    border-radius: 15px;
}

.notification {
    background: rgb(255,255,255, 0.3);
    color: #c62828;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    text-align: center;
    font-size: 0.9rem;
    border-left: 4px solid #c62828;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.elem {
    position: relative;
    margin-bottom: 25px;
}

.centrar {
    display: flex;
    align-items: center;
}

.icon-lateral {
    position: absolute;
    left: 15px;
    color: #666;
    font-size: 18px;
    z-index: 1;
}

.input {
    width: 100%;
    padding: 16px 20px 16px 50px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    font-family: 'Mulish', sans-serif;
    transition: all 0.3s ease;
    background: white;
}

.input:focus {
    outline: none;
    border: 1px solid rgba(176, 195, 1, 1); 
    box-shadow: inset 0 0 0 2px #eeff00ff,  
                0 0 0 3px rgba(255, 153, 0, 0.9); 
}

.input::placeholder {
    color: #999;
}

.btn-load {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #b35400, #e67c00, #ff9800, #e67c00, #b35400);
    background-size: 300% 300%;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Mulish', sans-serif;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.5px;
    animation: gradientAnimation 8s ease infinite;
}

.btn-load:hover {
    background: linear-gradient(135deg, #daa520, #ffd700, #b8860b, #ffdf00, #daa520);
    background-size: 300% 300%;
    box-shadow: 0 7px 20px rgba(255, 152, 0, 0.3);
    animation: gradientAnimation 2s ease infinite;
}

@keyframes gradientAnimation {
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

.btn-load:active {
    transform: translateY(0);
}

.btn-load span {
    position: relative;
    z-index: 1;
}

/* Efecto de onda al hacer hover */
.btn-load::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-load:hover::after {
    width: 300px;
    height: 300px;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    form {
        padding: 30px 25px;
        max-width: 400px;
    }
    
    .logo-login {
        width: 100px;
        height: 100px;
        margin-bottom: 25px;
    }
    
    .input {
        padding: 14px 18px 14px 45px;
        font-size: 15px;
    }
    
    .btn-load {
        padding: 16px;
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 15px;
    }
    
    form {
        padding: 25px 20px;
        border-radius: 15px;
    }
    
    .logo-login {
        width: 90px;
        height: 90px;
        margin-bottom: 20px;
    }
    
    .notification {
        font-size: 13px;
        padding: 10px 15px;
        margin-bottom: 20px;
    }
    
    .elem {
        margin-bottom: 20px;
    }
    
    .input {
        padding: 12px 15px 12px 40px;
        font-size: 14px;
    }
    
    .icon-lateral {
        font-size: 16px;
        left: 12px;
    }
    
    .btn-load {
        padding: 14px;
        font-size: 15px;
    }
}

/* Para pantallas muy altas */
@media (min-height: 800px) {
    form {
        margin: 40px 0;
    }
}

</style>

