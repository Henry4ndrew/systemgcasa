<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>Iniciar sesión | G Casa Club</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Tangerine&family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Iconos Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'mulish': ['Mulish', 'sans-serif'],
                        'tangerine': ['Tangerine', 'cursive'],
                    },
                    animation: {
                        'shine': 'shine 3s infinite',
                        'gradient': 'gradient 8s ease infinite',
                        'slideIn': 'slideIn 0.3s ease',
                        'ripple': 'ripple 0.6s ease-out',
                    },
                    keyframes: {
                        shine: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(100%)' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                        slideIn: {
                            'from': { opacity: '0', transform: 'translateY(-10px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' },
                        },
                        ripple: {
                            '0%': { transform: 'scale(0)', opacity: '0.5' },
                            '100%': { transform: 'scale(4)', opacity: '0' },
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
        /* Estilos personalizados para efectos especiales */
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .animate-gradient-shift {
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
        }
        
        .hover-ripple {
            position: relative;
            overflow: hidden;
        }
        
        .hover-ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .hover-ripple:hover::after {
            width: 300px;
            height: 300px;
        }
        
        .input-focus-glow:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2), inset 0 0 0 2px #fef3c7;
        }
        
        .backdrop-blur-custom {
            backdrop-filter: blur(10px);
        }

        /* Animación para el botón de atrás */
        .btn-back {
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            transform: translateX(-5px);
            background: linear-gradient(135deg, #ef4444, #dc2626, #b91c1c);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4);
        }
        
        /* Efecto de brillo para el botón de atrás */
        .btn-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-back:hover::before {
            left: 100%;
        }
        
        /* Eliminar scroll horizontal y asegurar que el fondo cubra todo */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden !important;
            position: relative;
        }
        
        body {
            font-family: 'Mulish', sans-serif;
            min-height: 100vh;
            background: url('img/fondoInicioSesion.avif') no-repeat center center;
            background-size: cover !important;
            background-attachment: fixed;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            overflow-x: hidden !important;
            position: relative;
        }
        
        /* Forzar que la imagen cubra toda la pantalla sin recortes blancos */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
            pointer-events: none;
        }
        
        /* Prevenir cualquier desbordamiento horizontal */
        .overflow-prevent {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }
        
        /* Ajustes para móviles */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll !important;
                background-size: cover !important;
            }
            
            body::before {
                position: absolute;
            }
        }
        
        /* Evitar scroll horizontal en todos los elementos */
        .no-horizontal-scroll {
            overflow-x: hidden;
            max-width: 100%;
        }
        
        /* Asegurar que el formulario no cause desbordamiento */
        form {
            max-width: 100%;
            overflow-x: hidden;
        }
    </style>
