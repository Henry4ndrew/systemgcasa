<?php
  $base_url = ''; 
  $base_url2 = '../'; 
  $title = 'Contacto - G Casa Club';
  require '../includes/conexion.php';
  include 'head.php';

  // Asegurar que datos_globales está cargado
  if (!function_exists('getDatoEmpresa')) {
      require_once 'datos_globales.php';
  }
?>

<style>
    .contact-card {
        transition: all 0.3s ease;
    }
    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .social-icon {
        transition: all 0.3s ease;
    }
    .social-icon:hover {
        transform: translateY(-3px) scale(1.05);
    }
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        border-color: #f97316;
        outline: none;
    }
    .map-card {
        transition: all 0.3s ease;
    }
    .map-card:hover {
        transform: scale(1.02);
    }
</style>


    <div class="bg-white shadow-md sticky top-0 z-30 bg-orange-meta">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-white">Contacto</h1>
            </div>
        </div>
    </div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-12 md:space-y-16">

    <!-- TÍTULO DE PÁGINA -->
    <div class="text-center">
   
        <div class="w-20 h-1 bg-orange-500 mx-auto rounded-full"></div>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
            Estamos aquí para ayudarte. Contáctanos a través de cualquiera de nuestros canales
        </p>
    </div>

    <!-- INFORMACIÓN DE CONTACTO - TARJETAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Teléfono / WhatsApp -->
        <div class="contact-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                <i class="fab fa-whatsapp text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">WhatsApp / Teléfono</h3>
            <?php if (existeDatoEmpresa('celular_tienda')): ?>
            <p class="text-gray-600 mb-3">
                <strong>Tienda:</strong> 
                <a href="https://wa.me/591<?php echo getDatoEmpresa('celular_tienda'); ?>" class="text-green-600 hover:text-green-700">
                    <?php echo getDatoEmpresa('celular_tienda'); ?>
                </a>
            </p>
            <?php endif; ?>
            <?php if (existeDatoEmpresa('celular_fabrica')): ?>
            <p class="text-gray-600">
                <strong>Gerencia:</strong> 
                <a href="https://wa.me/591<?php echo getDatoEmpresa('celular_fabrica'); ?>" class="text-green-600 hover:text-green-700">
                    <?php echo getDatoEmpresa('celular_fabrica'); ?>
                </a>
            </p>
            <?php endif; ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="<?php echo getWhatsAppUrl('tienda'); ?>" target="_blank" 
                   class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-all duration-300">
                    <i class="fab fa-whatsapp"></i>
                    Enviar WhatsApp
                </a>
            </div>
        </div>

        <!-- Email -->
        <div class="contact-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-envelope text-blue-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Correo Electrónico</h3>
            <?php if (existeDatoEmpresa('email')): ?>
            <p class="text-gray-600 mb-3">
                <a href="mailto:<?php echo getDatoEmpresa('email'); ?>" class="text-blue-600 hover:text-blue-700">
                    <?php echo getDatoEmpresa('email'); ?>
                </a>
            </p>
            <?php else: ?>
            <p class="text-gray-500">No disponible</p>
            <?php endif; ?>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <a href="mailto:<?php echo getDatoEmpresa('email', 'info@gcasaclub.com'); ?>" 
                   class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-300">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Email
                </a>
            </div>
        </div>

        <!-- Horario de Atención -->
        <div class="contact-card bg-white rounded-2xl shadow-lg p-6 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clock text-orange-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Horario de Atención</h3>
            <?php if (existeDatoEmpresa('horario_atencion')): ?>
            <p class="text-gray-600 whitespace-pre-line">
                <?php echo nl2br(getDatoEmpresa('horario_atencion')); ?>
            </p>
            <?php else: ?>
            <p class="text-gray-600">
                Lunes a Viernes: 8:00 - 18:00<br>
                Sábados: 9:00 - 13:00
            </p>
            <?php endif; ?>
        </div>
    </div>

   

        <!-- UBICACIÓN Y MAPA -->
         <!-- UBICACIÓN Y MAPA -->
        <div class="space-y-6">
            
            <!-- Dirección de Fábrica -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-orange-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Dirección de Fábrica</h3>
                        <?php if (existeDatoEmpresa('direccion_fabrica')): ?>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo getDatoEmpresa('direccion_fabrica'); ?>
                        </p>
                        <?php else: ?>
                        <p class="text-gray-500">No disponible</p>
                        <?php endif; ?>
                        
                        <?php if (existeDatoEmpresa('gps_fabrica')): ?>
                        <div class="mt-3">
                            <a href="<?php echo getDatoEmpresa('gps_fabrica'); ?>" target="_blank" 
                               class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700">
                                <i class="fas fa-map-marker-alt"></i>
                                Ver en Google Maps
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dirección de Tienda -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-store text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Dirección de Tienda</h3>
                        <?php if (existeDatoEmpresa('direccion_tienda')): ?>
                        <p class="text-gray-600 leading-relaxed">
                            <?php echo getDatoEmpresa('direccion_tienda'); ?>
                        </p>
                        <?php else: ?>
                        <p class="text-gray-500">Disponible en "Ver ubicación en Google maps"</p>
                        <?php endif; ?>
                        
                        <?php if (existeDatoEmpresa('gps_tienda')): ?>
                        <div class="mt-3">
                            <a href="<?php echo getDatoEmpresa('gps_tienda'); ?>" target="_blank" 
                               class="inline-flex items-center gap-2 text-green-600 hover:text-green-700">
                                <i class="fas fa-map-marker-alt"></i>
                                Ver ubicación en Google Maps
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="mt-3 text-gray-400 text-sm">
                            <i class="fas fa-info-circle"></i>
                            La ubicación exacta se proporcionará al contactarnos
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>



    <!-- REDES SOCIALES -->
    <?php 
    $socialLinks = [];
    if (existeDatoEmpresa('facebook')) {
        $socialLinks['facebook'] = [
            'url' => getDatoEmpresa('facebook'), 
            'icon' => 'fab fa-facebook', 
            'color' => 'bg-[#1877F2]', 
            'nombre' => 'Facebook'
        ];
    }
    if (existeDatoEmpresa('instagram')) {
        $socialLinks['instagram'] = [
            'url' => getDatoEmpresa('instagram'), 
            'icon' => 'fab fa-instagram', 
            'color' => 'bg-gradient-to-tr from-[#F58529] to-[#DD2A7B]', 
            'nombre' => 'Instagram'
        ];
    }
    if (existeDatoEmpresa('tiktok')) {
        $socialLinks['tiktok'] = [
            'url' => getDatoEmpresa('tiktok'), 
            'icon' => 'fab fa-tiktok', 
            'color' => 'bg-black', 
            'nombre' => 'TikTok'
        ];
    }
    if (existeDatoEmpresa('celular_tienda')) {
        $socialLinks['whatsapp'] = [
            'url' => getWhatsAppUrl('tienda'), 
            'icon' => 'fab fa-whatsapp', 
            'color' => 'bg-[#25D366]', 
            'nombre' => 'WhatsApp'
        ];
    }
    ?>

    <?php if (!empty($socialLinks)): ?>
    <div class="space-y-6">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center">Síguenos en Redes Sociales</h2>
        <div class="flex flex-wrap justify-center gap-6">
            <?php foreach($socialLinks as $key => $social): ?>
                <a href="<?= $social['url'] ?>" target="_blank" rel="noopener noreferrer" 
                   class="social-icon group flex flex-col items-center bg-white rounded-2xl shadow-sm hover:shadow-xl p-5 w-36 transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="<?= $social['color'] ?> w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl shadow-md mb-3 group-hover:scale-110 transition">
                        <i class="<?= $social['icon'] ?>"></i>
                    </div>
                    <h3 class="font-semibold text-gray-700"><?= $social['nombre'] ?></h3>
                    <span class="text-xs text-gray-400 mt-1">Síguenos</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- PREGUNTAS FRECUENTES -->
    <div class="bg-gray-50 rounded-3xl p-8 md:p-12">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Preguntas Frecuentes</h2>
            <div class="w-20 h-1 bg-orange-500 mx-auto rounded-full mt-3"></div>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-truck text-orange-500"></i>
                        ¿Realizan envíos a todo el país?
                    </h3>
                    <p class="text-gray-600">Sí, realizamos envíos a toda Bolivia con entregas rápidas y seguras.</p>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-credit-card text-orange-500"></i>
                        ¿Cuáles son los métodos de pago?
                    </h3>
                    <p class="text-gray-600">Aceptamos transferencias bancarias, depósitos y pago mediante código QR.</p>
                </div>
            </div>
            <div class="space-y-4">
                <div class="bg-white rounded-xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-undo-alt text-orange-500"></i>
                        ¿Puedo personalizar mis pedidos?
                    </h3>
                    <p class="text-gray-600">Sí, ofrecemos personalización según tus necesidades. Contáctanos para más información.</p>
                </div>
            </div>
        </div>
    </div>

</main>

<?php include 'foot.php'; ?>