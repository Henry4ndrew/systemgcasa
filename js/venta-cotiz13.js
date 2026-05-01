function restablecerCaracteristica(btn) {
    const card = btn.closest('.producto-card');
    const textarea = card.querySelector('textarea');
    const valorOriginal = textarea.dataset.original;
    textarea.value = valorOriginal;
}
function mostrarFormAcodeonExistente(){
  const formVentas = document.getElementById('formAcordeonVentas');
  const formCotizacion = document.getElementById('formAcordeonCotiz');
  const id = formVentas ? 'formAcordeonVentas' : 
           formCotizacion ? 'formAcordeonCotiz' : null;
 if (id) plop(id);
}


// ============================================================
// SCANNER DE CÓDIGO DE BARRAS via COM Port (Web Serial API)
// ============================================================

let _serialPort = null;
let _serialReader = null;
let _scannerProcesando = false;
let _serialReadLoop = false;

// Limpiar el puerto serial al salir o cambiar de página
window.addEventListener('beforeunload', () => {
    if (_serialPort) {
        _serialReadLoop = false;
        try {
            if (_serialReader) { _serialReader.cancel().catch(() => {}); }
            _serialPort.close().catch(() => {});
        } catch(e) {}
        _serialPort = null;
        _serialReader = null;
    }
});

// Conectar al scanner via puerto COM
async function conectarScannerCOM() {
    // Verificar compatibilidad del navegador
    if (!('serial' in navigator)) {
        alert('⚠️ Tu navegador no soporta Web Serial API.\nUsa Google Chrome o Microsoft Edge.');
        return;
    }

    try {
        actualizarEstadoScannerCOM('scanning', 'Conectando...');

        // Si ya hay un puerto abierto, desconectar primero
        if (_serialPort) {
            console.log('⚠️ Puerto ya existente, cerrando antes de reconectar...');
            await desconectarScannerCOM();
        }

        // Solicitar acceso al puerto serial (el navegador muestra un selector)
        _serialPort = await navigator.serial.requestPort();

        // Verificar si el puerto ya está abierto antes de intentar abrirlo
        if (!_serialPort.readable) {
            // Abrir el puerto con configuración estándar para scanners
            await _serialPort.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: 'none',
                flowControl: 'none'
            });
        }

        console.log('✅ Scanner COM conectado:', _serialPort.getInfo());
        actualizarEstadoScannerCOM('conectado', 'Conectado - Listo para escanear');
        actualizarBotonesCOM(true);

        // Iniciar lectura continua del puerto
        _serialReadLoop = true;
        leerPuertoSerial();

    } catch (error) {
        console.error('Error al conectar scanner COM:', error);
        if (error.name === 'NotFoundError') {
            actualizarEstadoScannerCOM('error', 'No se seleccionó ningún puerto');
        } else if (error.message && error.message.includes('already open')) {
            // El puerto ya estaba abierto, intentar cerrar y reintentar
            console.log('Puerto ya abierto, intentando cerrar y reconectar...');
            try {
                if (_serialReader) {
                    await _serialReader.cancel().catch(() => {});
                    _serialReader.releaseLock();
                    _serialReader = null;
                }
                if (_serialPort) {
                    await _serialPort.close();
                }
                // Reintentar la conexión
                actualizarEstadoScannerCOM('scanning', 'Reconectando...');
                await _serialPort.open({
                    baudRate: 9600,
                    dataBits: 8,
                    stopBits: 1,
                    parity: 'none',
                    flowControl: 'none'
                });
                console.log('✅ Scanner COM reconectado');
                actualizarEstadoScannerCOM('conectado', 'Conectado - Listo para escanear');
                actualizarBotonesCOM(true);
                _serialReadLoop = true;
                leerPuertoSerial();
                return;
            } catch (retryError) {
                console.error('Error al reconectar:', retryError);
                actualizarEstadoScannerCOM('error', '✗ Error al reconectar. Recargue la página.');
                _serialPort = null;
                _serialReader = null;
            }
        } else {
            actualizarEstadoScannerCOM('error', '✗ Error: ' + error.message);
        }
        setTimeout(() => actualizarEstadoScannerCOM('desconectado', 'Desconectado'), 3000);
    }
}

// Desconectar el scanner
async function desconectarScannerCOM() {
    try {
        _serialReadLoop = false;

        if (_serialReader) {
            await _serialReader.cancel();
            _serialReader.releaseLock();
            _serialReader = null;
        }

        if (_serialPort) {
            await _serialPort.close();
            _serialPort = null;
        }

        console.log('🔌 Scanner COM desconectado');
        actualizarEstadoScannerCOM('desconectado', 'Desconectado');
        actualizarBotonesCOM(false);

    } catch (error) {
        console.error('Error al desconectar:', error);
        _serialPort = null;
        _serialReader = null;
        actualizarEstadoScannerCOM('desconectado', 'Desconectado');
        actualizarBotonesCOM(false);
    }
}

