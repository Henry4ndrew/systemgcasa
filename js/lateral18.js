

// Función para cargar página con parámetro de paginación
function cargarPaginaConPagina(nombreArchivo, pagina) {
    // Solo agregar parámetro pagina si no es la página 1
    let parametros = '';
    if (pagina && pagina > 0) {
        parametros = 'pagina=' + pagina;
    }
    
    cargarPagina(nombreArchivo, true, parametros);
}

// Modificar la función principal para manejar la paginación
function cargarPagina(nombreArchivo, actualizarHistorial = true, parametrosAdicionales = '') {
    // Construir la URL con parámetros adicionales
    let url = 'contenido.php?p=' + nombreArchivo;
    
    // Agregar parámetros adicionales si existen
    if (parametrosAdicionales) {
        url += '&' + parametrosAdicionales;
    }
    
    fetch(url)
        .then(respuesta => {
            if (!respuesta.ok) {
                throw new Error('Error en la respuesta del servidor: ' + respuesta.status);
            }
            return respuesta.text();
        })
        .then(data => {
            // Antes de actualizar el contenido, guardar el estado actual de la página
            const estadoActual = {
                page: nombreArchivo,
                params: parametrosAdicionales
            };
            
            document.getElementById('contenido').innerHTML = data;
            
            // Lógica específica por página
            if (nombreArchivo === 'ventas_registro.php' || nombreArchivo === 'cotiz_crear.php') {
                if (typeof getAllClients === 'function') {
                    getAllClients();
                }
            }
            
            
            // Ejecutar scripts dinámicos
            ejecutarScriptsDinamicos(data);
            
            // Construir URL para historial
            let urlHistorial = '?p=' + nombreArchivo;
            if (parametrosAdicionales) {
                urlHistorial += '&' + parametrosAdicionales;
            }
            
            // Actualizar historial solo si se solicita
            if (actualizarHistorial) {
                history.pushState(estadoActual, '', urlHistorial);
            }
            
            // Siempre pintar el botón activo
            pintarBotonActivo(nombreArchivo);
            
            // Guardar en localStorage con parámetros
            localStorage.setItem('paginaActual', nombreArchivo);
            localStorage.setItem('paginaParams', parametrosAdicionales);
            localStorage.setItem('paginaData', JSON.stringify(estadoActual));
            
            console.log('Página cargada:', nombreArchivo, 'Parámetros:', parametrosAdicionales);
        })
        .catch(error => {
            console.error('Error al cargar la página:', error);
            document.getElementById('contenido').innerHTML = 
                '<div class="error">Error al cargar la página: ' + error.message + '</div>';
        });
}




function verSubmenu(subMenuId) {
    const subMenu = document.getElementById(subMenuId);
    const flecha = document.getElementById(`flecha-${subMenuId}`);
    
    // Verificar si ya está activo
    const yaEstaActivo = subMenu.classList.contains('active');
    
    // Cerrar otros submenús si no está en modo "mantener abiertos"
    if (!localStorage.getItem('mantenerSubmenus')) {
        const todosSubMenus = document.querySelectorAll(".subMenu");
        const todasFlechas = document.querySelectorAll(".menu-flecha");
        
        todosSubMenus.forEach(menu => {
            if (menu.id !== subMenuId && menu.classList.contains('active')) {
                menu.classList.remove("active");
                // Guardar que se cerró
                guardarStatusSubmenu(menu.id, false);
            }
        });
        
        todasFlechas.forEach(icono => {
            if (icono.id !== `flecha-${subMenuId}` && icono.classList.contains('girar')) {
                icono.classList.remove("girar");
            }
        });
    }
    
    // Alternar estado
    subMenu.classList.toggle('active');
    flecha.classList.toggle('girar');
    
    // Guardar estado del submenú
    guardarStatusSubmenu(subMenuId, subMenu.classList.contains('active'));
}

