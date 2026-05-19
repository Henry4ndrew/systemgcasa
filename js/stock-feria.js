
//Script del stock de ferias============================================
function editarFeria (idFeria, nombreFeria){
    plop('formFerias');

    gestionarBtns('formFerias' , 'editar');
    const titulo = document.getElementById('txt-formFerias');
    titulo.textContent = 'Editar nombre de feria';

    document.getElementById('id-feria').value = idFeria;
    document.getElementById('nombre_feria').value = nombreFeria;
    document.getElementById('btn1-formFerias').style.display = 'none';
    document.getElementById('btn2-formFerias').style.display = 'inline-block';
}
function mostrarFerias(valor) {
    const contenedor = document.getElementById("contenedor-feria");
    const selectFeria = document.getElementById("feria_select");
    if (valor === "Feria") {
        contenedor.style.display = "flex";
        selectFeria.setAttribute("required", "required");
        abrirPanel('pest2');
    } else {
        contenedor.style.display = "none";
        selectFeria.removeAttribute("required");
        selectFeria.value = "";
    }
}
function editarCantProductFeria(producto) {
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
            <input type="hidden" name="idFeria" value="${producto.id_feria}" readonly> 
            <div class="detail-lateral">${producto.detalle}</div>
        `;
    } else {
        productoSeleccionado.innerHTML = '<p class="error">Producto no encontrado</p>';
    }
        document.getElementById('cantidad-prod-actual').value = producto.cantidad;
       
}





// Función para obtener parámetros de la URL actual
function getUrlParams() {
    const params = {};
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    for (const [key, value] of urlParams) {
        params[key] = value;
    }
    return params;
}

// Función para guardar el filtro en localStorage (opcional, para persistencia)
function guardarEstadoFiltro(idFeria) {
    if (idFeria && idFeria !== 'todos') {
        localStorage.setItem('filtro_feria_seleccionada', idFeria);
    } else {
        localStorage.removeItem('filtro_feria_seleccionada');
    }
}

// Función principal de filtrado
function filtrarPorFerias(idFeria) {
    guardarEstadoFiltro(idFeria);
    // Construir los parámetros adicionales
    let parametros = '';
    if (idFeria && idFeria !== 'todos') {
        parametros = 'filtro_feria=' + idFeria;
    }
    // Recargar la página con el filtro
    if (typeof cargarPagina === 'function') {
        cargarPagina('stock_ferias.php', true, parametros);
    } else {
        // Fallback si cargarPagina no está disponible
        window.location.href = '?p=stock_ferias.php' + (parametros ? '&' + parametros : '');
    }
}

// Función para inicializar el select (solo para asegurar que coincida con la URL)
function inicializarSelectFiltro() {
    const selectFeria = document.getElementById('feria_select');
    if (!selectFeria) {
        // console.log('Select no encontrado');
        return;
    }
    // Obtener filtro de la URL
    const params = getUrlParams();
    let idFeria = params.filtro_feria || null;
    
    // console.log('ID Feria desde URL:', idFeria);
    // Si hay un valor en la URL y no es 'todos', seleccionarlo en el combobox
    if (idFeria && idFeria !== 'todos') {
        // Verificar que el valor existe en el select
        let existe = false;
        for (let i = 0; i < selectFeria.options.length; i++) {
            if (selectFeria.options[i].value == idFeria) {
                existe = true;
                break;
            }
        }
        // console.log('Existe en opciones:', existe);
        if (existe) {
            selectFeria.value = idFeria;
            console.log('Select actualizado a:', selectFeria.value);
        } else {
            // console.log('ID Feria no existe en opciones:', idFeria);
            selectFeria.value = 'todos';
        }
    } else {
        // Si no hay filtro válido en URL, asegurar que esté en 'todos'
        selectFeria.value = 'todos';
        // console.log('Select actualizado a: todos');
    }
}

// Función para aplicar filtro visual (solo como respaldo, el backend ya filtró)
function aplicarFiltroVisual(idFeria) {
    const tabla = document.getElementById('tablaStockProductos');
    if (!tabla) return;
    
    const tbody = tabla.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    
    const filas = tbody.getElementsByTagName('tr');
    
    // Si el filtro es 'todos', mostrar todas las filas
    if (idFeria === 'todos') {
        for (let i = 0; i < filas.length; i++) {
            const fila = filas[i];
            if (fila.cells.length === 1 && fila.cells[0].colSpan === 8) {
                continue;
            }
            fila.style.display = '';
        }
        return;
    }
    // Si hay un filtro específico, mostrar solo las filas que coinciden
    // (esto es un respaldo por si el backend no filtró correctamente)
    let filasVisibles = 0;
    
    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        if (fila.cells.length === 1 && fila.cells[0].colSpan === 8) {
            continue;
        }
        const celdaFeria = fila.cells[1];
        if (celdaFeria) {
            const spanOculto = celdaFeria.querySelector('span');
            if (spanOculto && spanOculto.textContent.trim() == idFeria) {
                fila.style.display = '';
                filasVisibles++;
            } else {
                fila.style.display = 'none';
            }
        } else {
            fila.style.display = 'none';
        }
    }
    
    // Mostrar mensaje si no hay resultados
    const existingMsg = document.getElementById('msg-sin-resultados');
    if (filasVisibles === 0 && !existingMsg) {
        const msgRow = tbody.insertRow();
        msgRow.id = 'msg-sin-resultados';
        const cell = msgRow.insertCell(0);
        cell.colSpan = 8;
        cell.textContent = idFeria === 'todos' ? 'No hay productos registrados' : 'No hay productos en esta feria';
        cell.style.textAlign = 'center';
        cell.style.padding = '20px';
        cell.style.backgroundColor = '#f5f5f5';
        cell.style.color = '#666';
    } else if (filasVisibles > 0 && existingMsg) {
        existingMsg.remove();
    }
}

// Función de búsqueda mejorada que respeta el filtro de feria
function buscar2C3C(inputId, tablaId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const filtro = input.value.toLowerCase();
    const tabla = document.getElementById(tablaId);
    if (!tabla) return;
    
    const tbody = tabla.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    
    const filas = tbody.getElementsByTagName('tr');
    const selectFeria = document.getElementById('feria_select');
    const feriaActual = selectFeria ? selectFeria.value : 'todos';
    
    let filasVisibles = 0;
    
    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        
        // Saltar fila de mensaje
        if (fila.id === 'msg-sin-resultados' || fila.id === 'msg-sin-resultados-busqueda' || 
            (fila.cells.length === 1 && fila.cells[0].colSpan === 8)) {
            continue;
        }
        
        // Verificar filtro de feria
        let coincideFeria = true;
        if (feriaActual !== 'todos') {
            const celdaFeria = fila.cells[1];
            if (celdaFeria) {
                const spanOculto = celdaFeria.querySelector('span');
                if (spanOculto && spanOculto.textContent.trim() != feriaActual) {
                    coincideFeria = false;
                }
            }
        }
        
        // Verificar búsqueda
        let coincideBusqueda = false;
        if (filtro === '') {
            coincideBusqueda = true;
        } else {
            const textoNombre = fila.cells[3]?.textContent.toLowerCase() || '';
            const textoCantidad = fila.cells[2]?.textContent.toLowerCase() || '';
            const textoCodigo = fila.cells[4]?.textContent.toLowerCase() || '';
            
            if (textoNombre.includes(filtro) || 
                textoCantidad.includes(filtro) || 
                textoCodigo.includes(filtro)) {
                coincideBusqueda = true;
            }
        }
        
        if (coincideFeria && coincideBusqueda) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    }
    
    // Mostrar mensaje si no hay resultados
    const existingMsg = document.getElementById('msg-sin-resultados-busqueda');
    if (filasVisibles === 0 && !existingMsg) {
        const msgRow = tbody.insertRow();
        msgRow.id = 'msg-sin-resultados-busqueda';
        const cell = msgRow.insertCell(0);
        cell.colSpan = 8;
        cell.textContent = 'No se encontraron productos que coincidan con "' + filtro + '"';
        cell.style.textAlign = 'center';
        cell.style.padding = '20px';
        cell.style.backgroundColor = '#f5f5f5';
        cell.style.color = '#666';
    } else if (filasVisibles > 0 && existingMsg) {
        existingMsg.remove();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // console.log('DOMContentLoaded - inicializando filtro...');
    inicializarSelectFiltro();
});

// También ejecutar inmediatamente si el contenido ya está cargado
if (document.getElementById('tablaStockProductos') && document.getElementById('feria_select')) {
    // console.log('Tabla y select ya presentes, inicializando inmediatamente...');
    inicializarSelectFiltro();
}
//FIN del Script del stock de ferias============================================