// Leer datos continuamente del puerto serial
async function leerPuertoSerial() {
    if (!_serialPort || !_serialPort.readable) return;

    const decoder = new TextDecoderStream();
    const readableStreamClosed = _serialPort.readable.pipeTo(decoder.writable);
    _serialReader = decoder.readable.getReader();

    let buffer = '';

    try {
        while (_serialReadLoop) {
            const { value, done } = await _serialReader.read();
            if (done) break;

            if (value) {
                buffer += value;

                // El scanner envía el código terminado en \r, \n, o \r\n
                let separadorIdx = buffer.search(/[\r\n]/);
                while (separadorIdx !== -1) {
                    const codigoBarra = buffer.substring(0, separadorIdx).trim();
                    // Saltar los caracteres de nueva línea
                    let nextStart = separadorIdx + 1;
                    if (buffer[separadorIdx] === '\r' && buffer[separadorIdx + 1] === '\n') {
                        nextStart = separadorIdx + 2;
                    }
                    buffer = buffer.substring(nextStart);

                    if (codigoBarra.length > 0 && !_scannerProcesando) {
                        console.log('📷 Código escaneado:', codigoBarra);
                        procesarCodigoBarraCOM(codigoBarra);
                    }

                    separadorIdx = buffer.search(/[\r\n]/);
                }
            }
        }
    } catch (error) {
        if (error.name !== 'TypeError' && _serialReadLoop) {
            console.error('Error leyendo puerto serial:', error);
            actualizarEstadoScannerCOM('error', '✗ Conexión perdida');
        }
    } finally {
        if (_serialReader) {
            try { _serialReader.releaseLock(); } catch(e) {}
        }
        try { await readableStreamClosed.catch(() => {}); } catch(e) {}
    }
}

