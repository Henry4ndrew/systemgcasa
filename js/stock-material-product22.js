// Funciones que se encargar de mostrar/ocultar lista flotante en form addProduct, addMaterial al almacén
function ocultarLista(idLista) {
    setTimeout(() => {
        document.getElementById(idLista).style.display = 'none';
    }, 200);
}
function mostrarLista(idLista) {
    const lista = document.getElementById(idLista);
    if(idLista === 'list-prod-details'){
       lista.style.display = 'grid';
    }else if(idLista === 'lista-materia-prima'){
       lista.style.display = 'grid';
    }else if(idLista === 'lista-materia-prima2'){
       lista.style.display = 'grid';
    } else
         {
        console.log('no se encontró un id de lista válida');
    }
}
// Usado para el caso del fumialrio de cotización
let mouseDentroLista = false;
function ocultarListaInteligente(idLista) {
    const input = document.getElementById('search-products3');
    const lista = document.getElementById(idLista);
    // Solo ocultar si el mouse NO está dentro de la lista y el input NO tiene foco
    if (!mouseDentroLista && document.activeElement !== input) {
        lista.style.display = 'none';
    }
}
function cerrarLisFlotanteProdCotiz() {
    document.getElementById('list-prod-details').style.display = 'none';
}

















// ================================MATERIAL============================================
// Del domrulario de crear / editar materia prima
function editarMaterial(material) {
    console.log(material);

    // Campos base
    document.getElementById('id_material').value = material.id_material;
    document.getElementById('codigo_material').value = material.codigo_material;
    document.getElementById('nombre_material').value = material.nombre_material;

    // Tipo de medida
    document.getElementById('tipo_medida').value = material.tipo_medida;

    // Crear inputs dinámicos
    crearInputMedida('tipo_medida', 'mostrarMaterial_medida');

    // Esperar a que el DOM inserte los inputs
    setTimeout(() => {

        const medida1 = document.querySelector('[name="medida1"]');
        const nombre_medida1 = document.querySelector('[name="nombre_medida1"]');
        const medida2 = document.querySelector('[name="medida2"]');
        const nombre_medida2 = document.querySelector('[name="nombre_medida2"]');

        if (medida1) medida1.value = material.medida1 ?? '';
        if (nombre_medida1) nombre_medida1.value = material.nombre_medida1 ?? '';
        if (medida2) medida2.value = material.medida2 ?? '';
        if (nombre_medida2) nombre_medida2.value = material.nombre_medida2 ?? '';

    }, 50);

    const areaImg = document.getElementById('areaImgMaterial');
    areaImg.innerHTML = `<img src="${material.ruta_imagen}" style="max-width:100%; max-height:100%;">`;

    plop('formMateria');
    gestionarBtns('formMateria', 'editar');
    document.getElementById('txt-formMateria').textContent = "Editar Material";
}
//usado para crear inputs de agregar medida de un material
function crearInputMedida(idCombobox, campoMostrar){
    const opcionSeleccionada = document.getElementById(idCombobox).value;
    const contenedor = document.getElementById(campoMostrar);
    contenedor.innerHTML = ''; 

    if(opcionSeleccionada === 'unidad'){
        contenedor.innerHTML = `
         <div class="elem2 column">
            <label class="f-peq f-white">Contiene:</label>
            <input type="number" min="1" name="medida1" step="1" placeholder="Cantidad" class="input pd" required>
         </div>
         <div class="elem2 column">
           <label class="f-peq f-white">Nombre de medida:</label>
            <input type="text" name="nombre_medida1" placeholder="Ej: Rollo, hoja, juego" class="input pd" required>
         </div>
        `;
    } else if(opcionSeleccionada === 'longitud'){
        contenedor.innerHTML = `
            <div class="elem2 column">
                <label class="f-peq f-white">Contiene:</label>
                <input type="number" name="medida1" min="0" step="0.01" placeholder="Dato numérico" class="input pd" required>
            </div>
            <div class="elem2 column"> 
                <label class="f-peq f-white">Nombre de medida:</label>     
                <select class="select pd" name="nombre_medida1" required>
                    <option value="m">Metros (m)</option>
                    <option value="cm">Centímetros (cm)</option>
                    <option value="mm">Milímetros (mm)</option>
                </select>
            </div>
        `;
    } else if(opcionSeleccionada === 'metro_cuadrado'){
        contenedor.innerHTML = `
         <div class="elem2 column">
    
            <div class="w100 gap05">
                <div class="mitad"> 
                  <label class="f-peq f-white">Dato numérico:</label>
                  <input type="number" name="medida1" min="0" step="0.01" placeholder="Alto" class="input pd" required>
                </div>
                <div class="mitad">        
                  <label class="f-peq f-white">Nombre de medida:</label>     
                  <select name="nombre_medida1" class="select pd" required>
                    <option value="">Seleccionar med.</option>
                    <option value="m">Metros (m)</option>
                    <option value="cm">Centímetros (cm)</option>
                    <option value="mm">Milímetros (mm)</option>
                  </select>
               </div>
            </div>
            <div class="w100 gap05">
                <div class="mitad"> 
                  <label class="f-peq f-white">Dato numérico:</label>
                  <input type="number" name="medida2"  min="0" step="0.01" placeholder="Ancho" class="input pd" required>
                </div>
                <div class="mitad">        
                  <label class="f-peq f-white">Nombre de medida:</label>     
                  <select name="nombre_medida2"  class="select pd" required>
                    <option value="">Seleccionar med.</option>
                    <option value="m">Metros (m)</option>
                    <option value="cm">Centímetros (cm)</option>
                    <option value="mm">Milímetros (mm)</option>
                  </select>
               </div>
            </div>

         </div> <!--fin elem2-->
        `;
    } else {
        console.log('No se encontró una opción seleccionada del combobox');
        contenedor.innerHTML = ''; 
    }
}











