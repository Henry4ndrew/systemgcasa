<?php
  $base_url = ''; 
  $base_url2 = '../'; 
  $title = 'Tienda virtual';
  include 'head.php';

  require '../includes/conexion.php';

  // Verificar conexión
  if (!$conexion) {
      die("Error de conexión: " . mysqli_connect_error());
  }
?>

<style>
    /* Tarjetas de tamaño uniforme */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 380px;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .product-img-container {
        height: 200px;
        overflow: hidden;
        flex-shrink: 0;
    }
    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-img {
        transform: scale(1.05);
    }
    .product-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1rem;
    }
    .product-title {
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-description {
        font-size: 0.875rem;
        color: #6b7280;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 1rem;
        flex: 1;
    }
    .no-image {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #9ca3af;
        font-size: 3rem;
    }
    
    /* Modal mejorado */
    .modal {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .modal.active {
        opacity: 1;
        visibility: visible;
    }
    .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }
    
    /* Slider de imágenes */
    .slider-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        border-radius: 0.5rem;
        background: #f9fafb;
    }
    .slider-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }
    .slider-slide {
        flex: 0 0 100%;
        position: relative;
        height: 350px;
    }
    .slider-slide img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #f9fafb;
    }
    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slider-btn:hover {
        background: rgba(0,0,0,0.8);
    }
    .slider-prev {
        left: 10px;
    }
    .slider-next {
        right: 10px;
    }
    .slider-dots {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 10;
    }
    .slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .slider-dot.active {
        background: white;
        width: 20px;
        border-radius: 4px;
    }
    
    /* Grid de detalles en el modal */
    .detalles-grid {
        max-height: 350px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }
    .detalles-grid::-webkit-scrollbar {
        width: 6px;
    }
    .detalles-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .detalles-grid::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .detalle-item {
        transition: all 0.3s ease;
    }
    .detalle-item:hover {
        background: #f9fafb;
        transform: translateX(5px);
    }
    
    .loading {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .slider-slide {
            height: 250px;
        }
        .detalles-grid {
            max-height: 300px;
        }
    }
</style>

<?php
// Obtener categoría seleccionada (por defecto: 'hotelera')
$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : 'hotelera';

// Mapeo de nombres para mostrar en los botones
$nombres_categorias = [
    'hotelera' => 'Línea Hotelera',
    'hogar' => 'Línea Hogar',
    'hospitalaria' => 'Línea Hospitalaria',
    'institucional' => 'Línea Institucional',
    'otros' => 'Otros'
];

// Obtener productos según categoría y tienda_virtual = 'si'
$query = "SELECT codigo, nombre, categoria, caracteristicas, tienda_virtual 
          FROM lista_productos 
          WHERE categoria = '$categoria_seleccionada' AND tienda_virtual = 'si' 
          ORDER BY nombre ASC";

$result = mysqli_query($conexion, $query);

$productos = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Obtener la primera imagen de cada producto
        $img_query = "SELECT ruta_imagen FROM imagenes WHERE codigo = '{$row['codigo']}' LIMIT 1";
        $img_result = mysqli_query($conexion, $img_query);
        $row['imagen'] = ($img_result && mysqli_num_rows($img_result) > 0) ? mysqli_fetch_assoc($img_result)['ruta_imagen'] : null;
        $productos[] = $row;
    }
}
?>

<div class="min-h-screen bg-gray-100">
    <!-- Header con carrito -->
    <div class="bg-white shadow-md sticky top-0 z-30 bg-orange-meta">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-white">Tienda Virtual</h1>
            </div>
        </div>
    </div>

<button id="cartBtn" class="cart-float">
    <i class="fas fa-shopping-cart"></i>
    <span id="cartCount" class="cart-badge">0</span>
</button>
<style>
  .cart-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    
    width: 60px;
    height: 60px;
    
    background: var(--blue-meta);
    color: white;
    
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    cursor: pointer;
    
    transition: all 0.3s ease;
    z-index: 9999;
}
.cart-float i{
  font-size:1.7rem;
}
.cart-float:hover {
    transform: scale(1.1);
}

