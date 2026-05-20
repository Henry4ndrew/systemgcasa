<?php 

require 'datos_globales.php';
?>
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
        .bg-orange-meta-gold{
           background: var(--orange-meta); 
           transition all 0.3s ease;
        }
        .bg-orange-meta-gold:hover{
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
        }
        
        /* Logo container con fondo semi-transparente */
        .logo-container {
            backdrop-filter: blur(8px);
            border-radius: 0.75rem;
            display: inline-block;
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
        
        /* ESTILOS MEJORADOS PARA EL CARRITO EN ESCRITORIO */
        .cart-link-desktop {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .cart-badge-desktop {
            position: absolute;
            top: -8px;
            right: -12px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            animation: pulse 2s infinite;
        }
        
        /* ESTILOS MEJORADOS PARA EL CARRITO EN MÓVIL - Botón independiente */
        .cart-mobile-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            transition: all 0.3s ease;
        }
        
        .cart-mobile-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }
        
        .cart-badge-mobile {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 20px;
            height: 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.5);
            animation: pulse 2s infinite;
        }
        
        /* ESTILOS PARA EL CARRITO EN MENÚ MÓVIL DESPLEGABLE */
        .cart-menu-item {
            position: relative;
        }
        
        .cart-badge-menu {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            min-width: 22px;
            height: 22px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Animación de pulso para el contador */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Animación de rebote cuando se agrega un producto */
        .cart-badge-bounce {
            animation: bounce 0.5s ease !important;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.3);
            }
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
        
        /* Contenedor de acciones móvil (carrito + menú) */
        .mobile-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Layout para escritorio: logo izquierda, nav centrado, login derecha */
        .header-desktop-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        
        .header-logo {
            flex: 0 0 auto;
        }
        
        .header-nav {
            flex: 1 1 auto;
            display: flex;
            justify-content: center;
        }
        
        .header-login {
            flex: 0 0 auto;
        }
        
        /* Ajuste para que la navegación no ocupe todo el espacio y permita centrado perfecto */
        @media (min-width: 640px) {
            .header-nav nav {
                margin: 0 auto;
            }
        }
    </style>