// ================================PRODUCTOS============================================
let listaProductDetails = []; 
function buscarProductDetails() {
    const searchInput = document.getElementById('search-products3');
    const searchTerm = searchInput.value.trim();
    const resultadoDiv = document.getElementById('list-prod-details');

    if (timeoutBusqueda) {
        clearTimeout(timeoutBusqueda);
    }
    if (searchTerm.length < 2) {
        resultadoDiv.innerHTML = '<p class="pad10">Ingrese al menos 2 letras o números para buscar</p>';
        return;
    }
    timeoutBusqueda = setTimeout(() => {
        resultadoDiv.innerHTML = '<div class="loading">Buscando productos...</div>';
        fetch(`functions/buscar_productos.php?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
            listaProductDetails = data.productos;
            console.log("lista de productos con detalles: ", listaProductDetails);
            listaProductosInterfaz(data.productos);  
            })
            .catch(error => {
                console.error("Error al obtener los productos:", error);
                listaProductDetails = [];
            });
    }, 300);
}


function listaProductosInterfaz(data) {
    const contenedor = document.getElementById("list-prod-details");
    contenedor.innerHTML = "";
    if (!data || data.length === 0) {
        contenedor.innerHTML = '<p class="pad10">No se encontraron productos con detalles</p>';
        return;
    }
    data.forEach((producto) => {
        if (!producto.detalles || producto.detalles.length === 0) {
            return;
        }
        // Para cada detalle del producto
        producto.detalles.forEach((detalle) => {
            const primeraImagen = producto.imagenes && producto.imagenes.length > 0 
                ? producto.imagenes[0].ruta_imagen 
                : null;
            
            const div = document.createElement("div");
            div.innerHTML = `
                <div class="producto-item" onclick="addProductDetail(${detalle.id_detalle})">
                    ${primeraImagen ? `<img src="${primeraImagen.replace('../', '')}" alt="${producto.nombre}" class="imgListFlotProduct">` : ''}
                    <div class="column">
                        <b>${producto.nombre || 'Sin nombre'}</b>
                        <p class="hora">Cod: ${producto.codigo || 'N/A'} - Precio: ${formatearDecimal(detalle.precio_unitario) || '0.00'} Bs</p>
                        <p class="f-peq"><b>Medida:</b> ${detalle.medida || 'N/A'}</p>
                        <p class="f-peq" style="line-height: 1.1;"><b>Detalle:</b> ${detalle.detalle ? detalle.detalle.replace(/\n/g, '<br>') : 'Sin detalle'}</p>
                        <p class="f-peq"><b>Stock:</b> ${detalle.cantidad_disponible || 0} unidades</p>
                    </div>
                </div>
            `;
            contenedor.appendChild(div);
        });
    });
    
    if (contenedor.children.length === 0) {
        contenedor.innerHTML = '<p class="pad10">No se pudieron mostrar los detalles de los productos</p>';
    }
}


function addProductDetail(id_detalle) {
    console.log("ID Detalle seleccionado:", id_detalle);
    // Buscar en todos los productos y sus detalles
    let productoEncontrado = null;
    let detalleEncontrado = null;
    
    for (const producto of listaProductDetails) {
        if (producto.detalles && producto.detalles.length > 0) {
            detalleEncontrado = producto.detalles.find(detalle => detalle.id_detalle == id_detalle);
            if (detalleEncontrado) {
                productoEncontrado = producto;
                break;
            }
        }
    }
    
    if (!productoEncontrado || !detalleEncontrado) {
        console.error("Detalle no encontrado con id_detalle:", id_detalle);
        return;
    }
    
    const contenedor = document.getElementById("lista-prod-agregados");
    
    // Revisar si ya existe usando data-id-detalle
    const existente = contenedor.querySelector(`.item-agregado[data-id-detalle="${id_detalle}"]`);
    if (existente) {
        const inputCantidad = existente.querySelector(".input-cantidad");
        inputCantidad.value = parseInt(inputCantidad.value) + 1;
        return;
    }
    
    // Obtener la primera imagen del producto
    const primeraImagen = productoEncontrado.imagenes && productoEncontrado.imagenes.length > 0 
        ? productoEncontrado.imagenes[0].ruta_imagen 
        : null;
    
    const div = document.createElement("div");
    div.classList.add("item-agregado");
    div.setAttribute("data-id-detalle", id_detalle);
    div.innerHTML = `
        <div class="w20 column centrar">
           ${primeraImagen ? `<img src="${primeraImagen.replace('../', '')}" alt="${productoEncontrado.nombre}" class="img-product-carro">` : ''}
        </div>

        <div class="w70 column" style="padding-left:7px;">
           <div class="column">
                <p><b>${productoEncontrado.nombre || 'Sin nombre'}</b></p>
                <p class="hora">
                    <b>Cod:</b> ${productoEncontrado.codigo || 'N/A'} - 
                    <b>Precio:</b> ${formatearDecimal(detalleEncontrado.precio_unitario)} Bs
                </p>
                <p class="f-peq"><b>Medida:</b> ${detalleEncontrado.medida || 'N/A'}</p>
                ${detalleEncontrado.detalle ? `<p class="f-peq"><b>Detalle:</b> ${detalleEncontrado.detalle}</p>` : ''}
            </div>
            <div class="info-cant-prod">
              <p><b>Cantidad existente:</b> ${detalleEncontrado.cantidad_disponible || 0}</p>
            </div>
        </div>

        <div class="w10 column centrar">
            <button type="button" class="btn-remove">
                <i class="fa-solid fa-trash eliminar-icono"></i>
            </button>

            <input type="hidden" name="codigo[]" value="${productoEncontrado.codigo}">
            <input type="hidden" name="id_detalle_producto[]" value="${id_detalle}">

            <label class="f-peq-1" style="margin-top:10px;">Cant:</label>
            <input type="text"
                   name="cantidad_producto[]"
                   class="soloInput f-center input-cantidad"
                   value="1" 
                   oninput="soloNumeros2()">
                
        </div>
    `;
    contenedor.appendChild(div);
    
    const eliminarBtn = div.querySelector(".eliminar-icono");
    eliminarBtn.addEventListener("click", () => {
        div.remove();
    });
}













function editarCantProduct(producto) {
    if (document.getElementById('formCantProduct')) {
    plop('formCantProduct');
    } else if (document.getElementById('formCantProductTienda')) {
        plop('formCantProductTienda');
    }
    const productoSeleccionado = document.getElementById('producto-seleccionado');
    
    const precioFormateado = formatearDecimal(producto.precio);
    if (producto) {

        productoSeleccionado.innerHTML = `
            <img src="${producto.ruta_imagen}" alt="${producto.nombre}" class="img-peq">
            <div class="column" style="padding-left:7px;">
                <h3>${producto.nombre || ''}</h2>
                <p class="hora"><b>Cod:</b>${producto.codigo} - <b>Precio:</b> ${precioFormateado  || 0}</p>
            </div>      
            <input type="hidden" name="codigo" value="${producto.codigo}" readonly> 
            <input type="hidden" name="idDetalle" value="${producto.id_detalle}" readonly>    

            <div class="detail-lateral">${producto.detalle}</div>
        `;
    } else {
        productoSeleccionado.innerHTML = '<p class="error">Producto no encontrado</p>';
    }
        document.getElementById('cantidad-prod-actual').value = producto.cantidad;
       
}
function filtrarProducts(texto) {
    const normalizado = texto.toLowerCase().replace(/\s+/g, "");
    if (normalizado === '') {
        listaProductosInterfaz(listaProductDetails);
        return;
    }
    const productosFiltrados = listaProductDetails.filter(producto => {
        const nombreNormalizado = (producto.nombre || '').toLowerCase().replace(/\s+/g, "");
        const codigoNormalizado = (producto.codigo || '').toLowerCase().replace(/\s+/g, "");

        return nombreNormalizado.includes(normalizado) || 
               codigoNormalizado.includes(normalizado);
    });
    listaProductosInterfaz(productosFiltrados);
}






















// ====================STOCK MATERIAL================================

let listMaterialStore = [], timeSearchMaterial = null;

function buscarMateriaPrimaAlmacen(inputId, resultId) {
    const searchInput = document.getElementById(inputId);
    const searchTerm = searchInput.value.trim();
    const resultadoDiv = document.getElementById(resultId);

    if (timeSearchMaterial) {
        clearTimeout(timeSearchMaterial);
    }
    if (searchTerm.length < 2) {
        resultadoDiv.innerHTML = '<p class="pad10">Ingrese al menos 2 letras o números para buscar</p>';
        return;
    }

    timeSearchMaterial = setTimeout(() => {
        resultadoDiv.innerHTML = '<div class="pad10">Buscando productos...</div>';

        // Mapeo de resultId a endpoints
        const endpoints = {
            'lista-materia-prima': 'get_materia_prima.php',
            'lista-materia-prima2': 'get_materia_prima_de_almacen.php'
            // Agrega más mapeos aquí si es necesario
        };

        const endpoint = endpoints[resultId] || 'get_materia_prima.php';
        
        fetch(`functions/${endpoint}?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                listMaterialStore = data.materiales;
                console.log("lista de materiales: ", listMaterialStore);
                mostrarMaterialesEnContenedor(data.materiales, resultId);
            })
            .catch(error => {
                console.error("Error al obtener los materiales:", error);
                listMaterialStore = [];
                resultadoDiv.innerHTML = '<p class="pad10">Error al buscar materiales</p>';
            });
    }, 300);
}



