<?php
  $base_url = ''; 
  $base_url2 = '../'; 
  $title = 'Mi Carrito';
  include 'head.php';

  require '../includes/conexion.php';

  // Obtener datos de la tienda
  $query_datos = "SELECT celular_fabrica FROM datos LIMIT 1";
  $result_datos = mysqli_query($conexion, $query_datos);
  $celular_fabrica = ($result_datos && mysqli_num_rows($result_datos) > 0) ? mysqli_fetch_assoc($result_datos)['celular_fabrica'] : '';

  // Obtener QR de pago (titular = 'si')
  $query_qr = "SELECT imagenQR, titularCuenta, nombreBanco FROM cuentas_bancarias WHERE titular = 'si' LIMIT 1";
  $result_qr = mysqli_query($conexion, $query_qr);
  $qr_info = ($result_qr && mysqli_num_rows($result_qr) > 0) ? mysqli_fetch_assoc($result_qr) : null;
?>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Estilos personalizados */
    .cart-item {
        transition: all 0.3s ease;
    }
    .cart-item:hover {
        background: #f9fafb;
    }
    .qr-modal {
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .qr-modal.active {
        opacity: 1;
        visibility: visible;
    }
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @media (max-width: 768px) {
        .cart-table {
            font-size: 12px;
        }
        .cart-table th, .cart-table td {
            padding: 8px 4px;
        }
    }
</style>

<div class="min-h-screen bg-gray-100 py-4 md:py-8">
    <div class="container mx-auto px-3 md:px-4">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <!-- Header del carrito -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <i class="fas fa-shopping-cart text-blue-500"></i>
                    Mi Carrito de Compras
                </h1>
                <a href="tienda-virtual.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 md:px-6 py-2 rounded-lg transition flex items-center gap-2 text-sm md:text-base">
                    <i class="fas fa-arrow-left"></i>
                    Seguir comprando
                </a>
            </div>
            
            <!-- Contenedor del carrito -->
            <div id="carritoContainer"></div>
            
            <!-- Botones de acción -->
            <div id="accionesContainer" class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4" style="display: none;">
                <button id="verQrBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 md:px-6 py-2 rounded-lg transition flex items-center gap-2 text-sm md:text-base w-full sm:w-auto justify-center">
                    <i class="fas fa-qrcode"></i>
                    Ver QR de Pago
                </button>
                <button id="finalizarCompra" class="bg-green-600 hover:bg-green-700 text-white px-4 md:px-6 py-2 rounded-lg transition flex items-center gap-2 text-sm md:text-base w-full sm:w-auto justify-center">
                    <i class="fab fa-whatsapp"></i>
                    Finalizar compra
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR de Pago -->
<div id="qrModal" class="qr-modal fixed inset-0 bg-black bg-opacity-50 z-50 opacity-0 invisible transition-all duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="border-b px-4 md:px-6 py-3 md:py-4 flex justify-between items-center">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800">
                <i class="fas fa-qrcode text-purple-500 mr-2"></i>
                Código QR de Pago
            </h3>
            <button id="closeQrModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <div class="p-4 md:p-6 text-center">
            <?php if ($qr_info && $qr_info['imagenQR'] && file_exists($qr_info['imagenQR'])): ?>
                <div class="mb-4">
                    <img id="qrImageDisplay" src="<?php echo $qr_info['imagenQR']; ?>" alt="QR de Pago" class="mx-auto border rounded-lg shadow-md" style="max-width: 250px; width: 100%;">
                </div>
                <div class="text-left bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        <strong>Titular:</strong> <?php echo htmlspecialchars($qr_info['titularCuenta']); ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-university text-gray-400 mr-2"></i>
                        <strong>Banco:</strong> <?php echo htmlspecialchars($qr_info['nombreBanco']); ?>
                    </p>
                </div>
                <p class="text-xs text-gray-500 mt-4">
                    <i class="fas fa-info-circle"></i>
                    Escanea el código QR para realizar el pago
                </p>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-qrcode text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">No hay código QR disponible</p>
                    <p class="text-sm text-gray-400 mt-2">Contacta al administrador</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="border-t px-4 md:px-6 py-3 md:py-4 flex justify-end">
            <button id="downloadQrBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                <i class="fas fa-download"></i>
                Descargar QR
            </button>
        </div>
    </div>
</div>

<script>
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
let celularFabrica = "<?php echo $celular_fabrica; ?>";
let qrImage = "<?php echo $qr_info ? $qr_info['imagenQR'] : ''; ?>";

// Función para obtener la primera imagen de un producto
async function obtenerImagenProducto(codigo) {
    try {
        const response = await fetch('get_imagen_producto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'codigo=' + encodeURIComponent(codigo)
        });
        const data = await response.json();
        return data.success && data.imagen ? data.imagen : null;
    } catch (error) {
        console.error('Error al obtener imagen:', error);
        return null;
    }
}

