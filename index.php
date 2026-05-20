<?php
  $base_url = 'public/'; 
  $base_url2 = ''; 
  $title = 'GcasaClub';
  include 'public/head.php';

  require 'includes/conexion.php';

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
?>

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




<section>
    <div>
       <img src="img/categoria-hotel.avif">
       <h2>Línea Hotelera</h2>
    </div>   

    <div>
        <img src="img/hogar.avif">
        <h2>Línea Hogar</h2>
    </div> 

    <div>
        <img src="img/hospitalaria.avif">
        <h2>Línea Hospitalaria</h2>
    </div> 

    <div>
        <img src="img/ropaInstituciones.jpeg">
        <h2>Línea institucional</h2>
    </div> 
</section>




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