// Función auxiliar que encapsula el resultId
function mostrarMaterialesEnContenedor(materiales, contenedorId) {
    const contenedor = document.getElementById(contenedorId);
    contenedor.innerHTML = "";
    if (!materiales || materiales.length === 0) {
        contenedor.innerHTML = '<p class="pad10">No se encontraron materiales</p>';
        return;
    }
    materiales.forEach(item => {
        const medida1 = item.medida1 ? parseFloat(item.medida1) : 0;
        const medida2 = item.medida2 ? parseFloat(item.medida2) : 0;

        const medidaExtra = item.tipo_medida === "metro_cuadrado"
            ? `<span> x ${medida2} ${item.nombre_medida2}</span>`
            : "";

        const div = document.createElement("div");


    // Si existe una cantidad se muestra la línea de cantidad
    let cantidadFormateada = null;
    let lineaCantidad = "";

    if (item.cantidad) {
        cantidadFormateada = formatearCantidad(item.cantidad);
        lineaCantidad = cantidadFormateada !== null
            ? `<p class="f-peq">Cantidad exist. <b>${cantidadFormateada}</b></p>`
            : "";
    }


        div.innerHTML = `
            <div class="producto-item" onclick="addMaterial('${item.id_material}','${contenedorId}')">
                <img src="${item.ruta_imagen || 'img/iconGalery.avif'}" class="imgListFlotProduct" alt="imagen">
                <div class="column">
                    <b>${item.nombre_material}</b>
                    <p class="hora">Cod: ${item.codigo}</p>
                    <p class="hora">${item.tipo_medida}</p>
                    <p class="hora">
                        ${medida1} ${item.nombre_medida1}
                        ${medidaExtra}
                    </p>
                    ${lineaCantidad}
                </div>
            </div>
        `;

        contenedor.appendChild(div);
    });
}