// Actualizar carrito con imágenes
async function actualizarCarrito() {
    const container = document.getElementById('carritoContainer');
    const accionesContainer = document.getElementById('accionesContainer');
    
    if (carrito.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Tu carrito está vacío</p>
                <a href="tienda-virtual.php" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-store mr-2"></i>
                    Ir a la tienda
                </a>
            </div>
        `;
        accionesContainer.style.display = 'none';
        return;
    }
    
    accionesContainer.style.display = 'flex';
    
    // Mostrar loading
    container.innerHTML = '<div class="text-center py-12"><div class="loading-spinner"></div><p class="mt-4">Cargando productos...</p></div>';
    
    // Obtener imágenes para cada producto
    for (let item of carrito) {
        if (!item.imagen) {
            item.imagen = await obtenerImagenProducto(item.codigo);
        }
    }
    
    // Generar HTML del carrito
    let html = `
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white cart-table">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="text-left py-3 px-2 md:px-4">Producto</th>
                        <th class="text-left py-3 px-2 md:px-4">Medida</th>
                        <th class="text-center py-3 px-2 md:px-4">Cantidad</th>
                        <th class="text-right py-3 px-2 md:px-4">Precio Unit.</th>
                        <th class="text-right py-3 px-2 md:px-4">Subtotal</th>
                        <th class="text-center py-3 px-2 md:px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    let total = 0;
    for (let i = 0; i < carrito.length; i++) {
        const item = carrito[i];
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        const imagenUrl = item.imagen ? item.imagen : null;
        
        html += `
            <tr class="border-b cart-item">
                <td class="py-2 md:py-3 px-2 md:px-4">
                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="w-10 h-10 md:w-12 md:h-12 flex-shrink-0">
                            ${imagenUrl ? 
                                `<img src="${imagenUrl}" alt="${escapeHtml(item.nombre)}" class="w-full h-full object-cover rounded-lg" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-100 rounded-lg flex items-center justify-center\'><i class=\'fas fa-image text-gray-400\'></i></div>'">` : 
                                `<div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>`
                            }
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">${escapeHtml(item.nombre)}</p>
                            <p class="text-xs text-gray-500 hidden md:block">Cód: ${item.codigo}</p>
                        </div>
                    </div>
                </td>
                <td class="py-2 md:py-3 px-2 md:px-4 text-sm md:text-base">${escapeHtml(item.medida)}</td>
                <td class="py-2 md:py-3 px-2 md:px-4 text-center">
                    <div class="flex items-center justify-center gap-1 md:gap-2">
                        <button class="cantidad-btn bg-gray-200 hover:bg-gray-300 w-6 h-6 md:w-8 md:h-8 rounded flex items-center justify-center" data-index="${i}" data-cambio="-1">-</button>
                        <span class="w-8 md:w-12 text-center text-sm md:text-base">${item.cantidad}</span>
                        <button class="cantidad-btn bg-gray-200 hover:bg-gray-300 w-6 h-6 md:w-8 md:h-8 rounded flex items-center justify-center" data-index="${i}" data-cambio="1">+</button>
                    </div>
                </td>
                <td class="py-2 md:py-3 px-2 md:px-4 text-right text-sm md:text-base">Bs ${item.precio.toFixed(2)}</td>
                <td class="py-2 md:py-3 px-2 md:px-4 text-right font-semibold text-sm md:text-base">Bs ${subtotal.toFixed(2)}</td>
                <td class="py-2 md:py-3 px-2 md:px-4 text-center">
                    <button class="eliminar-btn bg-red-500 hover:bg-red-600 text-white w-7 h-7 md:w-8 md:h-8 rounded flex items-center justify-center mx-auto" data-index="${i}" title="Eliminar">
                        <i class="fas fa-trash text-xs md:text-sm"></i>
                    </button>
                </td>
             </tr>
        `;
    }
    
    html += `
                </tbody>
                <tfoot class="border-t-2">
                    <tr class="bg-gray-50">
                        <td colspan="4" class="py-3 md:py-4 px-2 md:px-4 text-right font-bold text-sm md:text-base">Total:</td>
                        <td class="py-3 md:py-4 px-2 md:px-4 text-right font-bold text-xl md:text-2xl text-blue-600">Bs ${total.toFixed(2)}</td>
                        <td class="py-3 md:py-4 px-2 md:px-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Event listeners para botones de cantidad
    document.querySelectorAll('.cantidad-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const index = parseInt(btn.dataset.index);
            const cambio = parseInt(btn.dataset.cambio);
            const nuevaCantidad = carrito[index].cantidad + cambio;
            
            if (nuevaCantidad > 0) {
                carrito[index].cantidad = nuevaCantidad;
            } else {
                carrito.splice(index, 1);
            }
            
            localStorage.setItem('carrito', JSON.stringify(carrito));
            actualizarCarrito();
        });
    });
    
    // Event listeners para botones de eliminar
    document.querySelectorAll('.eliminar-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const index = parseInt(btn.dataset.index);
            carrito.splice(index, 1);
            localStorage.setItem('carrito', JSON.stringify(carrito));
            actualizarCarrito();
        });
    });
}

