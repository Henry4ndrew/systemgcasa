<?php
  $base_url = 'public/'; 
  $base_url2 = ''; 
  $title = 'GcasaClub';
  require 'includes/conexion.php';
  include 'public/head.php';



  // Función para limpiar la ruta de la imagen
  function limpiarRuta($ruta) {
      // Eliminar '../' de la ruta
      $ruta = str_replace('../', '', $ruta);
      // Eliminar './' si existe
      $ruta = str_replace('./', '', $ruta);
      return $ruta;
  }

  // Obtener las portadas de la base de datos
  $query = "SELECT id, ruta_img, titulo, descripcion FROM portada ORDER BY id ASC";
  $result = mysqli_query($conexion, $query);
  
  $portadas = [];
  if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
          // Limpiar la ruta de la imagen
          $row['ruta_img'] = limpiarRuta($row['ruta_img']);
          $portadas[] = $row;
      }
  }




  $query = "SELECT archivo_pdf, titulo, descripcion FROM documentos_pdf";
$result = $conexion->query($query);
if ($result) {
    if ($result->num_rows > 0) {
        $pdfs = [];
        while ($row = $result->fetch_assoc()) {
            $pdfs[] = $row; 
        }
    } else {
        $pdfs = []; 
    }
} else {
    echo "Error al obtener los datos de PDF: " . $conexion->error;
}
?>

<head>
            <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-93362N4ZZR"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-93362N4ZZR');
        </script>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WT9C3LTV');</script>
        <!-- End Google Tag Manager -->
        <meta name="facebook-domain-verification" content="suftldxcx8cnycathjjbal84aoik9v" />
        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '979455517026737');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=979455517026737&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->
    <meta name="description" content="Sábanas, Edredones, Almohadas, Cubrecamas, Especializado en Hoteles, Hogares e Instituciones. COTIZACIONES VÍA WHATSAPP">
    <meta name="keywords" content="Ropa de Cama, Edredones, Juego de Sabanas">
    <meta name="robots" content="index, all, follow">
</head>