//funcion usada al retrar un material del almacen (materia prima)
function formatearCantidad(valor) {
    if (valor === null || valor === undefined || valor === "" || isNaN(valor)) {
        return null;
    }
    return parseFloat(valor).toString();
}








function addMaterial(id, contenedorOrigenId) {
    const material = listMaterialStore.find(m => m.id_material == id);
    
    if (!material) {
        console.error(`Material con id ${id} no encontrado`);
        return;
    }
    console.log("Contenedor origen:", contenedorOrigenId);
    
    // Mapeo de contenedores origen -> destino
    const mapeoContenedores = {
        'lista-materia-prima': 'lista-materiales-agregados',
        'lista-materia-prima2': 'lista-materiales-agregados2'
    };
    
    const contenedorDestinoId = mapeoContenedores[contenedorOrigenId];
    
    if (!contenedorDestinoId) {
        console.error(`No hay mapeo para el contenedor: ${contenedorOrigenId}`);
        return;
    }
    
    const contenedor = document.getElementById(contenedorDestinoId);
    
    if (!contenedor) {
        console.error(`No se encontró el contenedor destino con ID: ${contenedorDestinoId}`);
        return;
    }

    // Revisar si ya existe
    const existente = contenedor.querySelector(`.item-agregado[data-id="${id}"]`);

    if (existente) {
        const inputCantidad = existente.querySelector(".input-cantidad");
        inputCantidad.value = parseFloat(inputCantidad.value || 0) + 1
        return;
    }

    const div = document.createElement("div");
    div.classList.add("item-agregado");
    div.setAttribute("data-id", id);

    div.innerHTML = `
        <div class="w20 centrar">
           <img src="${material.ruta_imagen || 'img/iconGalery.avif'}" alt="${material.nombre_material}" class="img-peque">
        </div>

        <div class="w60">
            <p><span class="hora">${material.codigo}</span> - <b>${material.nombre_material}</b></p>
            
            <p class="hora">
                ${limpiarCeros(material.medida1)} ${material.nombre_medida1}
                ${
                    material.tipo_medida === 'metro_cuadrado'
                    ? `<span class="dato-adicional">
                        x ${limpiarCeros(material.medida2)} ${material.nombre_medida2}
                    </span>`
                    : ''
                }
            </p>
        </div>

        <div class="w20 column centrar">
             <button type="button" class="btn-remove">
                <i class="fa-solid fa-trash eliminar-icono"></i>
            </button>
            <br>

            <label class="f-peq-1">Cantidad:</label>

            <input type="text"
                   name="cantidad[]"
                   class="soloInput f-center input-cantidad"
                   value="1" oninput="soloNumeros2()">

            <input type="hidden"
                   name="id_material[]"
                   value="${material.id_material}">
        </div>
    `;

    div.querySelector(".eliminar-icono").addEventListener("click", () => {
        div.remove();
    });

    contenedor.appendChild(div);
}