// Generar mensaje para WhatsApp
function generarMensajeWhatsApp() {
    let mensaje = "🛍️ *NUEVO PEDIDO - GCasaClub* 🛍️\n\n";
    mensaje += "*DETALLES DEL PEDIDO:*\n";
    mensaje += "───────────────────\n\n";
    
    let total = 0;
    carrito.forEach((item, index) => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        mensaje += `*${index + 1}. ${item.nombre}*\n`;
        mensaje += `   📏 Medida: ${item.medida}\n`;
        mensaje += `   🔢 Cantidad: ${item.cantidad}\n`;
        mensaje += `   💰 Precio unitario: Bs ${item.precio.toFixed(2)}\n`;
        mensaje += `   📊 Subtotal: Bs ${subtotal.toFixed(2)}\n\n`;
    });
    
    mensaje += "───────────────────\n";
    mensaje += `💰 *TOTAL DEL PEDIDO: Bs ${total.toFixed(2)}*\n\n`;
    mensaje += "───────────────────\n";
    mensaje += "📋 *DATOS DEL CLIENTE:*\n";
    mensaje += "Por favor, proporcione sus datos:\n";
    mensaje += "• Nombre completo:\n";
    mensaje += "• Dirección de entrega:\n";
    mensaje += "• Teléfono de contacto:\n\n";
    mensaje += "───────────────────\n";
    mensaje += "✅ *MÉTODO DE PAGO:*\n";
    mensaje += "Transferencia bancaria / QR\n\n";
    mensaje += "───────────────────\n";
    mensaje += "¡Gracias por su compra! 🙌\n";
    mensaje += "Atendemos su pedido a la brevedad.";
    
    return encodeURIComponent(mensaje);
}

