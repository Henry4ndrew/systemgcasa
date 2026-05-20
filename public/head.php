
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo $title; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 (gratuito) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom styles para gradientes personalizados -->
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Definición de los gradientes exactamente como los tienes */
        :root {
            --orange-meta: linear-gradient(135deg, #b35400, #e67c00, #ff9800, #e67c00, #b35400);
            --blue-meta: linear-gradient(135deg, #1e3a5f, #2c5282, #4a7bac, #2c5282, #1e3a5f);
            --gold: linear-gradient(135deg, #daa520, #ffd700, #b8860b, #ffdf00, #daa520);
        }
        
        /* Clases utilitarias para los gradientes */
        .bg-orange-meta {
            background: var(--orange-meta);
        }
        
        .bg-blue-meta {
            background: var(--blue-meta);
        }
        
        .bg-gold {
            background: var(--gold);
        }
        
        /* Efecto hover para los enlaces */
        .nav-link {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
        }
        
        /* Animación sutil del logo */
        .logo-img {
            transition: transform 0.3s ease;
            /* transform: scale(1.05); */
        }
        
        /* Logo container con fondo semi-transparente - PADDING REDUCIDO */
        .logo-container {
            backdrop-filter: blur(8px);
            border-radius: 0.75rem;
        }
        
        
        /* HEADER FIJO AZUL - sin cambios al hacer scroll */
        .main-header {
            background: var(--blue-meta);
            transition: none;
        }
        
        /* Eliminar cualquier efecto de scroll en el header */
        .main-header.header-scrolled {
            background: var(--blue-meta) !important;
            backdrop-filter: none !important;
        }
        
        /* Menú móvil - animaciones profesionales */
        .mobile-menu {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: top;
            animation: slideDown 0.3s ease-out;
            background: transparent;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Animación para los items del menú móvil */
        .mobile-nav-item {
            animation: fadeInSlide 0.3s ease-out backwards;
            transform-origin: left;
        }
        
        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Delay para cada item del menú */
        .mobile-nav-item:nth-child(1) { animation-delay: 0.05s; }
        .mobile-nav-item:nth-child(2) { animation-delay: 0.1s; }
        .mobile-nav-item:nth-child(3) { animation-delay: 0.15s; }
        .mobile-nav-item:nth-child(4) { animation-delay: 0.2s; }
        .mobile-nav-item:nth-child(5) { animation-delay: 0.25s; }
        
        /* Efecto ripple al hacer click */
        .ripple-effect {
            position: relative;
            overflow: hidden;
        }
        
        .ripple-effect:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .ripple-effect:active:after {
            width: 300px;
            height: 300px;
        }
        
        /* Ajustes responsivos adicionales */
        @media (max-width: 768px) {
            .nav-link {
                padding: 0.75rem 1rem;
                text-align: center;
                border-radius: 0.5rem;
            }
            
            .nav-link:hover {
                background: rgba(255, 255, 255, 0.1);
            }
        }
        
        /* Fondo semi-transparente para el botón de login en móvil */
        .login-btn-mobile {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-btn-mobile:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
        }
        
        /* Scroll suave para toda la página */
        html {
            scroll-behavior: smooth;
        }
        
        /* Asegurar que el header nunca cambie de color */
        .main-header,
        .main-header.sticky,
        .main-header.fixed,
        .main-header.header-scrolled {
            background: var(--blue-meta) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
    </style>
</head> 
<body class="bg-gray-50">
    <!-- Header con gradiente AZUL (blue-meta) - FIJO sin cambios al hacer scroll -->
    <header id="mainHeader" class="main-header shadow-lg sticky top-0 z-50 transition-none">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-row items-center justify-between py-3 sm:py-4">
                
                <!-- Logo container con fondo semi-transparente - PADDING REDUCIDO -->
                <div class="logo-container flex items-center bg-white/30 rounded-xl shadow-sm">
                    <img src="<?php echo $base_url; ?>img/logoGcasaclub.avif" 
                        alt="Logo de GcasaClub" 
                        class="logo-img h-14 sm:h-18 md:h-20 w-auto object-contain drop-shadow-md">
                </div>

                <!-- Navegación para escritorio (visible en sm y más grande) -->
                <nav class="hidden sm:flex flex-wrap items-center justify-center gap-1 md:gap-2 lg:gap-4">
                    <a href="<?php echo $base_url2; ?>index.php" 
                       class="nav-link px-3 py-2 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-home mr-2"></i>Inicio
                    </a>
                    <a href="<?php echo $base_url; ?>empresa.php" 
                       class="nav-link px-3 py-2 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-building mr-2"></i>Empresa
                    </a>
                    <a href="<?php echo $base_url; ?>contacto.php" 
                       class="nav-link px-3 py-2 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-envelope mr-2"></i>Contacto
                    </a>
                    <a href="<?php echo $base_url; ?>tienda-virtual.php" 
                       class="nav-link px-3 py-2 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300">
                        <i class="fas fa-box mr-2"></i>Tienda virtual
                    </a>
                    <a href="<?php echo $base_url2; ?>login.php" 
                       class="nav-link px-4 py-2 bg-white/20 text-white font-semibold rounded-full hover:bg-white/30 transition-all duration-300 shadow-md">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar sesión
                    </a>
                </nav>
                
                <!-- Botón menú hamburguesa para móvil -->
                <div class="sm:hidden">
                    <button id="menuToggle" class="ripple-effect text-white focus:outline-none focus:ring-2 focus:ring-white/50 rounded-lg p-3 transition-all duration-300 hover:bg-white/10">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Menú móvil desplegable con animaciones profesionales -->
            <div id="mobileNav" class="mobile-menu hidden sm:hidden pb-4 space-y-2">
                <a href="<?php echo $base_url2; ?>index.php" 
                   class="mobile-nav-item nav-link block px-4 py-3 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 ripple-effect">
                    <i class="fas fa-home mr-3 w-5"></i>Inicio
                </a>
                <a href="<?php echo $base_url; ?>empresa.php" 
                   class="mobile-nav-item nav-link block px-4 py-3 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 ripple-effect">
                    <i class="fas fa-building mr-3 w-5"></i>Empresa
                </a>
                <a href="<?php echo $base_url; ?>contacto.php" 
                   class="mobile-nav-item nav-link block px-4 py-3 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 ripple-effect">
                    <i class="fas fa-envelope mr-3 w-5"></i>Contacto
                </a>
                <a href="<?php echo $base_url; ?>tienda-virtual.php" 
                   class="mobile-nav-item nav-link block px-4 py-3 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 ripple-effect">
                    <i class="fas fa-box mr-3 w-5"></i>Tienda virtual
                </a>
                
                <!-- Línea separadora animada -->
                <div class="border-t border-white/20 my-2 transform transition-all duration-300"></div>
                
                <!-- Botón de login en la parte inferior del menú móvil -->
                <a href="<?php echo $base_url2; ?>login.php" 
                   class="mobile-nav-item nav-link block px-4 py-3 mt-2 bg-gradient-to-r from-white/20 to-white/10 text-white font-semibold rounded-lg hover:bg-white/30 transition-all duration-300 border border-white/30 ripple-effect">
                    <i class="fas fa-sign-in-alt mr-3 w-5"></i>Iniciar sesión
                </a>
            </div>
        </div>
    </header>


    <!-- Script para menú móvil -->
    <script>
        // Toggle para el menú móvil con animación mejorada
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        
        if (menuToggle && mobileNav) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isHidden = mobileNav.classList.contains('hidden');
                
                if (isHidden) {
                    mobileNav.classList.remove('hidden');
                    // Cambiar icono a X
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-times text-2xl';
                    
                    // Agregar animación a los items
                    const items = document.querySelectorAll('.mobile-nav-item');
                    items.forEach((item, index) => {
                        item.style.animation = `fadeInSlide 0.3s ease-out ${index * 0.05}s backwards`;
                    });
                } else {
                    // Animación de salida
                    const items = document.querySelectorAll('.mobile-nav-item');
                    items.forEach((item) => {
                        item.style.animation = '';
                    });
                    
                    setTimeout(() => {
                        mobileNav.classList.add('hidden');
                    }, 200);
                    
                    // Cambiar icono a hamburguesa
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-bars text-2xl';
                }
            });
        }
        
        // Cerrar menú móvil al hacer click en un enlace
        const mobileLinks = document.querySelectorAll('#mobileNav a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (mobileNav && !mobileNav.classList.contains('hidden')) {
                    // Animación de salida antes de cerrar
                    const items = document.querySelectorAll('.mobile-nav-item');
                    items.forEach((item) => {
                        item.style.animation = '';
                    });
                    
                    setTimeout(() => {
                        mobileNav.classList.add('hidden');
                        const icon = menuToggle.querySelector('i');
                        icon.className = 'fas fa-bars text-2xl';
                    }, 200);
                }
            });
        });
        
        // NOTA: Se eliminó el efecto de scroll que cambiaba el color del header
        // El header ahora permanece AZUL constantemente
        
        // Efecto ripple mejorado
        const rippleElements = document.querySelectorAll('.ripple-effect');
        rippleElements.forEach(element => {
            element.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = `${size}px`;
                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;
                ripple.style.position = 'absolute';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.4)';
                ripple.style.borderRadius = '50%';
                ripple.style.transform = 'scale(0)';
                ripple.style.transition = 'transform 0.6s, opacity 0.6s';
                ripple.style.pointerEvents = 'none';
                
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.style.transform = 'scale(1)';
                    ripple.style.opacity = '0';
                    setTimeout(() => ripple.remove(), 600);
                }, 10);
            });
        });
        
        // Cerrar menú al hacer click fuera
        document.addEventListener('click', function(event) {
            if (menuToggle && mobileNav) {
                const isClickInside = menuToggle.contains(event.target) || mobileNav.contains(event.target);
                if (!isClickInside && !mobileNav.classList.contains('hidden')) {
                    mobileNav.classList.add('hidden');
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-bars text-2xl';
                }
            }
        });
    </script>