function pintarBotonActivo(nombreArchivo) {
    // Quitar la clase "pintado" de todos los botones
    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.classList.remove('pintado');
        const icono = btn.querySelector('i');
        if (icono) icono.classList.add('f-gold');
    });

    // Mapeo de páginas a sus botones correspondientes (sin .php)
    const mapaBotones = {
        // Dashboard
        'dashboard.php': 'dashboard',
        
        // Ventas
        'ventas_registro.php': 'ventas_registro',
        'ventas_historial.php': 'ventas_historial',
        
        // Stock/Almacén
        'stock_productos.php': 'stock_productos',
        'stock_tienda.php': 'stock_tienda',
        'stock_materiales.php': 'stock_materiales',
        'stock_materiales_historial.php': 'stock_materiales',
        
        // Cotizaciones
        'cotiz_crear.php': 'cotiz_crear',
        'cotiz_ver.php': 'cotiz_ver',
        'cotiz_editar.php': 'cotiz_crear',
        'cotiz_lista.php': 'cotiz_ver',
        
        // Cobros
        'cobros.php': 'cobros',
        'cobros_historial.php': 'cobros_historial',
        
        // Artículos
        'art_productos.php': 'art_productos',
        'art_materiales.php': 'art_materiales',
        
        // Otros
        'clientes.php': 'clientes',
        'clientes_editar.php': 'clientes',
        'usuarios.php': 'usuarios',
        
        // Configuración
        'config_options.php': 'config_options',
        'config_data_cotiz.php': 'config_data_cotiz',
        'config_accounts.php': 'config_accounts',
        
        // Página web
        'web_portada.php': 'web_portada',
        'web_pdf.php': 'web_pdf',
        'web_data.php': 'web_data'
    };

    // Obtener el ID del botón según el mapa
    const botonId = mapaBotones[nombreArchivo];
    
    if (botonId) {
        const botonActivo = document.getElementById('btn-' + botonId);
        
        if (botonActivo) {
            botonActivo.classList.add('pintado');
            const iconoActivo = botonActivo.querySelector('i');
            if (iconoActivo) iconoActivo.classList.remove('f-gold');
            
            // Expandir el menú padre si es un submenú
            expandirMenuPadre(botonActivo);
        } else {
            console.warn('Botón no encontrado para ID:', 'btn-' + botonId);
        }
    } else {
        console.warn('No hay mapeo para la página:', nombreArchivo);
    }
}

// Función auxiliar para expandir el menú padre
function expandirMenuPadre(boton) {
    // Buscar el menú padre al que pertenece este botón
    let elementoActual = boton.parentElement;
    
    while (elementoActual && !elementoActual.classList.contains('menu-nav')) {
        if (elementoActual.classList.contains('subMenu')) {
            const subMenuId = elementoActual.id;
            const botonMenuPadre = document.querySelector(`button[onclick*="${subMenuId}"]`);
            
            if (botonMenuPadre) {
                // Expandir el submenú
                elementoActual.style.display = 'block';
                
                // Rotar la flecha
                const flechaId = 'flecha-' + subMenuId;
                const flecha = document.getElementById(flechaId);
                if (flecha) {
                    flecha.classList.remove('fa-chevron-down');
                    flecha.classList.add('fa-chevron-up');
                }
            }
        }
        elementoActual = elementoActual.parentElement;
    }
}

// Guardar estado del submenú
function guardarStatusSubmenu(subMenuId, estaActivo) {
    const todosSubMenus = document.querySelectorAll(".subMenu");
    const subMenusActivos = {};

    todosSubMenus.forEach(menu => {
        // Guardar solo el que se abrió, los demás en false
        subMenusActivos[menu.id] = (menu.id === subMenuId ? estaActivo : false);
    });

    localStorage.setItem('subMenusActivos', JSON.stringify(subMenusActivos));
}

// Mostrar/ocultar barra lateral
function toggleLateral() {
    const lateral = document.querySelector('.lateral');
    const cuerpo = document.querySelector('.cuerpo');
    const btn = document.querySelector('.toggle-btn');
    const icono = btn.querySelector('i');
    
    lateral.classList.toggle('mostrado');
    cuerpo.classList.toggle('completo');
    btn.classList.toggle('mostrado');

    // Guardar estado en localStorage
    const isVisible = lateral.classList.contains('mostrado');
    localStorage.setItem('lateralVisible', isVisible.toString());

    if (isVisible) {
        icono.classList.remove('fa-chevron-right');
        icono.classList.add('fa-chevron-left');
    } else {
        icono.classList.remove('fa-chevron-left');
        icono.classList.add('fa-chevron-right');
    }
}