/* contador */
.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    
    background: #ef4444;
    color: white;
    
    width: 20px;
    height: 20px;
    
    border-radius: 50%;
    font-size: 15px;
    
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>





    <div class="container mx-auto px-4 py-8">
        <!-- Botones de filtrado de categorías -->
        <div class="flex flex-wrap justify-center gap-2 md:gap-3 mb-8">
            <?php
            $categorias_reales = ['hotelera', 'hogar', 'hospitalaria', 'institucional', 'otros'];
            foreach ($categorias_reales as $cat):
                $activa = ($categoria_seleccionada == $cat) ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100';
                $nombre_mostrar = isset($nombres_categorias[$cat]) ? $nombres_categorias[$cat] : ucfirst($cat);
            ?>
                <button class="categoria-btn <?php echo $activa; ?> px-4 md:px-6 py-2 rounded-full font-semibold transition duration-300 shadow-md text-sm md:text-base" data-categoria="<?php echo $cat; ?>">
                    <?php echo $nombre_mostrar; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Contenedor de productos - Grid uniforme -->
        <div id="productosContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if (count($productos) > 0): ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden" data-codigo="<?php echo $producto['codigo']; ?>">
                        <!-- Imagen del producto -->
                        <div class="product-img-container">
                            <?php if ($producto['imagen'] && file_exists($producto['imagen'])): ?>
                                <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-img">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image fa-4x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-content">
                            <h3 class="product-title text-gray-800"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars(substr($producto['caracteristicas'], 0, 80)); ?></p>
                            <div class="mt-auto">
                                <button class="ver-detalles-btn w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition duration-300 text-sm">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay productos disponibles en <?php echo $nombres_categorias[$categoria_seleccionada] ?? $categoria_seleccionada; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de detalles del producto - Mejorado con dos columnas balanceadas -->
<div id="productModal" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 opacity-0 invisible transition-all duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-6xl w-full modal-content">
        <div class="sticky top-0 bg-white border-b rounded-t-lg px-4 md:px-6 py-3 md:py-4 flex justify-between items-center">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800" id="modalTitulo"></h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <div class="p-4 md:p-6">
            <div id="detallesContainer"></div>
        </div>
    </div>
</div>

<script>
// Carrito de compras
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

// Actualizar contador del carrito
function actualizarCarritoCount() {
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = totalItems;
    }
    localStorage.setItem('carrito', JSON.stringify(carrito));
}





















// Agregar al carrito
// Agregar al carrito - VERSIÓN MODIFICADA
function agregarAlCarrito(producto, detalle) {
    const existingIndex = carrito.findIndex(item => 
        item.codigo === producto.codigo && 
        item.id_detalle === detalle.id_detalle
    );
    
    if (existingIndex !== -1) {
        carrito[existingIndex].cantidad++;
        Swal.fire({
            icon: 'success',
            title: 'Cantidad actualizada',
            text: `${producto.nombre} - ${detalle.medida}: ${carrito[existingIndex].cantidad} unidades`,
            timer: 1500,
            showConfirmButton: false,
            background: '#fff',
            customClass: {
                popup: 'rounded-lg'
            }
        });
    } else {
        carrito.push({
            codigo: producto.codigo,
            nombre: producto.nombre,
            id_detalle: detalle.id_detalle,
            medida: detalle.medida,
            detalle: detalle.detalle,
            precio: parseFloat(detalle.precio_unitario),
            cantidad: 1
        });
        Swal.fire({
            icon: 'success',
            title: '¡Agregado!',
            text: `${producto.nombre} - ${detalle.medida} agregado al carrito`,
            timer: 1500,
            showConfirmButton: false,
            background: '#fff',
            customClass: {
                popup: 'rounded-lg'
            }
        });
    }
    actualizarCarritoCount();  // Esta función ya existe
    // *** LÍNEA AGREGADA: Notificar actualización al header ***
    if (typeof window.notificarActualizacionCarrito === 'function') {
        window.notificarActualizacionCarrito();
    }
}

