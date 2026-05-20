<?php
  $base_url = ''; 
  $base_url2 = '../'; 
  $title = 'Sobre nosotros - G Casa Club';
  require '../includes/conexion.php';
  include 'head.php';

  // Obtener imágenes de clientes
  $queryImagenes = "SELECT url_img FROM img_section_clientes";
  $resultImagenes = $conexion->query($queryImagenes);
  $imagenes = [];
  if ($resultImagenes) {
      while ($row = $resultImagenes->fetch_assoc()) {
          if (isset($row['url_img'])) {
              $imagenes[] = trim($row['url_img']);
          }
      }
  }

  if (!isset($datos_empresa)) {
      $queryDatos = "SELECT direccion_fabrica, celular_fabrica, email FROM datos LIMIT 1";
      $resultDatos = $conexion->query($queryDatos);
      $datos_empresa = $resultDatos ? $resultDatos->fetch_assoc() : [];
  }
?>
<head>
          <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-93362N4ZZR"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-93362N4ZZR');
    </script>
    <!-- Google Tag Manager -->
    <script>
      (function(w,d,s,l,i){
        w[l]=w[l]||[];
        w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),
            dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-WT9C3LTV');
     </script>
</head>




<style>
    .value-card {
        transition: all 0.3s ease;
    }
    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .client-logo {
        transition: all 0.3s ease;
        filter: grayscale(100%);
        opacity: 0.7;
    }
    .client-logo:hover {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }
    .section-title {
        position: relative;
        display: inline-block;
    }
    .section-title:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #e67c00, #ff9800);
        border-radius: 3px;
    }
    .section-title-left:after {
        left: 0;
        transform: none;
    }