// Función específica para restaurar el estado de la barra lateral
function restaurarEstadoLateral() {
    const lateralVisible = localStorage.getItem('lateralVisible');
    const lateral = document.querySelector('.lateral');
    const cuerpo = document.querySelector('.cuerpo');
    const btn = document.querySelector('.toggle-btn');
    
    if (lateral && cuerpo && btn) {
        // Si no existe en localStorage, mostrar por defecto (true)
        const mostrarLateral = lateralVisible === null ? true : lateralVisible === 'true';
        
        if (mostrarLateral) {
            lateral.classList.add('mostrado');
            cuerpo.classList.add('completo');
            btn.classList.add('mostrado');
        } else {
            lateral.classList.remove('mostrado');
            cuerpo.classList.remove('completo');
            btn.classList.remove('mostrado');
        }
        
        // Actualizar icono según el estado
        const icono = btn.querySelector('i');
        if (icono) {
            if (mostrarLateral) {
                icono.classList.remove('fa-chevron-right');
                icono.classList.add('fa-chevron-left');
            } else {
                icono.classList.remove('fa-chevron-left');
                icono.classList.add('fa-chevron-right');
            }
        }
    }
}

// Función para ejecutar scripts dinámicos
function ejecutarScriptsDinamicos(contenido) {
    const temporal = document.createElement('div');
    temporal.innerHTML = contenido;
    
    // Buscar y ejecutar scripts
    const scripts = temporal.querySelectorAll('script');
    scripts.forEach(script => {
        const nuevoScript = document.createElement('script');
        if (script.src) {
            nuevoScript.src = script.src;
        } else {
            nuevoScript.textContent = script.textContent;
        }
        document.body.appendChild(nuevoScript);
        document.body.removeChild(nuevoScript);
    });
}

// Limpiar localStorage (opcional, para debugging)
function limpiarEstado() {
    localStorage.removeItem('lateralVisible');
    localStorage.removeItem('paginaActual');
    localStorage.removeItem('botonActivo');
    localStorage.removeItem('subMenusActivos');
}





















window.addEventListener('popstate', function(event) {
    console.log('popstate fired:', event.state);
    
    if (event.state) {
        const { page, params } = event.state;
        if (page) {
            // Cargar la página sin actualizar el historial (ya está en el historial)
            cargarPagina(page, false, params);
        }
    } else {
        // Si no hay estado, cargar desde la URL actual
        const urlParams = new URLSearchParams(window.location.search);
        const paginaURL = urlParams.get('p');
        const paginaParam = urlParams.get('pagina');
        
        let parametros = '';
        if (paginaParam) {
            parametros = 'pagina=' + paginaParam;
        }
        
        if (paginaURL) {
            cargarPagina(paginaURL, false, parametros);
        }
    }
});

// Modificar la inicialización para cargar con parámetros de paginación
window.addEventListener('load', function() {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const paginaURL = urlParams.get('p');
    const paginaParam = urlParams.get('pagina');
    
    // Obtener del localStorage
    const paginaLocalStorage = localStorage.getItem('paginaActual');
    const paramsLocalStorage = localStorage.getItem('paginaParams');
    
    let paginaACargar = paginaURL || paginaLocalStorage || 'dashboard.php';
    let parametros = '';
    
    // Priorizar parámetros de la URL sobre localStorage
    if (paginaParam) {
        parametros = 'pagina=' + paginaParam;
    } else if (paramsLocalStorage && !paginaParam) {
        parametros = paramsLocalStorage;
    }
    
    // Cargar la página con parámetros
    cargarPagina(paginaACargar, false, parametros);
    
    // Restaurar estado de la barra lateral
    restaurarEstadoLateral();
    
    // Restaurar submenús activos
    const subMenusActivos = JSON.parse(localStorage.getItem('subMenusActivos') || '{}');
    Object.keys(subMenusActivos).forEach(subMenuId => {
        if (subMenusActivos[subMenuId]) {
            const subMenu = document.getElementById(subMenuId);
            const flecha = document.getElementById(`flecha-${subMenuId}`);
            if (subMenu && flecha) {
                subMenu.classList.add('active');
                flecha.classList.add('girar');
            }
        }
    });
    
    // Manejar enlaces del menú
    document.querySelectorAll('.btn-menu').forEach(boton => {
        boton.addEventListener('click', function(e) {
            if (this.getAttribute('onclick') && this.getAttribute('onclick').includes('verSubmenu')) {
                return;
            }
            e.preventDefault();
            const pagina = this.getAttribute('data-page');
            if (pagina) {
                // Al hacer clic en un botón del menú, cargar sin parámetros de paginación
                cargarPagina(pagina + '.php');
            }
        });
    });
});

// También mantener el event listener para DOMContentLoaded para otras inicializaciones
document.addEventListener('DOMContentLoaded', function() {
    // Inicializaciones rápidas que no dependen de contenido cargado
    if (typeof initFiltros === 'function') {
        initFiltros();
    }
    if (typeof initTablas === 'function') {
        initTablas();
    }
});