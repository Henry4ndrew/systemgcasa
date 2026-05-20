<?php
// foot.php - Versión corregida
// Asegurar que el archivo de datos globales está incluido
if (!function_exists('getDatoEmpresa')) {
    require_once 'datos_globales.php';
}

// Definir BASE_URL si no está definida
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
?>

<footer class="bg-gradient-to-r from-[#1e3a5f] to-[#2c5282] text-white mt-auto">
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            
            <!-- Columna Izquierda - Información de la empresa -->
            <div class="space-y-4">
                <div class="flex justify-center md:justify-start">
                    <img src="<?php echo BASE_URL; ?>public/img/logoGcasaclub.avif" 
                         alt="Logo G Casa Club" 
                         class="h-16 md:h-20 w-auto object-contain drop-shadow-lg"
                         onerror="this.src='<?php echo BASE_URL; ?>img/logoGcasaclub.avif'">
                </div>
                <p class="text-gray-200 text-sm md:text-base leading-relaxed text-center md:text-left">
                    Somos una empresa textil, especializada en la confección de ropa de cama con materias primas de alta calidad, mano de obra calificada y maquinarias industrializadas en línea hogar, hotelera, hospitalaria e institucional con producción 100% nacional, hecho en Bolivia.
                </p>
            </div>
            
            <!-- Columna Central - Contactos -->
            <div class="space-y-4">
                <h3 class="text-xl md:text-2xl font-bold text-orange-400 text-center md:text-left">
                    <i class="fas fa-phone-alt mr-2"></i>Contactos:
                </h3>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-center md:text-left">
                        <span class="font-semibold text-gray-200 min-w-[130px]">
                            <i class="fab fa-whatsapp text-green-400 mr-2"></i>Celular tienda:
                        </span>
                        <span class="text-gray-100">
                            <?php echo getDatoEmpresa('celular_tienda', 'No disponible'); ?>
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-center md:text-left">
                        <span class="font-semibold text-gray-200 min-w-[130px]">
                            <i class="fas fa-building mr-2"></i>Dirección Fábrica:
                        </span>
                        <span class="text-gray-100">
                            <?php echo getDatoEmpresa('direccion_fabrica', 'No disponible'); ?>
                        </span>
                    </div>
                    <?php if (existeDatoEmpresa('email')): ?>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-center md:text-left">
                        <span class="font-semibold text-gray-200 min-w-[130px]">
                            <i class="fas fa-envelope mr-2"></i>Email:
                        </span>
                        <span class="text-gray-100">
                            <?php echo getDatoEmpresa('email'); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Columna Derecha - Pedidos en línea -->
            <div class="space-y-4">
                <h3 class="text-xl md:text-2xl font-bold text-orange-400 text-center md:text-left">
                    <i class="fas fa-shopping-cart mr-2"></i>Pedidos en línea
                </h3>
                <p class="text-gray-200 text-sm md:text-base text-center md:text-left">
                    Para realizar un pedido en línea, puedes hacerlo mediante WhatsApp
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                    <?php if (existeDatoEmpresa('celular_tienda')): ?>
                    <button onclick="redireccionar('<?php echo getWhatsAppUrl('tienda'); ?>')" 
                            class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold px-6 py-2 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-xl"></i>
                        Tienda
                    </button>
                    <?php endif; ?>
                    
                    <?php if (existeDatoEmpresa('celular_fabrica')): ?>
                    <button onclick="redireccionar('<?php echo getWhatsAppUrl('fabrica'); ?>')" 
                            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-6 py-2 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-chart-line text-xl"></i>
                        Gerencia
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Barra inferior de copyright -->
    <div class="border-t border-white/20 bg-black/20">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-sm text-gray-300">
                <p class="text-center md:text-left">
                    © <?php echo date('Y'); ?> G Casa Club - Todos los derechos reservados.
                </p>
                <p class="text-center md:text-right">
                    Desarrollado por <a href="https://b1tsoft.kesug.com" target="_blank" class="text-orange-400 hover:text-orange-300 transition-colors">Henry4ndrew | B1tsoft</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
function redireccionar(url) {
    window.open(url, '_blank');
}
</script>