function limpiarCeros(valor) {
    return valor !== null && valor !== ''
        ? Number(valor).toString()
        : '';
}
function filtrarMateriales(texto, idLista) {
    const normalizado = texto.toLowerCase().replace(/\s+/g, "");
    if (normalizado === '') {
        mostrarMaterialesEnContenedor(listMaterialStore);
        return;
    }
    const materialesFiltrados = listMaterialStore.filter(item => {
        const nombreNormalizado = (item.nombre_material || '')
            .toLowerCase()
            .replace(/\s+/g, "");

        const codigoNormalizado = (item.codigo_material || '')
            .toLowerCase()
            .replace(/\s+/g, "");

        return nombreNormalizado.includes(normalizado) ||
               codigoNormalizado.includes(normalizado);
    });
    mostrarMaterialesEnContenedor(materialesFiltrados, idLista);
}

function editarCantMaterial(material) {
  plop('formCantMateria');

  const materialEncontrado = document.getElementById("materia-prima-seleccionada");

  if (!material) {
    materialEncontrado.innerHTML = "<p>No se encontró el material.</p>";
    return;
  }

  materialEncontrado.innerHTML = `
    <div class="itemMaterial pad10 centrar gap10">
      <img src="${material.ruta_imagen || 'img/iconGalery.avif'}" 
           alt="${material.nombre_material}" 
           class="img-peque">

      <div class="column">
        <p>
          <span class="hora">${material.codigo_material}</span>
          ${material.nombre_material}
        </p>

        <p class="hora">
          ${limpiarCeros(material.medida1)} ${material.nombre_medida1}
          ${
            material.tipo_medida === 'metro_cuadrado'
              ? `<span class="dato-adicional">
                   x ${limpiarCeros(material.medida2)} ${material.nombre_medida2}
                 </span>`
              : ''
          }
        </p>
      </div>
    </div>
  `;
  document.getElementById('materia-idAlmacen').value = material.id_almacen;
  const cantidadFormateada = limpiarCeros(material.cantidad);
  document.getElementById('cantidad-m-actual').value = cantidadFormateada;

  document.getElementById('cantidad-m-anterior').value = cantidadFormateada;
}