// Procesar código de barra recibido del COM port
function procesarCodigoBarraCOM(codigoBarra) {
    if (_scannerProcesando) return;
    _scannerProcesando = true;

    actualizarEstadoScannerCOM('scanning', 'Buscando: ' + codigoBarra + '...');

    fetch(`functions/buscar_por_codigobarra.php?codigo_barra=${encodeURIComponent(codigoBarra)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.producto) {
                const producto = data.producto;
                const detalleEncontrado = producto.detalle_encontrado;

                const mensaje = `✓ ${producto.nombre} - ${detalleEncontrado.medida || ''}`;
                actualizarEstadoScannerCOM('success', mensaje);
                mostrarUltimoEscaneo(`${producto.nombre} | ${detalleEncontrado.medida || ''} | ${codigoBarra}`);

                // Asegurar que el producto esté en listaAllProducts
                const idx = listaAllProducts.findIndex(p => p.codigo === producto.codigo);
                if (idx === -1) {
                    listaAllProducts.push(producto);
                } else {
                    listaAllProducts[idx] = producto;
                }

                // Agregar al carrito
                agregarProductoPorScanner(producto.codigo, detalleEncontrado);

                setTimeout(() => {
                    actualizarEstadoScannerCOM('conectado', 'Conectado - Listo para escanear');
                }, 2000);

            } else {
                actualizarEstadoScannerCOM('error', `✗ No encontrado: ${codigoBarra}`);
                mostrarUltimoEscaneo(`⚠️ No encontrado: ${codigoBarra}`);
                setTimeout(() => {
                    actualizarEstadoScannerCOM('conectado', 'Conectado - Listo para escanear');
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error al buscar producto:', error);
            actualizarEstadoScannerCOM('error', '✗ Error de conexión');
            setTimeout(() => {
                actualizarEstadoScannerCOM('conectado', 'Conectado - Listo para escanear');
            }, 3000);
        })
        .finally(() => {
            _scannerProcesando = false;
        });
}

// Actualizar UI del scanner COM
function actualizarEstadoScannerCOM(estado, texto) {
    const panel = document.querySelector('.scanner-panel');
    const dot = document.getElementById('scannerDot');
    const textSpan = document.getElementById('scannerText');
    if (!dot || !textSpan) return;

    dot.classList.remove('conectado', 'scanning', 'success', 'error');
    if (panel) panel.classList.remove('conectado');

    switch(estado) {
        case 'conectado':
            dot.classList.add('conectado');
            if (panel) panel.classList.add('conectado');
            break;
        case 'scanning':
            dot.classList.add('scanning');
            if (panel) panel.classList.add('conectado');
            break;
        case 'success':
            dot.classList.add('success');
            if (panel) panel.classList.add('conectado');
            break;
        case 'error':
            dot.classList.add('error');
            break;
    }
    textSpan.textContent = texto;
}

function actualizarBotonesCOM(conectado) {
    const btnConectar = document.getElementById('btnConectarScanner');
    const btnDesconectar = document.getElementById('btnDesconectarScanner');
    if (btnConectar) btnConectar.style.display = conectado ? 'none' : 'flex';
    if (btnDesconectar) btnDesconectar.style.display = conectado ? 'flex' : 'none';
}

function mostrarUltimoEscaneo(texto) {
    const display = document.getElementById('lastScanDisplay');
    const textSpan = document.getElementById('lastScanText');
    if (display && textSpan) {
        textSpan.textContent = texto;
        display.style.display = 'flex';
        // Re-trigger animation
        display.style.animation = 'none';
        display.offsetHeight; // force reflow
        display.style.animation = 'fadeInScan 0.3s ease';
    }
}

function agregarProductoPorScanner(codigo, detalleEncontrado) {
    const idDetalle = detalleEncontrado.id_detalle;
    const precio = detalleEncontrado.precio_unitario;
    const descripcion = detalleEncontrado.detalle || '';
    const caracteristica = '';

    // Abrir el formulario acordeón si no está visible
    const formVentas = document.getElementById('formAcordeonVentas');
    if (formVentas && (formVentas.style.display === 'none' || formVentas.style.display === '')) {
        mostrarFormAcodeonExistente();
    }

    const contenedor = document.getElementById('lista-productos');
    if (!contenedor) return;

    const items = contenedor.querySelectorAll('.item-agregado');
    const precioFormateado = formatearDecimal(precio);

    const productoNuevo = {
        codigo: codigo,
        idDetalle: String(idDetalle),
        precio: precioFormateado,
        descripcion: descripcion,
        caracteristica: caracteristica
    };

    const productoExistente = encontrarProductoExistente(items, productoNuevo);

    if (productoExistente) {
        sumarCantProdExist(productoExistente);
    } else {
        const nuevoItem = crearItemProductoHtml(codigo, idDetalle, precio, descripcion, caracteristica);
        contenedor.appendChild(nuevoItem);
    }

    desplazarPanelList();
}

// ============================================================
// SCANNER DE CÓDIGO DE BARRAS - Fin
// ============================================================





// 1) Buscamos el producto y lo agregamos a un arreglo para acceder a sus datos
let listaAllProducts = [];
let timeoutBusqueda = null;

function buscarProducto(searchInputId, resultadoDivId) {
    const searchInput = document.getElementById(searchInputId);
    const searchTerm = searchInput.value.trim();
    const resultadoDiv = document.getElementById(resultadoDivId);
    
    if (timeoutBusqueda) {
        clearTimeout(timeoutBusqueda);
    }
    if (searchTerm.length < 2) {
        resultadoDiv.innerHTML = '<p class="no-results">Ingrese al menos 2 letras o números para buscar</p>';
        return;
    }
    
    timeoutBusqueda = setTimeout(() => {
        resultadoDiv.innerHTML = '<div class="loading">Buscando productos...</div>';
        
        fetch(`functions/buscar_productos.php?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.productos.length > 0) {
                    listaAllProducts = data.productos;
                    crearInterfazProdEncontrados(data.productos);
                    console.log("listaAllProducts actualizado:", listaAllProducts);
                } else {
                    resultadoDiv.innerHTML = `<p class="pad10">${data.message || 'No se encontraron productos'}</p>`;
                    listaAllProducts = [];
                }
            })
            .catch(error => {
                console.error("Error en la búsqueda:", error);
                resultadoDiv.innerHTML = '<p class="pad10">Error al buscar productos</p>';
                // Limpiar el arreglo en caso de error
                listaAllProducts = [];
            });
    }, 300);
}
function crearInterfazProdEncontrados(productos) {
    //Utiliará como contendor el que exista en el DOM (para saber si buscar en ventas o cotizaciones
    const contenedor = document.getElementById('resultadoBusqueda') || document.getElementById('list-prod-details');

    //lista flotante inteligente para la busqueda de productos al realizar uan cotización
    let inputProducto;
    let listaDetalles;
    if(contenedor && contenedor.id === 'list-prod-details'){
        inputProducto = document.getElementById('search-products3');
        listaDetalles = document.getElementById('list-prod-details');
    }
    if (inputProducto && listaDetalles) {
        inputProducto.addEventListener('blur', () => ocultarListaInteligente('list-prod-details'));

        listaDetalles.addEventListener('mouseenter', () => mouseDentroLista = true);
        listaDetalles.addEventListener('mouseleave', () => {
            mouseDentroLista = false;
            ocultarListaInteligente('list-prod-details');
        });
    }
    //fin de la lista inteligente


    if (!productos || productos.length === 0) {
        contenedor.innerHTML = '<div>No hay productos disponibles</div>';
        return;
    }
    let html = '';
    productos.forEach(producto => {
        const cantidadDetalles = producto.detalles ? producto.detalles.length : 0;
        const tieneDetalles = cantidadDetalles > 0;

       const caracteristicas = (producto.caracteristicas || 'Sin características');

        //obtener la primera imagen
        const primeraImagen = producto.imagenes && producto.imagenes.length > 0
        ? producto.imagenes[0].ruta_imagen
        : 'img/fondoLogin.avif';
        let rutaLimpia = primeraImagen.startsWith('../')
            ? primeraImagen.substring(3)
            : primeraImagen;

        html += `
            <div class="producto-card">
                <h3 class="f-center">${producto.nombre}</h3>
                <img src="${rutaLimpia}" alt="${producto.nombre}" class="producto-img" onclick="mostrarDetallesprod('${producto.codigo}', this);">     
                <div class="flex-between b-blank pad0-5">
                    <div class="hora f-center"><b>Categoría:</b> ${producto.categoria}</div>
                    <span class="hora">${cantidadDetalles} ${cantidadDetalles === 1 ? 'detalle disp.' : 'detalles disp.'}</span>
                </div>
                <div class="column pad10"> 
                   <div class="flex-between">
                        <div><b>Código:</b> ${producto.codigo}</div> 
                        <button type="button"
                            class="btn-load gold"
                            onclick="restablecerCaracteristica(this)">
                            <span><i class="fa-solid fa-rotate-right"></i></span>
                        </button>
                   </div>
                   <textarea rows="3"
    data-original="${caracteristicas.replace(/"/g, '&quot;')}">${caracteristicas}</textarea>
               </div>
            </div>
        `;
    });
    contenedor.innerHTML = html;
}