<!-- Carrusel Full Width -->
<div class="carousel-container">
    <div class="relative">
        <!-- Carrusel track -->
        <div class="carousel-track" id="carouselTrack">
            <?php if (count($portadas) > 0): ?>
                <!-- Portadas originales -->
                <?php foreach ($portadas as $portada): ?>
                    <div class="carousel-slide" style="background-image: url('<?php echo $portada['ruta_img']; ?>');">
                        <div class="overlay"></div>
                        <div class="carousel-content">
                            <h2><?php echo htmlspecialchars($portada['titulo']); ?></h2>
                            <p><?php echo htmlspecialchars($portada['descripcion']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <!-- Clonamos el primer elemento para efecto infinito -->
                <?php if (count($portadas) > 0): ?>
                    <div class="carousel-slide" style="background-image: url('<?php echo $portadas[0]['ruta_img']; ?>');">
                        <div class="overlay"></div>
                        <div class="carousel-content">
                            <h2><?php echo htmlspecialchars($portadas[0]['titulo']); ?></h2>
                            <p><?php echo htmlspecialchars($portadas[0]['descripcion']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="carousel-slide flex items-center justify-center bg-gray-200">
                    <div class="overlay"></div>
                    <p class="text-gray-500 text-lg">No hay portadas disponibles</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (count($portadas) > 0): ?>
            <!-- Botones de navegación -->
            <button id="prevBtn" class="nav-btn prev-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button id="nextBtn" class="nav-btn next-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            <!-- Indicadores/dots -->
            <div class="dots-container" id="dotsContainer">
                <?php foreach ($portadas as $index => $portada): ?>
                    <button class="dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-index="<?php echo $index; ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Estilos para el carrusel - FULL WIDTH -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .carousel-container {
        position: relative;
        width: 100vw;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        overflow: hidden;
        border-radius: 0;
        box-shadow: none;
    }
    
    .carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }
    
    .carousel-slide {
        flex: 0 0 100%;
        position: relative;
        height: 100vh;
        min-height: 500px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    /* Overlay para mejorar legibilidad del texto */
    .carousel-slide .overlay {
        position: absolute;
        inset: 0;
    }
    
    /* Contenido centrado */
    .carousel-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 10;
        width: 90%;
        max-width: 800px;
        padding: 2rem;
        border-radius: 1rem;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
    }
    
    .carousel-content h2 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .carousel-content p {
        font-size: 1.2rem;
        line-height: 1.6;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    /* Botones de navegación */
    .nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        padding: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .nav-btn:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: translateY(-50%) scale(1.1);
    }
    
    .nav-btn svg {
        width: 24px;
        height: 24px;
    }
    
    .prev-btn {
        left: 20px;
    }
    
    .next-btn {
        right: 20px;
    }
    
    /* Dots/indicadores */
    .dots-container {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        z-index: 20;
    }
    
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        padding: 0;
    }
    
    .dot:hover {
        background: rgba(255, 255, 255, 0.8);
        transform: scale(1.2);
    }
    
    .dot.active {
        width: 30px;
        border-radius: 5px;
        background: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .carousel-slide {
            height: 100vh;
            min-height: 400px;
        }
        
        .carousel-content h2 {
            font-size: 1.5rem;
        }
        
        .carousel-content p {
            font-size: 0.9rem;
        }
        
        .nav-btn {
            padding: 8px;
        }
        
        .nav-btn svg {
            width: 18px;
            height: 18px;
        }
        
        .prev-btn {
            left: 10px;
        }
        
        .next-btn {
            right: 10px;
        }
        
        .dots-container {
            bottom: 20px;
            gap: 8px;
        }
        
        .dot {
            width: 8px;
            height: 8px;
        }
        
        .dot.active {
            width: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .carousel-content {
            width: 95%;
            padding: 1rem;
        }
        
        .carousel-content h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .carousel-content p {
            font-size: 0.8rem;
        }
    }
</style>


</head>
<body class="antialiased">

    <!-- Contenedor principal con max-width y padding responsivo -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-16 md:space-y-20">

        <!-- SECCIÓN CATEGORÍAS (Líneas de producto) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <!-- Línea Hotelera -->
            <a href="public/tienda-virtual.php?categoria=hotelera" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                <div class="h-52 overflow-hidden bg-gray-100">
                    <img src="img/categoria-hotel.avif" alt="Línea Hotelera" class="w-full h-full object-cover img-hover-grow group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5 text-center">
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight">Línea Hotelera</h2>
                    <span class="inline-block mt-2 text-sm font-medium text-indigo-600 group-hover:text-indigo-800">Ver productos →</span>
                </div>
            </a>
            <!-- Línea Hogar -->
            <a href="public/tienda-virtual.php?categoria=hogar" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                <div class="h-52 overflow-hidden bg-gray-100">
                    <img src="img/hogar.avif" alt="Línea Hogar" class="w-full h-full object-cover img-hover-grow group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5 text-center">
                    <h2 class="text-xl font-bold text-gray-800">Línea Hogar</h2>
                    <span class="inline-block mt-2 text-sm font-medium text-indigo-600">Ver productos →</span>
                </div>
            </a>
            <!-- Línea Hospitalaria -->
            <a href="public/tienda-virtual.php?categoria=hospitalaria" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                <div class="h-52 overflow-hidden bg-gray-100">
                    <img src="img/hospitalaria.avif" alt="Línea Hospitalaria" class="w-full h-full object-cover img-hover-grow group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5 text-center">
                    <h2 class="text-xl font-bold text-gray-800">Línea Hospitalaria</h2>
                    <span class="inline-block mt-2 text-sm font-medium text-indigo-600">Ver productos →</span>
                </div>
            </a>
            <!-- Línea Institucional -->
            <a href="public/tienda-virtual.php?categoria=institucional" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100">
                <div class="h-52 overflow-hidden bg-gray-100">
                    <img src="img/ropaInstituciones.jpeg" alt="Línea institucional" class="w-full h-full object-cover img-hover-grow group-hover:scale-105 transition duration-500">
                </div>
                <div class="p-5 text-center">
                    <h2 class="text-xl font-bold text-gray-800">Línea Institucional</h2>
                    <span class="inline-block mt-2 text-sm font-medium text-indigo-600">Ver productos →</span>
                </div>
            </a>
        </section>

        <!-- SELLOS DE CALIDAD / BADGES (Hecho en Bolivia, Calidad, Envíos) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 py-6 border-y border-gray-200 bg-gray-50/50 rounded-3xl p-6">
            <div class="flex flex-col items-center text-center space-y-3">
                <div class="w-20 h-20 rounded-full bg-white shadow-md flex items-center justify-center p-3">
                    <img src="img/selloHechoBolivia.avif" alt="Hecho en Bolivia" class="max-w-full max-h-full object-contain">
                </div>
                <p class="font-bold text-gray-800 text-lg tracking-wide">HECHO EN BOLIVIA</p>
                <p class="text-sm text-gray-500">Apoyo a la industria nacional</p>
            </div>
            <div class="flex flex-col items-center text-center space-y-3">
                <div class="w-20 h-20 rounded-full bg-white shadow-md flex items-center justify-center p-3">
                    <img src="img/selloCalidad.avif" alt="Alta calidad" class="max-w-full max-h-full object-contain">
                </div>
                <p class="font-bold text-gray-800 text-lg tracking-wide">PRODUCTO DE CALIDAD</p>
                <p class="text-sm text-gray-500">Materias primas seleccionadas</p>
            </div>
            <div class="flex flex-col items-center text-center space-y-3">
                <div class="w-20 h-20 rounded-full bg-white shadow-md flex items-center justify-center p-3">
                    <img src="img/iconEntrega.avif" alt="Entregas a nivel Nacional" class="max-w-full max-h-full object-contain">
                </div>
                <p class="font-bold text-gray-800 text-lg tracking-wide">ENVÍOS A TODA BOLIVIA</p>
                <p class="text-sm text-gray-500">Entregas rápidas y seguras</p>
            </div>
        </section>

        
        <?php if (existeDatoEmpresa('celular_tienda')): ?>
        <a href="<?php echo getWhatsAppUrl('tienda'); ?>?text=Deseo%20comprar%20mediante%20la%20aplicación%20CONSUME%20LO%20NUESTRO" 
         class="bg-orange-meta-gold block group relative rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300">  <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 gap-6">
                    <div class="text-center md:text-left text-white space-y-2">
                        <p class="text-lg font-medium opacity-90">Click aquí si deseas comprar mediante la aplicación</p>
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">CONSUME LO NUESTRO</h2>
                        <span class="inline-flex items-center gap-2 text-emerald-100 text-sm mt-2"><i class="fas fa-arrow-right"></i> Compra segura y confiable</span>
                    </div>
                    <div class="w-32 h-32 md:w-40 md:h-40 bg-white rounded-full p-2 shadow-lg flex items-center justify-center">
                        <img src="img/logoConsumeLoNuestro.avif" alt="Logo Consume lo Nuestro" class="w-full h-full object-contain rounded-full">
                    </div>
                </div>
        </a>
        <?php else: ?>
            <div class="bg-gray-100 rounded-2xl p-8 text-center border border-gray-200">
                <div class="flex justify-center mb-4">
                    <img src="img/logoConsumeLoNuestro.avif" alt="Consume lo Nuestro" class="w-24 h-24 object-contain">
                </div>
                <h2 class="text-2xl font-bold text-gray-700">CONSUME LO NUESTRO</h2>
                <p class="text-gray-500 mt-2">Disponible para compras.</p>
            </div>
        <?php endif; ?>


        <!-- SECCIÓN SOBRE NOSOTROS con video -->
        <section class="grid lg:grid-cols-2 gap-10 items-center bg-blue-meta rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 lg:p-8">
            <div class="space-y-5">
                <h2 class="text-3xl font-bold text-white tracking-tight border-l-4 border-indigo-500 pl-4">Sobre Nosotros</h2>
                <p class="text-white leading-relaxed">Somos una empresa textil dedicada a la confección y fabricación de productos de alta calidad, especializada en ropa de cama: edredones, sábanas, cubrecamas, almohadas, etc. para el hogar, hotelería y sector hospitalario. Con más de 20 años de experiencia en el mercado, utilizamos materias primas seleccionadas y tecnología de vanguardia para ofrecer soluciones textiles que cumplen con los más altos estándares de calidad.</p>
                <p class="text-white leading-relaxed">Nuestro equipo está compuesto por profesionales altamente calificados, comprometidos con la innovación y la excelencia en cada proceso productivo. Nos enorgullece ser una empresa que apoya la industria nacional, con producción 100% local y un enfoque sostenible en el uso de recursos.</p>
                <a href="public/empresa.php" class="bg-orange-meta-gold inline-flex items-center gap-2 text-white font-semibold py-3 px-6 rounded-xl transition shadow-md hover:shadow-lg">
                    Ver más sobre nosotros <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-xl video-overlay bg-gray-900 aspect-[9/16] w-full max-w-xs mx-auto lg:max-w-sm">
                  <video src="img/gcasaclub.mp4" autoplay muted loop playsinline class="w-full h-full object-cover"></video>
                <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>
            </div>
        </section>
    <style>
        /* transiciones y efectos suaves */
        a, button, .card-hover {
            transition: all 0.2s ease;
        }
        .btn-hover-scale:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);
        }
        .img-hover-grow {
            transition: transform 0.3s ease;
        }
        .group:hover .img-hover-grow {
            transform: scale(1.02);
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .video-overlay {
            position: relative;
            border-radius: 1.5rem;
            overflow: hidden;
        }
        video {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
        .form-control-focus:focus {
            outline: none;
            ring: 2px solid #3b82f6;
            border-color: #3b82f6;
        }
        .center-image {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        @media (max-width: 768px) {
            .grid-responsive {
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            }
        }
    </style>

    
 
  <!-- REDES SOCIALES -->
<?php 
// Usar los datos globales en lugar de $data local
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
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center">Nuestras redes sociales</h2>
        <div class="flex flex-wrap justify-center gap-6">
            <?php foreach($socialLinks as $key => $social): ?>
                <a href="<?= $social['url'] ?>" target="_blank" rel="noopener noreferrer" class="group flex flex-col items-center bg-white rounded-2xl shadow-sm hover:shadow-xl p-5 w-36 transition-all hover:-translate-y-1 border border-gray-100">
                    <div class="<?= $social['color'] ?> w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl shadow-md mb-3 group-hover:scale-110 transition">
                        <i class="<?= $social['icon'] ?>"></i>
                    </div>
                    <h3 class="font-semibold text-gray-700"><?= $social['nombre'] ?></h3>
                    <span class="text-xs text-gray-400 mt-1">Síguenos</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="text-center py-4 text-gray-400 bg-gray-50 rounded-xl">
        <i class="fas fa-share-alt mr-2"></i> Próximamente más redes sociales
    </div>
<?php endif; ?>





        
        <?php if (!empty($pdfs)): ?>
            <div class="bg-gray-50 rounded-3xl p-6 md:p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-file-alt text-indigo-500 text-4xl mb-2"></i>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Información que puede interesarte</h2>
                    <p class="text-gray-500 mt-2">Documentos útiles y catálogos digitales</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($pdfs as $pdf): ?>
                        <div class="bg-white rounded-2xl shadow-md p-5 transition-all hover:shadow-lg border border-gray-200 flex flex-col">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($pdf['titulo']) ?></h3>
                                    <div class="text-gray-600 text-sm mt-1 leading-relaxed whitespace-pre-line"><?= nl2br(htmlspecialchars($pdf['descripcion'])) ?></div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-5 pt-3 border-t border-gray-100">
                                <a href="pdf/<?= htmlspecialchars($pdf['archivo_pdf']) ?>" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium"><i class="fas fa-eye"></i> Ver PDF</a>
                                <a href="pdf/<?= htmlspecialchars($pdf['archivo_pdf']) ?>" download class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-900 text-sm font-medium"><i class="fas fa-download"></i> Descargar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400 bg-gray-50 p-6 rounded-xl">No hay documentos disponibles temporalmente.</p>
        <?php endif; ?>

        <!-- FORMULARIO DE CONTACTO moderno (con estilos profesionales, manteniendo estructura original pero con tailwind) -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-8 md:px-10">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 text-center">¡Envíe su Consulta Aquí!</h2>
                <p class="text-center text-gray-500 mt-1">Respondemos a la brevedad</p>
            </div>
            <div class="p-6 md:p-10">
                <form action="https://lkit.engeni.com/public/events" method="post" id="form:112" class="space-y-5">
                    <input type="hidden" name="form_configuration_id" value="112">
                    <input type="hidden" name="form_type" value="EMAIL">
                    <input type="hidden" name="recipient" value="">
                    
                    <!-- Alertas de éxito / error -->
                    <div landkit-form-alert-success class="alert alert-success visually-hidden hidden bg-green-100 text-green-700 p-3 rounded-lg" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> ¡Muchas gracias! Nos pondremos en contacto a la brevedad.
                    </div>
                    <div landkit-form-alert-error class="alert alert-danger visually-hidden hidden bg-red-100 text-red-700 p-3 rounded-lg" role="alert"></div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="nombre" class="form-label block text-sm font-semibold text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition" id="nombre" name="name" placeholder="Ej: María López" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono" class="form-label block text-sm font-semibold text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                            <input type="tel" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-300" id="telefono" name="phone" placeholder="Número de contacto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label block text-sm font-semibold text-gray-700 mb-1">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-300" id="email" name="email" placeholder="tu@ejemplo.com" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje" class="form-label block text-sm font-semibold text-gray-700 mb-1">Mensaje / Consulta <span class="text-red-500">*</span></label>
                        <textarea class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-300" id="mensaje" name="message" rows="4" placeholder="Escribe tu consulta aquí..." required></textarea>
                    </div>
                    <div class="text-center pt-4">
                        <button type="button" class="btn-pill bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-full inline-flex items-center gap-2 shadow-md transition-all" landkit-form-target="form:112">
                            <small class="spinner-border spinner-border-sm visually-hidden hidden"></small>
                            Enviar mensaje
                            <i class="fas fa-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    
    </main>



<?php include 'public/foot.php'; ?>





<?php if (count($portadas) > 0): ?>
<script>
    // Configuración del carrusel
    const track = document.getElementById('carouselTrack');
    const slides = Array.from(document.querySelectorAll('.carousel-slide'));
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    const totalRealSlides = <?php echo count($portadas); ?>;
    const totalSlides = slides.length;
    
    let currentIndex = 0;
    let autoScrollInterval;
    let isTransitioning = false;
    
    // Configurar el ancho de cada slide
    function setSlideWidth() {
        const slideWidth = window.innerWidth;
        slides.forEach(slide => {
            slide.style.flex = `0 0 ${slideWidth}px`;
        });
        updateTrackPosition(false);
    }
    
    // Actualizar posición del track
    function updateTrackPosition(animate = true) {
        if (animate) {
            track.style.transition = 'transform 0.5s ease-in-out';
        } else {
            track.style.transition = 'none';
        }
        
        const slideWidth = window.innerWidth;
        track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    }
    
    // Actualizar dots activos
    function updateDots() {
        let realIndex = currentIndex % totalRealSlides;
        dots.forEach((dot, i) => {
            if (i === realIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
    
    // Manejar el efecto infinito
    function handleInfiniteScroll() {
        if (currentIndex >= totalRealSlides) {
            setTimeout(() => {
                track.style.transition = 'none';
                currentIndex = 0;
                const slideWidth = window.innerWidth;
                track.style.transform = `translateX(0px)`;
                
                // Forzar reflow
                track.offsetHeight;
                
                // Restaurar transición
                setTimeout(() => {
                    track.style.transition = 'transform 0.5s ease-in-out';
                }, 50);
            }, 500);
        }
        else if (currentIndex < 0) {
            setTimeout(() => {
                track.style.transition = 'none';
                currentIndex = totalRealSlides - 1;
                const slideWidth = window.innerWidth;
                track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
                
                // Forzar reflow
                track.offsetHeight;
                
                // Restaurar transición
                setTimeout(() => {
                    track.style.transition = 'transform 0.5s ease-in-out';
                }, 50);
            }, 500);
        }
    }
    
    // Siguiente slide
    function nextSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex++;
        updateTrackPosition(true);
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
            handleInfiniteScroll();
        }, 500);
        
        resetAutoScroll();
    }
    
    // Slide anterior
    function prevSlide() {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex--;
        updateTrackPosition(true);
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
            handleInfiniteScroll();
        }, 500);
        
        resetAutoScroll();
    }
    
    // Ir a un slide específico
    function goToSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        currentIndex = index;
        updateTrackPosition(true);
        updateDots();
        
        setTimeout(() => {
            isTransitioning = false;
            handleInfiniteScroll();
        }, 500);
        
        resetAutoScroll();
    }
    
    // Auto-scroll
    function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
            nextSlide();
        }, 5000);
    }
    
    function resetAutoScroll() {
        clearInterval(autoScrollInterval);
        startAutoScroll();
    }
    
    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }
    
    // Event listeners
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);
    }
    
    // Event listeners para dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            goToSlide(index);
        });
    });
    
    // Pausar auto-scroll al pasar el mouse
    const carouselContainer = document.querySelector('.carousel-container');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', stopAutoScroll);
        carouselContainer.addEventListener('mouseleave', startAutoScroll);
    }
    
    // Ajustar al cambiar el tamaño de la ventana
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            setSlideWidth();
            updateTrackPosition(false);
            handleInfiniteScroll();
        }, 250);
    });
    
    // Inicializar
    setSlideWidth();
    startAutoScroll();
    
    // Soporte para teclado
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
</script>
<?php endif; ?>