// Inicializar slider de imágenes
function initImageSlider(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const track = container.querySelector('.slider-track');
    const slides = container.querySelectorAll('.slider-slide');
    const prevBtn = container.querySelector('.slider-prev');
    const nextBtn = container.querySelector('.slider-next');
    const dotsContainer = container.querySelector('.slider-dots');
    
    if (!track || slides.length <= 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (dotsContainer) dotsContainer.style.display = 'none';
        return;
    }
    
    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Crear dots
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('slider-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }
    }
    
    function updateSlider() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll('.slider-dot');
            dots.forEach((dot, i) => {
                if (i === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
    }
    
    function goToSlide(index) {
        currentIndex = index;
        updateSlider();
    }
    
    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
    }
    
    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlider();
    }
    
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
}

// Cargar detalles del producto
function cargarDetalles(codigo) {
    const modal = document.getElementById('productModal');
    const modalTitulo = document.getElementById('modalTitulo');
    const detallesContainer = document.getElementById('detallesContainer');
    
    detallesContainer.innerHTML = '<div class="flex justify-center py-8"><div class="loading"></div></div>';
    modal.classList.add('active');
    
    fetch('get_detalles_completo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'codigo=' + encodeURIComponent(codigo)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalTitulo.textContent = data.producto.nombre;
            
            let html = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Columna izquierda: Slider de imágenes -->
                    <div class="slider-container" id="modalSlider">
                        <div class="slider-track">
            `;
            
            // Agregar imágenes del slider
            if (data.imagenes && data.imagenes.length > 0) {
                data.imagenes.forEach(img => {
                    let rutaImagen = img.ruta_imagen;
                    if (!rutaImagen.startsWith('http') && !rutaImagen.startsWith('/')) {
                        rutaImagen = rutaImagen;
                    }
                    html += `
                        <div class="slider-slide">
                            <img src="${rutaImagen}" alt="Imagen del producto" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'1\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Crect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'%3E%3C/rect%3E%3Ccircle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'%3E%3C/circle%3E%3Cpolyline points=\'21 15 16 10 5 21\'%3E%3C/polyline%3E%3C/svg%3E'">
                        </div>
                    `;
                });
            } else {
                html += `
                    <div class="slider-slide">
                        <div class="no-image" style="height: 350px;">
                            <i class="fas fa-image fa-6x"></i>
                            <p class="text-sm mt-2">Sin imagen disponible</p>
                        </div>
                    </div>
                `;
            }
            
            html += `
                        </div>
                        <button class="slider-btn slider-prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="slider-btn slider-next"><i class="fas fa-chevron-right"></i></button>
                        <div class="slider-dots"></div>
                    </div>
                    
                    <!-- Columna derecha: Características y detalles -->
                    <div class="flex flex-col">
                        <div class="mb-6">
                            <h4 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-tag text-blue-500 mr-2"></i>
                                Características del Producto
                            </h4>
                            <p class="text-gray-600 text-sm md:text-base leading-relaxed">${escapeHtml(data.producto.caracteristicas)}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 flex items-center sticky top-0 bg-white py-2">
                                <i class="fas fa-boxes text-green-500 mr-2"></i>
                                Presentaciones y Precios
                                <span class="ml-2 text-sm font-normal text-gray-500">(${data.detalles.length} opciones)</span>
                            </h4>
                            <div class="detalles-grid space-y-3">
            `;
            
            data.detalles.forEach((detalle, index) => {
                html += `
                    <div class="detalle-item border rounded-lg p-3 md:p-4 hover:shadow-md transition-all">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex-1">
                                <h5 class="font-semibold text-gray-800 text-sm md:text-base">${detalle.medida}</h5>
                                <p class="text-gray-500 text-xs md:text-sm">${detalle.detalle}</p>
                            </div>
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                <p class="text-xl md:text-2xl font-bold text-blue-600">Bs ${parseFloat(detalle.precio_unitario).toFixed(2)}</p>
                                <button class="agregar-carrito-btn bg-green-500 hover:bg-green-600 text-white px-3 md:px-4 py-2 rounded-lg transition duration-300 text-sm whitespace-nowrap"
                                        data-codigo="${data.producto.codigo}"
                                        data-nombre="${data.producto.nombre}"
                                        data-id-detalle="${detalle.id_detalle}"
                                        data-medida="${detalle.medida}"
                                        data-detalle="${detalle.detalle}"
                                        data-precio="${detalle.precio_unitario}">
                                    <i class="fas fa-cart-plus mr-1"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            detallesContainer.innerHTML = html;
            
            // Inicializar el slider después de que el DOM esté listo
            setTimeout(() => {
                initImageSlider('modalSlider');
            }, 100);
            
            // Agregar event listeners a los botones de agregar al carrito
            document.querySelectorAll('.agregar-carrito-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const producto = {
                        codigo: btn.dataset.codigo,
                        nombre: btn.dataset.nombre
                    };
                    const detalle = {
                        id_detalle: btn.dataset.idDetalle,
                        medida: btn.dataset.medida,
                        detalle: btn.dataset.detalle,
                        precio_unitario: btn.dataset.precio
                    };
                    agregarAlCarrito(producto, detalle);
                });
            });
        } else {
            detallesContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                    <p class="text-red-500">${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        detallesContainer.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-circle text-5xl text-red-500 mb-4"></i>
                <p class="text-red-500">Error al cargar los detalles del producto</p>
            </div>
        `;
    });
}

// Filtrar por categoría
function filtrarPorCategoria(categoria) {
    const url = new URL(window.location.href);
    url.searchParams.set('categoria', categoria);
    window.history.pushState({}, '', url);
    
    document.querySelectorAll('.categoria-btn').forEach(btn => {
        if (btn.dataset.categoria === categoria) {
            btn.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-100');
            btn.classList.add('bg-blue-600', 'text-white');
        } else {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-100');
        }
    });
    
    const container = document.getElementById('productosContainer');
    container.innerHTML = '<div class="col-span-full flex justify-center py-12"><div class="loading"></div></div>';
    
    fetch('get_productos_por_categoria.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'categoria=' + encodeURIComponent(categoria)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.productos.length > 0) {
            let html = '';
            data.productos.forEach(producto => {
                html += `
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden" data-codigo="${producto.codigo}">
                        <div class="product-img-container">
                            ${producto.imagen ? `
                                <img src="${producto.imagen}" alt="${escapeHtml(producto.nombre)}" class="product-img" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'1\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Crect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'%3E%3C/rect%3E%3Ccircle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'%3E%3C/circle%3E%3Cpolyline points=\'21 15 16 10 5 21\'%3E%3C/polyline%3E%3C/svg%3E'">
                            ` : `
                                <div class="no-image">
                                    <i class="fas fa-image fa-4x"></i>
                                </div>
                            `}
                        </div>
                        <div class="product-content">
                            <h3 class="product-title text-gray-800">${escapeHtml(producto.nombre)}</h3>
                            <p class="product-description">${escapeHtml(producto.caracteristicas.substring(0, 80))}</p>
                            <div class="mt-auto">
                                <button class="ver-detalles-btn w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition duration-300 text-sm">
                                    Ver detalles <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
            
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (!e.target.classList.contains('ver-detalles-btn')) {
                        const codigo = card.dataset.codigo;
                        cargarDetalles(codigo);
                    }
                });
                
                const verBtn = card.querySelector('.ver-detalles-btn');
                if (verBtn) {
                    verBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const codigo = card.dataset.codigo;
                        cargarDetalles(codigo);
                    });
                }
            });
        } else {
            container.innerHTML = `<div class="col-span-full text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 text-lg">No hay productos disponibles en esta categoría</p>
            </div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-red-500 text-lg">Error al cargar los productos</p></div>';
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.categoria-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const categoria = btn.dataset.categoria;
            filtrarPorCategoria(categoria);
        });
    });
    
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.classList.contains('ver-detalles-btn')) {
                const codigo = card.dataset.codigo;
                cargarDetalles(codigo);
            }
        });
        
        const verBtn = card.querySelector('.ver-detalles-btn');
        if (verBtn) {
            verBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const codigo = card.dataset.codigo;
                cargarDetalles(codigo);
            });
        }
    });
    
    const modal = document.getElementById('productModal');
    const closeModal = document.getElementById('closeModal');
    
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }
    
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }
    
    const cartBtn = document.getElementById('cartBtn');
    if (cartBtn) {
        cartBtn.addEventListener('click', () => {
            window.location.href = 'carrito.php';
        });
    }
    
    actualizarCarritoCount();
});
</script>

<?php
  include 'footer.php';
?>