// 2) Al hacer click en un producto mostrar los detalles disponibles (también se obtiene la caracteristica modificada)
function mostrarDetallesprod(codigo, elementoClick) {
    plop('formDetails');

    //obtenemos el valor de la caracteristica modificada
    const card = elementoClick.closest('.producto-card');
    let caracteristicasTexto = '';
    if (card) {
        const textarea = card.querySelector('textarea');
        if (textarea) {
            caracteristicasTexto = textarea.value;
        }
    }

    const producto = listaAllProducts.find(p => p.codigo === codigo);

    const textoModificado = (caracteristicasTexto || '')
        .trim()
        .replace(/\r\n/g, '\n');
    const textoOriginal = (producto.caracteristicas || '')
        .trim()
        .replace(/\r\n/g, '\n');
     let caracteristicaFinal = '';
    if (textoModificado !== textoOriginal) {
        caracteristicaFinal = textoModificado;
    }

    const contenedor = document.getElementById('detallesEncontrados');
    
    document.getElementById('txtFormDetails').textContent = producto ? producto.nombre : '';

    if (!producto || !producto.detalles || producto.detalles.length === 0) {
        contenedor.innerHTML = producto ? 'No hay detalles' : 'No encontrado';
        return;
    }

    let html = '';
    producto.detalles.forEach(detalle => {
        html += `
           <div class="item-agregado flex-between">
                <div class="pad10 w70">
                    <div class="flex-between"><h4>${detalle.medida || ''}</h4> <div class="f-999 f-peq" style="display:flex;"><p class="pad0-5" style="background:#f0f0f0; border-right:1px solid #ccc;"><span>Fabrica: ${detalle.cantidad_disponible}</p><p class="pad0-5">Tienda: ${detalle.cantidad_tienda}<p> </div></div>
                    <textarea id="nuevoDetalle-${detalle.id_detalle}" class="input rad7 w100" rows="5">${detalle.detalle || ''}</textarea>
                    <textarea id="nuevaCaract-${detalle.id_detalle}" class="input rad7 w100 m-top" rows="3" hidden readonly>${caracteristicaFinal || ''}</textarea>
                </div>
                <div class="w25 column centrar pad-right-20">
                  <div class="centrar gap10 b-blank pad0-5"><input id="nuevoPrecio-${detalle.id_detalle}" class="soloInput f-center input-cantidad" value="${detalle.precio_unitario}" oninput="soloNumeros2()"><h3 class="f-999"> Bs</h3></div>
                  <button class="m-top btn-load verde" type="button" onclick="addListProd('${producto.codigo}','${detalle.id_detalle}')"><span class="f-grande pad20"><i class="fa-solid fa-cart-plus"></i></span></button>
                </div>
            </div>
        `;
    });
    contenedor.innerHTML = html;
}







//3) Agregamos un producto al formulario de lista de productos acordeon, si ya existe solo incrementamos la cantidad
function addListProd(codigo, idDetalle) {

 plop('formDetails');


const campoBuscador = document.getElementById('campoBuscadorProdCotiz');

if (!campoBuscador || window.getComputedStyle(campoBuscador).display === 'none') {
    mostrarFormAcodeonExistente();
}


  const idNewTextarea = document.getElementById('nuevoDetalle-' + idDetalle);
  const contenido = idNewTextarea.value;
  const nuevoPrecio = document.getElementById('nuevoPrecio-' + idDetalle).value;
  const precioFormateado = formatearDecimal(nuevoPrecio);
  const caracteristica = document.getElementById('nuevaCaract-' + idDetalle).value;
  const contenedor = document.getElementById('lista-productos');
  const items = contenedor.querySelectorAll('.item-agregado');
  // Buscar si ya existe un producto con las mismas características

  const productoNuevo = {
    codigo,
    idDetalle,
    precio: precioFormateado,
    descripcion: contenido,
    caracteristica
  };

  const productoExistente = encontrarProductoExistente(items, productoNuevo);
  
  if (productoExistente) {
    console.log('El producto SI existe');
    // Si existe, incrementar la cantidad en 1
    sumarCantProdExist(productoExistente);
  } else {
     console.log('El producto NO existe');
    // Si no existe, crear un nuevo elemento
    const nuevoItem = crearItemProductoHtml(codigo, idDetalle, nuevoPrecio, contenido, caracteristica);
    contenedor.appendChild(nuevoItem);
  }
   desplazarPanelList();
}