</style>

    <div class="bg-white shadow-md sticky top-0 z-30 bg-orange-meta">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-white">Sobre Nosotros</h1>
            </div>
        </div>
    </div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 mt-8 md:mt-12 space-y-16 md:space-y-20">

    <!-- PORTADA -->
    <div class="relative rounded-3xl overflow-hidden shadow-xl -mt-8 md:-mt-12">
        <img src="../img/portadaGCasaClub2.avif" 
             alt="G Casa Club - Sobre nosotros" 
             class="w-full h-64 md:h-96 object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30 flex items-center justify-center">
            <h2 class="text-4xl md:text-6xl font-bold text-white text-center tracking-tight">
                G Casa Club
            </h2>
        </div>
    </div>

    <!-- MISIÓN Y VISIÓN -->
    <div class="grid md:grid-cols-2 gap-8 md:gap-12">
        <!-- Misión -->
        <div class="bg-white rounded-3xl shadow-lg p-8 text-center hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="w-24 h-24 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-6">
                <img src="img/iconMision.png" alt="Misión" class="w-12 h-12 object-contain">
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Misión</h2>
            <p class="text-gray-600 leading-relaxed">
                "Somos una empresa textil, especializada en la confección de ropa con materias primas de alta calidad, 
                mano de obra calificada y maquinarias industrializadas en línea hogar, hotelera, hospitalaria e institucional 
                con producción 100% nacional, hecho en Bolivia."
            </p>
        </div>

        <!-- Visión -->
        <div class="bg-white rounded-3xl shadow-lg p-8 text-center hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="w-24 h-24 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-6">
                <img src="img/iconVision.png" alt="Visión" class="w-12 h-12 object-contain">
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Visión</h2>
            <p class="text-gray-600 leading-relaxed">
                "Alcanzar el estándar de elite y reconocimiento a nivel nacional, brindando satisfacción a nuestros clientes a cada entrega."
            </p>
        </div>
    </div>

    <!-- VALORES -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-3xl p-8 md:p-12">
        <div class="text-center mb-12">
            <div class="w-24 h-24 mx-auto bg-gold rounded-full flex items-center justify-center mb-4">
                <img src="img/iconDiamond.webp" alt="Valores" class="w-12 h-12 object-contain">
            </div>
            <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-800">Nuestros Valores</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                "En G CASA CLUB somos positivos. Nuestro jefe es el cliente. Los problemas son retos, no obstáculos, 
                compartimos los triunfos. Trabajamos en equipo. Amamos lo que hacemos, somos proactivos, nos ponemos en tu lugar."
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Honestidad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-hand-holding-heart text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Honestidad</h3>
            </div>

            <!-- Responsabilidad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Responsabilidad</h3>
            </div>

            <!-- Trabajo en equipo -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Trabajo en equipo</h3>
            </div>

            <!-- Empatía -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-pink-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-heart text-pink-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Empatía</h3>
            </div>

            <!-- Solidaridad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-hands-helping text-red-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Solidaridad</h3>
            </div>

            <!-- Tolerancia -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-balance-scale text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Tolerancia</h3>
            </div>

            <!-- Seguridad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Seguridad</h3>
            </div>

            <!-- Puntualidad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-teal-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-clock text-teal-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Puntualidad</h3>
            </div>

            <!-- Competitividad -->
            <div class="value-card bg-white rounded-2xl p-4 text-center shadow-md">
                <div class="w-16 h-16 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-chart-line text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800">Competitividad</h3>
            </div>
        </div>
    </div>

    <!-- UBICACIÓN DE LA FÁBRICA -->
    <?php if (!empty($datos_empresa['direccion_fabrica'])): ?>
    <div class="bg-blue-meta rounded-3xl p-8 md:p-12 text-white">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <img src="img/iconGpsDIF.gif" alt="Ubicación" class="w-20 h-20 object-contain">
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">
                    <i class="fas fa-map-marker-alt text-orange-400 mr-2"></i>
                    Ubicación de la Fábrica
                </h2>
                <p class="text-lg text-gray-100 leading-relaxed">
                    <?php echo htmlspecialchars($datos_empresa['direccion_fabrica']); ?>
                </p>
                <?php if (!empty($datos_empresa['celular_fabrica'])): ?>
                <p class="mt-4 text-gray-200">
                    <i class="fab fa-whatsapp text-green-400 mr-2"></i>
                    Teléfono: <?php echo htmlspecialchars($datos_empresa['celular_fabrica']); ?>
                </p>
                <?php endif; ?>
                <?php if (!empty($datos_empresa['email'])): ?>
                <p class="mt-2 text-gray-200">
                    <i class="fas fa-envelope text-orange-400 mr-2"></i>
                    Email: <?php echo htmlspecialchars($datos_empresa['email']); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- CARTERA DE CLIENTES -->
    <div class="space-y-8">
        <div class="text-center">
            <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-800">Cartera de Clientes</h2>
            <p class="text-gray-600 mt-4 max-w-3xl mx-auto">
                En G Casa Club nos enorgullecemos de trabajar con una amplia gama de clientes en la industria textil. 
                Nuestra dedicación a la calidad y el compromiso con la satisfacción del cliente nos ha permitido forjar 
                relaciones sólidas en todo el sector. Gracias a nuestro enfoque personalizado y a nuestra capacidad de 
                adaptarnos a las necesidades específicas de cada proyecto, nuestros clientes continúan confiando en nosotros 
                para materializar sus ideas y desarrollar productos de alta calidad.
            </p>
        </div>

        <?php if (!empty($imagenes)): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach ($imagenes as $imagen): ?>
            <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-center hover:shadow-lg transition-all duration-300">
                <img src="<?php echo htmlspecialchars('../' . $imagen); ?>" 
                     alt="Logo cliente" 
                     class="client-logo max-w-full h-auto max-h-20 object-contain"
                     onerror="this.onerror=null; this.src='img/iconGalery.avif';">
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 bg-gray-50 rounded-3xl">
            <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Próximamente más clientes</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- LLAMADO A LA ACCIÓN -->
    <div class="bg-orange-meta-gold rounded-3xl p-8 md:p-12 text-center text-white">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">¿Listo para trabajar con nosotros?</h2>
        <p class="text-lg mb-6 opacity-90">Contáctanos y descubre la calidad de nuestros productos</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="contacto.php" class="inline-flex items-center justify-center gap-2 bg-white text-orange-600 font-semibold px-6 py-3 rounded-xl hover:bg-gray-100 transition-all duration-300">
                <i class="fas fa-envelope"></i>
                Contáctanos
            </a>
            <a href="tienda-virtual.php" class="inline-flex items-center justify-center gap-2 bg-orange-700 hover:bg-orange-800 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300">
                <i class="fas fa-store"></i>
                Ver Productos
            </a>
        </div>
    </div>

</main>

<?php include 'foot.php'; ?>