// Función auxiliar para enviar pedido por WhatsApp
function enviarPedidoWhatsApp(descargarQR = false) {
    const mensaje = generarMensajeWhatsApp();
    const numeroWhatsApp = celularFabrica ? '591' + celularFabrica : '';
    
    if (!numeroWhatsApp) {
        Swal.fire('Error', 'No se pudo obtener el número de WhatsApp', 'error');
        return;
    }
    
    window.open(`https://wa.me/${numeroWhatsApp}?text=${mensaje}`, '_blank');
    
    if (descargarQR && qrImage) {
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = qrImage;
            link.download = 'qr_pago_gcasaclub.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 500);
        
        Swal.fire({
            title: '¡Proceso completado!',
            html: '✅ Pedido enviado por WhatsApp<br>📱 Código QR descargado',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    } else {
        Swal.fire({
            title: '¡Pedido enviado!',
            text: 'Hemos abierto WhatsApp. Envía el mensaje para confirmar tu pedido.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }
}

// Finalizar compra con 3 botones personalizados
function finalizarCompraTresBotones() {
    if (carrito.length === 0) {
        Swal.fire('Error', 'No hay productos en el carrito', 'error');
        return;
    }
    
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    
    Swal.fire({
        title: '¿Confirmar pedido?',
        html: `
            <div class="text-left">
                <p class="mb-2"><strong>Total del pedido:</strong></p>
                <p class="text-2xl font-bold text-green-600 mb-4">Bs ${total.toFixed(2)}</p>
                <p class="text-sm text-gray-600 mb-3">Elige cómo deseas proceder:</p>
                <div class="space-y-2">
                    <button id="btnSoloWhatsApp" class="swal2-confirm swal2-styled" style="background-color: #25D366; width: 100%; margin: 5px 0; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fab fa-whatsapp"></i> Solo enviar por WhatsApp
                    </button>
                    <button id="btnWhatsAppQR" class="swal2-confirm swal2-styled" style="background-color: #9b59b6; width: 100%; margin: 5px 0; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fab fa-whatsapp"></i> <i class="fas fa-download"></i> Enviar y descargar QR
                    </button>
                    <button id="btnCancelar" class="swal2-cancel swal2-styled" style="background-color: #d33; width: 100%; margin: 5px 0; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        Cancelar
                    </button>
                </div>
            </div>
        `,
        icon: 'question',
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: () => {
            const soloWhatsAppBtn = document.getElementById('btnSoloWhatsApp');
            const whatsAppQRBtn = document.getElementById('btnWhatsAppQR');
            const cancelarBtn = document.getElementById('btnCancelar');
            
            if (soloWhatsAppBtn) {
                soloWhatsAppBtn.addEventListener('click', () => {
                    Swal.close();
                    enviarPedidoWhatsApp(false);
                });
            }
            
            if (whatsAppQRBtn) {
                whatsAppQRBtn.addEventListener('click', () => {
                    Swal.close();
                    enviarPedidoWhatsApp(true);
                });
            }
            
            if (cancelarBtn) {
                cancelarBtn.addEventListener('click', () => {
                    Swal.close();
                });
            }
        }
    });
}

// Descargar QR desde el modal
function descargarQR() {
    const imgElement = document.getElementById('qrImageDisplay');
    if (imgElement && imgElement.src) {
        fetch(imgElement.src)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'qr_pago_gcasaclub.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Descarga exitosa',
                    text: 'El código QR se ha descargado',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo descargar el código QR', 'error');
            });
    } else if (qrImage) {
        const link = document.createElement('a');
        link.href = qrImage;
        link.download = 'qr_pago_gcasaclub.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        Swal.fire({
            icon: 'success',
            title: 'Descarga iniciada',
            text: 'El código QR se está descargando',
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        Swal.fire('Error', 'No hay código QR disponible', 'error');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    actualizarCarrito();
    
    // Botón finalizar compra
    const finalizarBtn = document.getElementById('finalizarCompra');
    if (finalizarBtn) {
        finalizarBtn.addEventListener('click', finalizarCompraTresBotones);
    }
    
    // Modal QR
    const qrModal = document.getElementById('qrModal');
    const verQrBtn = document.getElementById('verQrBtn');
    const closeQrModal = document.getElementById('closeQrModal');
    const downloadQrBtn = document.getElementById('downloadQrBtn');
    
    if (verQrBtn) {
        verQrBtn.addEventListener('click', () => {
            qrModal.classList.add('active');
        });
    }
    
    if (closeQrModal) {
        closeQrModal.addEventListener('click', () => {
            qrModal.classList.remove('active');
        });
    }
    
    if (downloadQrBtn) {
        downloadQrBtn.addEventListener('click', descargarQR);
    }
    
    qrModal.addEventListener('click', (e) => {
        if (e.target === qrModal) {
            qrModal.classList.remove('active');
        }
    });
});
</script>

<?php include 'footer.php'; ?>