</head>
<body class="overflow-prevent no-horizontal-scroll" style="margin: 0; padding: 0; width: 100%; max-width: 100%; overflow-x: hidden;">
    
    <!-- Contenedor principal con prevención de desbordamiento -->
    <div class="relative z-10 w-full max-w-full overflow-x-hidden min-h-screen flex items-center justify-center p-4 md:p-6">
        
        <!-- Contenedor del formulario -->
        <div class="w-full max-w-md lg:max-w-lg mx-auto my-auto">
            
            <!-- Formulario con diseño moderno -->
            <form action="actions/login.php" method="post" 
                  class="relative bg-gradient-to-br from-slate-900/90 via-blue-900/90 to-slate-900/90 
                         backdrop-blur-custom rounded-2xl lg:rounded-3xl p-6 md:p-8 lg:p-10 
                         border border-white/20 shadow-2xl overflow-hidden w-full">
                
                <!-- Efecto de brillo deslizante -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-0 -inset-full h-full w-1/2 z-10 block transform -skew-x-12 bg-gradient-to-r from-transparent via-white/10 to-transparent animate-shine"></div>
                </div>
                
                <!-- Logo -->
                <div class="flex justify-center mb-6 md:mb-8">
                    <div class="bg-black/40 p-2 md:p-3 rounded-xl border-2 border-white/30 shadow-xl transition-transform hover:scale-105 duration-300">
                        <img src="img/logoGcasaclub.avif" alt="Logo G Casa Club" 
                             class="w-40 sm:w-48 md:w-60 lg:w-64 h-auto object-contain rounded-lg"
                             style="max-width: 100%;">
                    </div>
                </div>
                
                <!-- Mensajes de notificación -->
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="mb-6 animate-slideIn">
                        <div class="bg-red-500/20 backdrop-blur-sm border-l-4 border-red-500 text-red-100 p-3 md:p-4 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                                <span class="text-sm md:text-base font-medium break-words"><?php echo htmlspecialchars($_SESSION['mensaje']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>
                
                <!-- Campo de usuario -->
                <div class="relative mb-5 md:mb-6 group">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-400 transition-colors z-10 text-base md:text-lg"></i>
                    <input type="text" 
                           name="usuario" 
                           required
                           class="w-full py-3 md:py-4 pl-11 md:pl-12 pr-4 rounded-xl 
                                  bg-white/10 backdrop-blur-sm border-2 border-white/20 
                                  text-white placeholder-gray-400 
                                  focus:border-amber-500 input-focus-glow
                                  transition-all duration-300 text-sm md:text-base
                                  hover:bg-white/20"
                           placeholder="Usuario">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-amber-500 to-orange-500 transition-all duration-300 group-focus-within:w-full"></div>
                </div>
                
                <!-- Campo de contraseña -->
                <div class="relative mb-6 md:mb-8 group">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-amber-400 transition-colors z-10 text-base md:text-lg"></i>
                    <input type="password" 
                           name="contrasena" 
                           required
                           class="w-full py-3 md:py-4 pl-11 md:pl-12 pr-4 rounded-xl 
                                  bg-white/10 backdrop-blur-sm border-2 border-white/20 
                                  text-white placeholder-gray-400 
                                  focus:border-amber-500 input-focus-glow
                                  transition-all duration-300 text-sm md:text-base
                                  hover:bg-white/20"
                           placeholder="Contraseña">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-amber-500 to-orange-500 transition-all duration-300 group-focus-within:w-full"></div>
                </div>
                
                <!-- Botón de inicio de sesión -->
                <button type="submit" 
                        class="hover-ripple relative w-full py-3.5 md:py-4 rounded-xl 
                               bg-gradient-to-r from-amber-600 via-orange-500 to-amber-600 
                               animate-gradient-shift text-white font-bold text-base md:text-lg
                               shadow-lg hover:shadow-amber-500/30 transform hover:scale-[1.02]
                               transition-all duration-300 flex items-center justify-center gap-2
                               group overflow-hidden">
                    <span class="relative z-10 flex items-center gap-2">
                        <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform"></i>
                        <span>Iniciar sesión</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform opacity-0 group-hover:opacity-100"></i>
                    </span>
                </button>
                
                <!-- Línea decorativa -->
                <div class="mt-6 pt-4 text-center">
                    <div class="flex items-center justify-center gap-2 text-white/40 text-xs">
                        <div class="h-px w-8 bg-white/20"></div>
                        <span>G Casa Club</span>
                        <div class="h-px w-8 bg-white/20"></div>
                    </div>
                </div>
                
            </form>
            
            <!-- Botón flotante en esquina inferior derecha para volver atrás -->
            <a href="index.php" 
               class="btn-back fixed bottom-6 right-6 md:bottom-8 md:right-8 z-20
                      bg-gradient-to-r from-gray-700 to-gray-800 
                      hover:from-red-600 hover:to-red-700
                      text-white rounded-full px-5 py-3 md:px-6 md:py-3.5
                      flex items-center gap-2 shadow-xl
                      transition-all duration-300 group
                      border border-white/20 backdrop-blur-sm"
               style="text-decoration: none;">
                <i class="fas fa-arrow-left text-base md:text-lg group-hover:-translate-x-1 transition-transform"></i>
                <span class="text-sm md:text-base font-semibold">Atrás</span>
                <i class="fas fa-home text-sm md:text-base opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1"></i>
            </a>
            
        </div>
        
    </div>
    
    <!-- Script para efectos adicionales -->
    <script>
        // Prevenir scroll horizontal en toda la página
        document.body.style.overflowX = 'hidden';
        document.documentElement.style.overflowX = 'hidden';
        
        // Asegurar que no haya elementos desbordados
        window.addEventListener('load', function() {
            // Verificar si hay scroll horizontal
            if (document.body.scrollWidth > window.innerWidth) {
                document.body.style.overflowX = 'hidden';
                document.documentElement.style.overflowX = 'hidden';
            }
        });
        
        // Efecto ripple mejorado para el botón de login
        const loginBtn = document.querySelector('button[type="submit"]');
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.4)';
                ripple.style.transform = 'scale(0)';
                ripple.style.transition = 'transform 0.6s ease-out';
                ripple.style.pointerEvents = 'none';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.style.transform = 'scale(4)';
                }, 10);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
        
        // Efecto de clic para el botón flotante
        const backBtn = document.querySelector('.btn-back');
        if (backBtn) {
            backBtn.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.4)';
                ripple.style.transform = 'scale(0)';
                ripple.style.transition = 'transform 0.6s ease-out';
                ripple.style.pointerEvents = 'none';
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.style.transform = 'scale(4)';
                }, 10);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
        
        // Mejora de accesibilidad: permitir mostrar/ocultar contraseña
        const passwordInput = document.querySelector('input[name="contrasena"]');
        if (passwordInput) {
            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-amber-400 transition-colors z-10';
            toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
            toggleBtn.style.background = 'transparent';
            toggleBtn.style.border = 'none';
            toggleBtn.style.cursor = 'pointer';
            
            const parentDiv = passwordInput.parentElement;
            if (parentDiv) {
                parentDiv.style.position = 'relative';
                parentDiv.appendChild(toggleBtn);
                
                let isPasswordVisible = false;
                toggleBtn.addEventListener('click', () => {
                    isPasswordVisible = !isPasswordVisible;
                    passwordInput.type = isPasswordVisible ? 'text' : 'password';
                    toggleBtn.innerHTML = isPasswordVisible ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                });
            }
        }
    </script>
    
    <!-- Estilos responsivos adicionales -->
    <style>
        /* Optimizaciones para móviles */
        @media (max-width: 640px) {
            .backdrop-blur-custom {
                backdrop-filter: blur(8px);
            }
            
            .hover-ripple:hover::after {
                width: 200px;
                height: 200px;
            }
            
            .btn-back {
                bottom: 16px;
                right: 16px;
                padding: 10px 18px;
            }
        }
        
        @media (max-width: 480px) {
            .p-4 {
                padding: 0.75rem;
            }
        }
        
        /* Soporte para navegadores más antiguos */
        @supports not (backdrop-filter: blur(10px)) {
            .backdrop-blur-custom {
                background: rgba(15, 23, 42, 0.95);
            }
        }
        
        /* Estilo para autocompletado */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.1) inset;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* Animación sutil para el botón flotante */
        .btn-back {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes softPulse {
            0%, 100% {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }
            50% {
                box-shadow: 0 15px 30px -5px rgba(239, 68, 68, 0.3);
            }
        }
        
        .btn-back {
            animation: fadeInUp 0.5s ease-out, softPulse 2s ease-in-out infinite;
        }
        
        .btn-back:hover {
            animation: none;
        }
        
        /* Asegurar que no haya scroll horizontal en ningún dispositivo */
        @media (max-width: 100%) {
            body, html {
                overflow-x: hidden !important;
                width: 100% !important;
            }
        }
        
        /* Prevenir cualquier desbordamiento de elementos */
        img, video, iframe, canvas, svg {
            max-width: 100%;
            height: auto;
        }
        
        /* Asegurar que el fondo cubra completamente en todos los dispositivos */
        body {
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
        }
    </style>
    
</body>
</html>