//4) Creamos la intefaz gráfica del producto dentro del carrito, dentro la lista del form acordeón
function crearItemProductoHtml(codigo, idDetalle, precio, descripcion, caracteristica) {
  console.log('carcteristica recibida',caracteristica)
  const div = document.createElement('div');
  // Buscar el producto en listaAllProducts
  const producto = listaAllProducts.find(prod => prod.codigo === codigo);
  // Obtener la primera imagen del producto o usar la imagen por defecto
  let imagenSrc = "img/fondoLogin.avif";
  let nombreProducto = "Producto no encontrado";
  let nombreDetalle = "Sin detalle";
  
  if (producto) {
    nombreProducto = producto.nombre || "Sin nombre";
    // Obtener la primera imagen si existe
    if (producto.imagenes && producto.imagenes.length > 0) {
      imagenSrc = producto.imagenes[0].ruta_imagen.replace(/^\.\.\//, '');
    }  
    // Buscar el detalle específico por idDetalle
    if (producto.detalles && producto.detalles.length > 0) {
      const detalle = producto.detalles.find(d => d.id_detalle == idDetalle);
      if (detalle) {
        nombreDetalle = detalle.medida || detalle.detalle || "Sin medida";
      }
    }
  }
  const precioFormateado = formatearDecimal(precio);
  div.innerHTML = `
    <div class="item-agregado flex-between">
      <div class="w20 column centrar">
        <img src="${escapeHtml(imagenSrc)}" alt="${escapeHtml(nombreProducto)}" class="img-product-carro">
      </div>
      <div class="column w60 content-list-prod" style="padding-left:7px;">
          <h4 class="f-center f-plomo">${escapeHtml(nombreProducto)}</h4>
          <div class="gap05"><b class="f-peq f-999">Medida:</b><p class="f-peq">${escapeHtml(nombreDetalle)}</p></div>
          <div class="gap05"><b class="f-peq f-999">Código:</b><input class="input-none" type="text" name="codigo[]" value="${escapeHtml(codigo)}" readonly></div>
          <input type="hidden" name="idDetalle[]" value="${escapeHtml(idDetalle)}" readonly>
          <div class="gap05"><b class="f-peq f-999">Precio:</b><input class="input-none" type="text" name="precio[]" value="${escapeHtml(precioFormateado)}" oninput="calcularSubTotal(this)" readonly></div>
          <div class="centrar">
              <div class="w60 column">
                <p class="f-peq f-999">Detalle:</p>
                <textarea class="txtArea-none" name="descripcion[]" rows="3" readonly>${escapeHtml(descripcion)}</textarea>
              </div>
              <div class="w40 column">
                <p class="f-peq f-999">Caract. modif:</p>
                <textarea class="txtArea-none" name="caracteristica[]" rows="3" readonly>${escapeHtml(caracteristica)}</textarea>
              </div>
          </div>
      </div>

        <div class="w20 column gap10 centrar">
          <button type="button" class="btn-remove" onclick="deleteProdList(this)"><i class="fa-solid fa-trash eliminar-icono"></i></button>
          <div class="pad0-5 f-peq">SubTotal: <input type="text" class="input-none" style="width:50px;" value="${precioFormateado}" name="subtotal[]" readonly> </div>
           <div>
              <button type="button" class="btn-load azul" onclick="cantidad(this, 'suma')"><span><i class="fas fa-plus"></i></span></button>
              <input type="text" name="cantidad[]" value="1" oninput="soloNumInt(this); calcularSubTotal(this)" class="soloInput f-center input-cantidad">
              <button type="button" class="btn-load azul" onclick="cantidad(this, 'resta')"><span><i class="fas fa-minus"></i></span></button>
           </div>
        </div>
    </div>
  `;
  // Inicializar el subtotal después de crear el elemento
  setTimeout(() => {
    const inputCantidad = div.querySelector('.input-cantidad');
    if (inputCantidad) {
      calcularSubTotal(inputCantidad);
    }
  }, 0);
  
  return div;
}





// 5) scripts funcoinales de los productos dentro la lista y el calculo automático del total de venta c/sin descuento
function deleteProdList(estadoBtn){
  const item = estadoBtn.closest('.item-agregado');
  if(item){
    item.remove();
  }

  calcularTotalGeneral();
  calcularTotalconDescuento();
}
//funcoines para caluclar subtotales y el total generañ
function calcularSubTotal(elemento) {
  const item = elemento.closest('.item-agregado');
  const inputPrecio = item.querySelector('input[name="precio[]"]');
  const inputCantidad = item.querySelector('.input-cantidad');
  const spanSubTotal = item.querySelector('input[name="subtotal[]"]');
  
  const precio = parseFloat(inputPrecio.value) || 0;
  const cantidad = parseInt(inputCantidad.value) || 1;
  
  const subTotal = precio * cantidad;
  spanSubTotal.value = formatearDecimal(subTotal);
  
  calcularTotalGeneral();
  calcularTotalconDescuento();
}

function cantidad(boton, tipoOperacion) {
  const item = boton.closest('.item-agregado');
  const inputCantidad = item.querySelector('.input-cantidad');
  let cantidad = parseInt(inputCantidad.value) || 1;
  
  if (tipoOperacion === 'suma') {
    cantidad += 1;
  } else if (tipoOperacion === 'resta') {
    cantidad = Math.max(1, cantidad - 1); // Mínimo 1
  }
  
  inputCantidad.value = cantidad;
  
  // Calcular el subtotal después de cambiar la cantidad
  calcularSubTotal(inputCantidad);
}


function calcularTotalGeneral() {
  const subTotales = document.querySelectorAll('input[name="subtotal[]"]');
  let totalGeneral = 0;

  subTotales.forEach(input => {
    const valor = parseFloat(input.value) || 0;
    totalGeneral += valor;
  });

  const totalGeneralElement = document.getElementById('totalGeneral');
  if (totalGeneralElement) {
    totalGeneralElement.textContent = totalGeneral.toFixed(2);
  }

  return totalGeneral;
}

function calcularTotalconDescuento() {
    const totalSinDescuento = parseFloat(document.getElementById("totalGeneral").textContent) || 0;
    const respuesta = document.getElementById("totalConDescuento");
    const tipoDeOperacion = document.getElementById("tipoDescuento").value;

    if (tipoDeOperacion === "monto") {
        const valorDescontar = parseFloat(document.getElementById("inputMonto").value) || 0;
        respuesta.value = totalSinDescuento - valorDescontar;
    } else if (tipoDeOperacion === "porcentaje") {
        let valorDescontar = parseFloat(document.getElementById("inputPorcentaje").value) || 0;
        valorDescontar = valorDescontar / 100;
        // Total final con descuento aplicado
        respuesta.value = totalSinDescuento - (totalSinDescuento * valorDescontar);
    } else {
        respuesta.value = totalSinDescuento;
    }
    //SIGNAR AL LET saldoActual = 0; su valor
        saldoActual = respuesta.value || 0;
        console.log(saldoActual);
}

// Función para recalcular todos los subtotales (útil cuando se cargan productos o se modifica un precio)
function recalcularTodosLosSubTotales() {
  const items = document.querySelectorAll('.item-agregado');
  items.forEach(item => {
    const inputCantidad = item.querySelector('.input-cantidad');
    if (inputCantidad) {
      calcularSubTotal(inputCantidad);
    }
  });
}










// Función para buscar producto existente 
function encontrarProductoExistente(items, productoBuscar) {
  for (const item of items) {
    const productoExistente = {
      codigo: item.querySelector('input[name="codigo[]"]').value,
      idDetalle: item.querySelector('input[name="idDetalle[]"]').value,
      precio: item.querySelector('input[name="precio[]"]').value,
      descripcion: item.querySelector('textarea[name="descripcion[]"]').value,
      caracteristica: item.querySelector('textarea[name="caracteristica[]"]').value
    };
    
    if (sonProductosIguales(productoExistente, productoBuscar)) {
      return item;
    }
  }
  return null;
}
function sonProductosIguales(producto1, producto2) {
  // Decodificar entidades HTML para comparar correctamente
  // (crearItemProductoHtml usa escapeHtml() al guardar, pero los datos del scanner vienen sin escapar)
  const tempDiv = document.createElement('div');
  const decodeHtml = (html) => {
    if (!html) return '';
    tempDiv.innerHTML = html;
    return tempDiv.textContent || tempDiv.innerText || '';
  };
  const normalizar = (valor) => {
    if (valor === null || valor === undefined) return '';
    return decodeHtml(valor.toString()).trim();
  };
  
  return normalizar(producto1.codigo) === normalizar(producto2.codigo) && 
         normalizar(producto1.idDetalle) === normalizar(producto2.idDetalle) && 
         normalizar(producto1.precio) === normalizar(producto2.precio) && 
         normalizar(producto1.descripcion) === normalizar(producto2.descripcion) &&
         normalizar(producto1.caracteristica) === normalizar(producto2.caracteristica);
}
// Función para incrementar cantidad si el producto ya existe
function sumarCantProdExist(item) {
  const cantidadInput = item.querySelector('input[name="cantidad[]"]');
  const cantidadActual = parseInt(cantidadInput.value);
  cantidadInput.value = cantidadActual + 1;

  calcularSubTotal(cantidadInput);
}





// Función para mostrar el form existente... usando en cotizacion y ventas
function mostrarFormAcodeonExistente(){
  const formVentas = document.getElementById('formAcordeonVentas');
  const formCotizacion = document.getElementById('formAcordeonCotiz');
  const id = formVentas ? 'formAcordeonVentas' : 
           formCotizacion ? 'formAcordeonCotiz' : null;
 if (id) plop(id);
}


// Función de utilidad para escapar HTML (importante para seguridad)
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}