</head> 
<body class="bg-gray-50">
    <!-- Header con gradiente AZUL (blue-meta) - FIJO sin cambios al hacer scroll -->
    <header id="mainHeader" class="main-header shadow-lg sticky top-0 z-50 transition-none">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Desktop Layout (visible en sm y más grande) -->
            <div class="hidden sm:flex header-desktop-layout py-3 sm:py-4">
                <!-- Logo - Izquierda -->
                <div class="header-logo">
                    <a href="<?php echo $base_url2; ?>index.php" class="logo-container flex items-center bg-white/30 rounded-xl shadow-sm">
                        <img src="<?php echo $base_url; ?>img/logoGcasaclub.avif" 
                            alt="Logo de GcasaClub" 
                            class="logo-img h-14 sm:h-18 md:h-20 w-auto object-contain drop-shadow-md">
                    </a>
                </div>
                
                <!-- Navegación centrada -->
                <div class="header-nav">
                    <nav class="flex flex-wrap items-center justify-center gap-1 md:gap-2 lg:gap-4">
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
                            <i class="fas fa-store mr-2"></i>Tienda virtual
                        </a>
                        
                        <!-- CARRITO DE ESCRITORIO -->
                        <a href="<?php echo $base_url; ?>carrito.php" 
                           class="cart-link-desktop nav-link px-3 py-2 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            Carrito
                            <span id="cartCountHeader" class="cart-badge-desktop" style="display: none;">0</span>
                        </a>
                    </nav>
                </div>
                
                <!-- Login - Derecha -->
                <div class="header-login">
                    <a href="<?php echo $base_url2; ?>login.php" 
                       class="nav-link px-4 py-2 bg-white/20 text-white font-semibold rounded-full hover:bg-white/30 transition-all duration-300 shadow-md whitespace-nowrap">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar sesión
                    </a>
                </div>
            </div>
            
            <!-- Mobile Layout (visible solo en móvil) -->
            <div class="flex sm:hidden flex-row items-center justify-between py-3">
                <!-- Logo -->
                <a href="<?php echo $base_url2; ?>index.php" class="logo-container flex items-center bg-white/30 rounded-xl shadow-sm">
                    <img src="<?php echo $base_url; ?>img/logoGcasaclub.avif" 
                        alt="Logo de GcasaClub" 
                        class="logo-img h-14 w-auto object-contain drop-shadow-md">
                </a>
                
                <!-- ACCIONES PARA MÓVIL: Botón carrito + Botón menú hamburguesa -->
                <div class="mobile-actions">
                    <!-- Botón carrito independiente para móvil -->
                    <a href="<?php echo $base_url; ?>carrito.php" 
                       class="cart-mobile-btn">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                        <span id="cartCountMobileFloating" class="cart-badge-mobile" style="display: none;">0</span>
                    </a>
                    
                    <!-- Botón menú hamburguesa -->
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
                    <i class="fas fa-store mr-3 w-5"></i>Tienda virtual
                </a>
                
                <!-- CARRITO EN MENÚ MÓVIL (con contador a la derecha) -->
                <a href="<?php echo $base_url; ?>carrito.php" 
                   class="mobile-nav-item nav-link cart-menu-item block px-4 py-3 text-white font-medium rounded-lg hover:bg-white/10 transition-all duration-300 ripple-effect">
                    <i class="fas fa-shopping-cart mr-3 w-5"></i>
                    Carrito
                    <span id="cartCountMobileMenu" class="cart-badge-menu" style="display: none;">0</span>
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


    <!-- Script para menú móvil y funciones del carrito -->
    <script>
        // ========== FUNCIONES GLOBALES DEL CARRITO ==========
        
        // Función para agregar animación de rebote al contador
        function animateCartBadge(element) {
            if (element) {
                element.classList.add('cart-badge-bounce');
                setTimeout(() => {
                    element.classList.remove('cart-badge-bounce');
                }, 500);
            }
        }
        
        // Función para actualizar los contadores del carrito en el header
        function actualizarContadoresCarrito() {
            // Obtener carrito del localStorage
            let carritoGlobal = JSON.parse(localStorage.getItem('carrito')) || [];
            
            // Calcular cantidad total de items
            let totalItems = 0;
            carritoGlobal.forEach(item => {
                totalItems += item.cantidad;
            });
            
            // Actualizar contador de escritorio
            const cartCountHeader = document.getElementById('cartCountHeader');
            if (cartCountHeader) {
                const oldValue = parseInt(cartCountHeader.textContent) || 0;
                cartCountHeader.textContent = totalItems;
                
                if (totalItems > 0) {
                    cartCountHeader.style.display = 'flex';
                    if (totalItems > oldValue) {
                        animateCartBadge(cartCountHeader);
                    }
                } else {
                    cartCountHeader.style.display = 'none';
                }
            }
            
            // Actualizar contador flotante de móvil (junto al menú)
            const cartCountMobileFloating = document.getElementById('cartCountMobileFloating');
            if (cartCountMobileFloating) {
                const oldValue = parseInt(cartCountMobileFloating.textContent) || 0;
                cartCountMobileFloating.textContent = totalItems;
                
                if (totalItems > 0) {
                    cartCountMobileFloating.style.display = 'flex';
                    if (totalItems > oldValue) {
                        animateCartBadge(cartCountMobileFloating);
                    }
                } else {
                    cartCountMobileFloating.style.display = 'none';
                }
            }
            
            // Actualizar contador dentro del menú móvil desplegable
            const cartCountMobileMenu = document.getElementById('cartCountMobileMenu');
            if (cartCountMobileMenu) {
                const oldValue = parseInt(cartCountMobileMenu.textContent) || 0;
                cartCountMobileMenu.textContent = totalItems;
                
                if (totalItems > 0) {
                    cartCountMobileMenu.style.display = 'inline-flex';
                    if (totalItems > oldValue) {
                        animateCartBadge(cartCountMobileMenu);
                    }
                } else {
                    cartCountMobileMenu.style.display = 'none';
                }
            }
            
            // Actualizar también el contador flotante de tienda-virtual.php si existe
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = totalItems;
                if (totalItems > 0) {
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }
            }
            
            return totalItems;
        }
        
        // Escuchar cambios en localStorage desde otras pestañas/ventanas
        window.addEventListener('storage', function(e) {
            if (e.key === 'carrito') {
                actualizarContadoresCarrito();
            }
        });
        
        // Inicializar contadores al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarContadoresCarrito();
        });
        
        // Crear un evento personalizado para cuando se actualiza el carrito
        function notificarActualizacionCarrito() {
            actualizarContadoresCarrito();
            // Disparar evento personalizado para que otros scripts puedan escucharlo
            window.dispatchEvent(new CustomEvent('carritoActualizado'));
        }
        
        // Exponer función global para que otros scripts la usen
        window.actualizarContadoresCarrito = actualizarContadoresCarrito;
        window.notificarActualizacionCarrito = notificarActualizacionCarrito;
        
        // ========== MENÚ MÓVIL ==========
        
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
                        if (icon) {
                            icon.className = 'fas fa-bars text-2xl';
                        }
                    }, 200);
                }
            });
        });
        
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
                    if (icon) {
                        icon.className = 'fas fa-bars text-2xl';
                    }
                }
            }
        });
    </script>