// COTIZACION EDITAR

function editarCotization(idCotizacion) {
    document.getElementById('campoBuscadorProdCotiz').style.display = 'block';

    fetch("functions/get_cotizacion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id_cotizacion=${idCotizacion}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error(data.message);
            return;
        }        
        const datosCompletos = {
            cotizacion: data.cotizacion,
            cliente: data.cliente,
            venta: data.venta,
        };
        autorellenarFormularioAcordeon(datosCompletos);
        limpiarFormAcordeon();
    })
    .catch(error => {
        console.error("Error al obtener la cotización:", error);
    });
    mostrarFormAcodeonExistente();
}
function autorellenarFormularioAcordeon(datos){
   document.getElementById('txt-formAcordeonCotiz').textContent='Editar Cotización';
   gestionarBtns('formAcordeonCotiz' , 'editar');
   const venta = datos.venta;
   const cliente = datos.cliente;
   const cotizacion = datos.cotizacion;

   document.getElementById('idCotizacion').value = cotizacion.id_cotizacion;

    //manejamos los productos
    autorellenarProductos(venta);
    console.log(datos.cliente);


    //manejamos los clientes
    document.getElementById('id_cliente').value = cliente.id_cliente || '';
    document.getElementById('nameCliente').value = cliente.nombre || '';
    document.getElementById('empresaCliente').value = cliente.empresa || '';
    document.getElementById('nit').value = cliente.nit || '';
    document.getElementById('carnetCliente').value = cliente.carnet_ci || '';
    document.getElementById('departamento').value = cliente.departamento || '';
    document.getElementById('correo').value = cliente.correo || '';
    document.getElementById('celCliente').value = cliente.celular || '';
    document.getElementById('celEmpresa').value = cliente.cel_empresa || '';
    document.getElementById('detailCLienteProd').value = cliente.nota || '';


    //datos de la cotizacion
    document.getElementById('tituloCotizacion').value = cotizacion.titulo || '';
    document.getElementById('opcionesCotiz').value = cotizacion.id_dataPiePag || '';
    document.getElementById('cuenta_bancaria').value = cotizacion.cuenta_bancaria || '';
    document.getElementById('nota-cotiz').value = venta.nota || '';


    // Manejamos la fecha de caducidad
    const fechaCaducidad = cotizacion.fecha_caducidad;
    const fechaValida = fechaCaducidad && fechaCaducidad !== '0000-00-00';
    if (fechaValida) {
        document.getElementById('fechaEntregaSi2').checked = true;
        document.getElementById('fechaEntrega2').value = fechaCaducidad;
    } else {
        document.getElementById('fechaEntregaNo2').checked = true;
        document.getElementById('fechaEntrega2').value = '';
    }
    gestionarFechaEntrega2();



    // manejamos el descuento
    const totalGeneral = document.querySelector('.total-general');
    const parrafos = totalGeneral.querySelectorAll('p');

    const tipoDescuento = venta.tipo_descuento;
    const valorDescuento = venta.valor_descuento;
    if (tipoDescuento !== '') {
        document.getElementById('tipoDescuento').value= tipoDescuento;
        mostrarDesc('inputMonto','inputPorcentaje');
      if(tipoDescuento === 'monto'){
         document.getElementById('inputMonto').value= valorDescuento;
      }else if(tipoDescuento === 'porcentaje'){
         document.getElementById('inputPorcentaje').value= valorDescuento;
      }
    }else{
         //ocultamos y quitamos required al valor de los inputs de descuento
      parrafos[0].style.display = 'block';  // Oculta "Total General"
      parrafos[1].style.display = 'none';
      document.getElementById('tipoDescuento').value= '';
        const inputs = [inputMonto, inputPorcentaje];
        inputs.forEach(input => {
            input.required = false;
            input.style.display = 'none';
            input.value = '';
        });
    } 
    //fin seccion descuento
    calcularTotalGeneral();
    calcularTotalconDescuento();
}
function autorellenarProductos(datos) {  
    // Manejamos los productos
    const productos = datos.productos;
    const listaProductos = document.getElementById('lista-productos');
    
    // Limpiar contenido existente
    listaProductos.innerHTML = '';

    const limpiarRutaImagen = ruta =>
    ruta ? ruta.replace(/^(\.\.\/)+/, '') : '';
    // Generar HTML para cada producto
    productos.forEach(producto => {

        const itemHTML = `
            <div class="item-agregado flex-between">
                <div class="w20 column centrar">
                    <img src="${limpiarRutaImagen(producto.ruta_imagen)}"
                    alt="${producto.nombre}"
                    class="img-product-carro">
                </div>
                                
                <div class="column w60 content-list-prod" style="padding-left:7px;">
                    <h4 class="f-center f-plomo">${producto.nombre}</h4>
                    
                    <div class="gap05">
                        <b class="f-peq f-999">Medida:</b>
                        <p class="f-peq">${producto.medida || ''}</p>
                    </div>
                    
                    <div class="gap05">
                        <b class="f-peq f-999">Código:</b>
                        <input class="input-none" type="text" name="codigo[]" value="${producto.codigo}" readonly>
                    </div>
                    
                    <input type="hidden" name="idDetalle[]" value="${producto.id_detalle}" readonly>
                    
                    <div class="gap05">
                        <b class="f-peq f-999">Precio:</b>
                        <input class="input-none" type="text" name="precio[]" value="${producto.precio_venta}" readonly>
                    </div>
                    
                    <div class="centrar">
                        <div class="w60 column">
                            <p class="f-peq f-999">Detalle:</p>
                            <textarea class="txtArea-none" name="descripcion[]" rows="3" readonly>
${producto.detalle_final || ''}
                            </textarea>
                        </div>
                        
                        <div class="w40 column">
                            <p class="f-peq f-999">Caract. modif:</p>
                            <textarea class="txtArea-none" name="caracteristica[]" rows="3" readonly>
${producto.caracteristicas_final || ''}
                            </textarea>
                        </div>
                    </div>
                </div>
                
                <div class="w20 column gap10 centrar">
                    <button type="button" class="btn-remove" onclick="deleteProdList(this)">
                        <i class="fa-solid fa-trash eliminar-icono"></i>
                    </button>
                    
                    <div class="pad0-5 f-peq">
                        SubTotal:
                        <input class="input-none" style="width:50px;" value="${producto.sub_total}" name="subtotal[]" readonly>
                    </div>
                    
                    <div>
                        <button type="button" class="btn-load azul" onclick="cantidad(this, 'suma')">
                            <span><i class="fas fa-plus"></i></span>
                        </button>
                        
                        <input type="text" name="cantidad[]" value="${producto.cantidad}" oninput="soloNumInt(this); calcularSubTotal(this)" class="soloInput f-center input-cantidad">

                        <button type="button" class="btn-load azul" onclick="cantidad(this, 'resta')">
                            <span><i class="fas fa-minus"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        listaProductos.innerHTML += itemHTML;
    });
}

// funciones para aprobar una cotización
function aprobarCotization(idCotizacion) {
    fetch("functions/get_cotizacion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id_cotizacion=${idCotizacion}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error(data.message);
            return;
        }        
        const datosCompletos = {
            cotizacion: data.cotizacion,
            cliente: data.cliente,
            venta: data.venta,
        };
         autorellenarFormAprobarCotiz(datosCompletos);
    })
    .catch(error => {
        console.error("Error al obtener la cotización:", error);
    });
    plop('formAprobarCotiz');
}

function autorellenarFormAprobarCotiz(datos){
   const venta = datos.venta;
   const cotizacion = datos.cotizacion;
   console.log(datos.venta);
   console.log(datos.cotizacion);

   const saldoFormateado = formatearDecimal(venta.total_venta);
   document.getElementById('saldo-cobro').value = saldoFormateado;

   saldoActual = parseFloat(venta.total_venta);

   document.getElementById('idVenta-aprobarCotiz').value= venta.id_venta;
   
document.querySelectorAll('input[name="id_venta"]').forEach(i => {
  console.log(i.id, i.value);
});

  //Asignación de textos referentes
   document.getElementById('txt-formAprobarCotiz').textContent = cotizacion.id_cotizacion + ' - ' + cotizacion.titulo;
   const totalGral = formatearDecimal(venta.total_venta);
   document.getElementById('totalVenta-aprCotiz').textContent = totalGral;

   gestionarCampoFecha(saldoActual);
   actualizarCambio(0);
}

// Función para filtrar las cotizaciones aprobadas
function filtrarAprobadas() {
    const filtro = document.getElementById("filtro-aprobado").value;
    const tabla = document.getElementById("tabla-cotizaciones");
    const filas = tabla.querySelectorAll("tbody tr");
    filas.forEach(fila => {
        const aprobadoTexto = fila.cells[2].innerText.trim().toLowerCase();
        let aprobado = "no";
        if (aprobadoTexto.includes("si") || aprobadoTexto.includes("1")) {
            aprobado = "si";
        }

        if (filtro === "todos") {
            fila.style.display = "";
        } 
        else if (filtro === aprobado) {
            fila.style.display = "";
        } 
        else {
            fila.style.display = "none";
        }
    });
}

function descargarPdf(idCotizacion) {
    const id = encodeURIComponent(idCotizacion);
    const origin = window.location.origin;
    const pathName = window.location.pathname;
    let pdfUrl = '';

    if (pathName.includes('/descarga/system.gcasaclub.com/')) {
        pdfUrl = `${origin}/descarga/system.gcasaclub.com/functions/crearPdfCotizacion.php?id=${id}`;
    } else if (pathName.includes('/system.gcasaclub.com/')) {
        pdfUrl = `${origin}/system.gcasaclub.com/functions/crearPdfCotizacion.php?id=${id}`;
    } else {
        pdfUrl = new URL(`functions/crearPdfCotizacion.php?id=${id}`, window.location.href).toString();
    }

    const nuevaPestana = window.open(pdfUrl, '_blank');

    if (!nuevaPestana) {
        // Fallback when popup blocker prevents opening a new tab.
        window.location.href = pdfUrl;
    }
}