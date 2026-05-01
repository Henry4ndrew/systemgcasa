/*
  Script generales
  Script para mostrar formularios en general mostrarForm
  Script del buscador de clientes
  Script del fomulario acordeon
*/

function previsualizarImagen(inputId, labelId) {
  let input = document.getElementById(inputId);
  let label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            label.innerHTML = `<img src="${e.target.result}" alt="Imagen seleccionada" class="imgSeleccionada">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// limpiarPrevisualizacion de una imagen
function limpiarImagen(inputId, labelId) {
    let label = document.getElementById(labelId);
    let input = document.getElementById(inputId);
    label.innerHTML = `
        <i class="fa-solid fa-image"></i>Seleccionar imagen`;
    input.value = "";
}


//onsubmit para validar una imagen de los formularios, usado en addPortada y addPDF
// (fomrularios com campos de vista previa de imagen obligatoria)
function validarImagen(idInput, idBotonGuardar, idBotonEditar) {
    var botonGuardar = document.getElementById(idBotonGuardar);
    var botonEditar = document.getElementById(idBotonEditar);
    if (botonGuardar && botonGuardar === document.activeElement) {
        var inputImagen = document.getElementById(idInput);
        if (!inputImagen || inputImagen.files.length === 0) {
            alert('Por favor, selecciona un archivo antes de enviar.');
            return false;
        }
    }
    if (botonEditar && botonEditar === document.activeElement) {
        return true; 
    }
    return true;
}





function soloNumeros(idInput) {
  const input = document.getElementById(idInput);
  input.addEventListener("keypress", function (e) {
    const char = e.key;
    if (!/[0-9.]/.test(char)) {
      e.preventDefault(); 
    }
    if (char === '.' && this.value.includes('.')) {
      e.preventDefault();
    }
  });
}
function soloNumeros2() {
    const inputs = document.querySelectorAll(".soloInput");
    inputs.forEach(input => {
        input.value = input.value.replace(/[^0-9.]/g, "");
        const partes = input.value.split(".");
        if (partes.length > 2) {
            input.value = partes[0] + "." + partes.slice(1).join("");
        }
    });
}
// Función para permitir solo números enteros sin puntos o comas
function soloNumInt(input) {
  let valor = input.value;
  let nuevoValor = valor.replace(/[^0-9]/g, '');
  if (nuevoValor.length > 1 && nuevoValor.startsWith('0')) {
    nuevoValor = nuevoValor.substring(1);
  }
  input.value = nuevoValor;
  const num = parseInt(nuevoValor) || 1;
  if (num < 1) {
    input.value = '1';
  }
}





// convierte un numero de 10.00 a 10; de 9.50 a 9.5 
function formatearDecimal(precio) {
    if (!precio) return "0";
    return parseFloat(precio).toString();
}












// Script para mostrar formularios en general mostrarForm =========================================
function mostrarForm(id){
  const form = document.getElementById(id);
  form.reset();
  const txtForm = document.getElementById('txt-' + id);
  if(id === 'formCliente'){
    txtForm.textContent = "Registrar Cliente";
    gestionarBtns('formCliente' , 'guardar');
  } else if(id === 'formProducto'){
     txtForm.textContent = "Crear producto";
     document.getElementById("imagen").required = true;
     document.getElementById('imagen').value = "";
     imagenesSeleccionadas.items.clear();
     document.getElementById('imagePreviewContainer').innerHTML = '';
  } else if(id === 'formUser'){
    txtForm.textContent = "Registrar Usuario";
    gestionarBtns('formUser' , 'guardar');
  } else if(id === 'formPortada'){
     txtForm.textContent = "Agregar portada";
     gestionarBtns('formPortada' , 'guardar');
     limpiarImagen('imagen', 'areaImg');
  } else if(id === 'formDetailProd'){
     txtForm.textContent = "Agregar detalle al producto";
     gestionarBtns('formDetailProd' , 'guardar');
  } else if(id == 'formMateria'){
     txtForm.textContent = "Crear materia prima";
     gestionarBtns('formMateria' , 'guardar');
     crearInputMedida('tipo_medida', 'mostrarMaterial_medida');
      limpiarImagen('imagenMaterial', 'areaImgMaterial');
  } else if(id === 'formBank'){
     txtForm.textContent = "Registrar cuenta de banco";
     gestionarBtns('formBank' , 'guardar');
     document.getElementById("imagenQR").required = true;
  }
  form.style.display = 'block';
}

//Muestra el formulario con buscador y campo html usado en almacen para agregar productos o materia prima
function mostrarFormBuscador(idForm, idContenedor) {
    const form = document.getElementById(idForm);
    form.style.display = 'block';
    form.reset();

    const contenedor = document.getElementById(idContenedor);
    contenedor.innerHTML = "";
}
//gestiona botones dinamicos de un formulario y reseta si es en formulario nuevo (sin casos especiales)
function gestionarBtns(id, btnMostrar){
    const btnEditar = document.getElementById('btn2-' + id);
    const btnGuardar = document.getElementById('btn1-' + id);
    if(btnMostrar === 'editar'){
       btnEditar.style.display='block';
       btnGuardar.style.display='none';
    } else if(btnMostrar === 'guardar'){
       btnEditar.style.display='none';
       btnGuardar.style.display='block';
    }
}













// Script del buscador de clientes ===================================================================

let listaAllClients = [];

function getAllClients() {
    fetch("functions/get_all_clients.php")
        .then(response => response.json())
        .then(data => {
            listaAllClients = data.clientes;
            // console.log("lista de clientes con detalles: ", listaAllClients);
        })
        .catch(error => {
            console.error("Error al obtener los clientes:", error);
        });
}

function llenarInputsCliente(cliente) {
    document.getElementById('nameCliente').value = cliente.nombre || '';
    document.getElementById('empresaCliente').value = cliente.empresa || '';
    document.getElementById('nit').value = cliente.nit || '';
    document.getElementById('carnetCliente').value = cliente.carnet_ci || '';
    const selectDepartamento = document.getElementById('departamento');
    if (cliente.departamento && selectDepartamento) {
        for (let option of selectDepartamento.options) {
            if (option.value === cliente.departamento) {
                option.selected = true;
                break;
            }
        }
    }
    document.getElementById('correo').value = cliente.correo || '';
    document.getElementById('celCliente').value = cliente.telefono || '';
    document.getElementById('celEmpresa').value = cliente.cel_empresa || '';
    document.getElementById('detailCLienteProd').value = cliente.nota || '';
    document.getElementById('id_cliente').value = cliente.id_cliente || '';
}

function limpiarInputs() {
    document.getElementById('id_cliente').value = '';
    document.getElementById('nameCliente').value = '';
    document.getElementById('empresaCliente').value = '';
    document.getElementById('nit').value = '';
    document.getElementById('carnetCliente').value = '';
    const selectDepartamento = document.getElementById('departamento');
    if (selectDepartamento) {
        selectDepartamento.selectedIndex = 0;
    }
    document.getElementById('correo').value = '';
    document.getElementById('celCliente').value = '';
    document.getElementById('celEmpresa').value = '';
    document.getElementById('detailCLienteProd').value = '';
    const hiddenInput = document.getElementById('hiddenClienteId');
    if (hiddenInput) {
        hiddenInput.remove();
    }
}

// Versión mejorada con sugerencias en tiempo real
function buscarClienteConSugerencias() {
    const searchInput = document.getElementById('search-client');
    const searchTerm = searchInput.value.trim().toLowerCase();
    
    // Limpiar sugerencias previas
    limpiarSugerencias();
    
    if (!searchTerm) {
        limpiarInputs();
        return;
    }
    
    // Filtrar clientes que coincidan
    const clientesCoincidentes = listaAllClients.filter(cliente => {
        return (
            (cliente.nombre && cliente.nombre.toLowerCase().includes(searchTerm)) ||
            (cliente.empresa && cliente.empresa.toLowerCase().includes(searchTerm)) ||
            (cliente.nit && cliente.nit.toLowerCase().includes(searchTerm)) ||
            (!isNaN(searchTerm) && cliente.id_cliente.toString() === searchTerm) ||
            (cliente.telefono && cliente.telefono.includes(searchTerm))
        );
    });
    
    // Si hay coincidencias, mostrar sugerencias
    if (clientesCoincidentes.length > 0) {
        mostrarSugerencias(clientesCoincidentes, searchTerm);
        
        // Si hay exactamente una coincidencia, autocompletar
        if (clientesCoincidentes.length === 1) {
            llenarInputsCliente(clientesCoincidentes[0]);
        }
    } else {
        limpiarInputs();
    }
}

function mostrarSugerencias(clientes, termino) {
    const contenedorSugerencias = document.getElementById('sugerencias-clientes');
    // Limpiar sugerencias previas
    contenedorSugerencias.innerHTML = '';
    // Si no hay clientes, ocultar el contenedor
    if (clientes.length === 0) {
        contenedorSugerencias.style.display = 'none';
        return;
    }
    contenedorSugerencias.style.display = 'block';
    let sugerenciasHTML = '';
    clientes.forEach((cliente, index) => {
        const nombre = cliente.nombre || 'Sin nombre';
        let nombreHTML = nombre;
        
        if (termino && termino.length > 0) {
            const regex = new RegExp(`(${termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            nombreHTML = nombre.replace(regex, '<strong>$1</strong>');
        }
        const empresaHTML = cliente.empresa 
            ? `<div class="sugerencia-empresa f-align"><span>🏢</span> ${cliente.empresa}</div>`
            : '';
        const telefonoHTML = cliente.telefono 
            ? `<div class="sugerencia-telefono">📞 ${cliente.telefono}</div>`
            : '';
        const nitHTML = cliente.nit 
            ? `<div class="sugerencia-nit" title="${cliente.nit}">📄 ${cliente.nit.substring(0, 15)}${cliente.nit.length > 15 ? '...' : ''}</div>`
            : '';
        sugerenciasHTML += `
            <div class="sugerencia-cliente flex-between" data-cliente-index="${index}">
                <div class="sugerencia-info-principal">
                    <div class="sugerencia-nombre">${nombreHTML}</div>
                    ${empresaHTML}
                </div>
                <div class="sugerencia-info-secundaria">
                    ${telefonoHTML}
                    ${nitHTML}
                </div>
            </div>
        `;
    });
    sugerenciasHTML += `<div class="contador-sugerencias">${clientes.length} cliente${clientes.length !== 1 ? 's' : ''} encontrado${clientes.length !== 1 ? 's' : ''}</div>`;
    contenedorSugerencias.innerHTML = sugerenciasHTML;
    const sugerenciasElements = contenedorSugerencias.querySelectorAll('.sugerencia-cliente');
    sugerenciasElements.forEach((element, index) => {
        element.addEventListener('click', () => {
            llenarInputsCliente(clientes[index]);
            document.getElementById('search-client').value = clientes[index].nombre || '';
            contenedorSugerencias.innerHTML = '';
            contenedorSugerencias.style.display = 'none';
        });
    });
}
function limpiarSugerencias() {
    const contenedorSugerencias = document.getElementById('sugerencias-clientes');
    if (contenedorSugerencias) {
        contenedorSugerencias.innerHTML = '';
        contenedorSugerencias.style.display = 'none';
    }
}
document.addEventListener('click', (e) => {
    const searchInput = document.getElementById('search-client');
    const sugerencias = document.getElementById('sugerencias-clientes');
    
    if (sugerencias && sugerencias.style.display === 'block') {
        if (!searchInput.contains(e.target) && !sugerencias.contains(e.target)) {
            limpiarSugerencias();
        }
    }
});
// Ajustar posición de las sugerencias cuando se hace scroll
window.addEventListener('scroll', () => {
    const sugerencias = document.getElementById('sugerencias-clientes');
    if (sugerencias && sugerencias.style.display === 'block') {
        limpiarSugerencias();
    }
});









// Script del fomulario acordeon =================================================================

//Usado en el formulario de cotizacion para editar o guardar
function cambiarDataForm(id, accion) {
  const form = document.getElementById(id);
  form.dataset.accion = accion;
}

//maneja el caso de enviar submit e form de venta y los 2 casos del form de cotización
function validarFormularioAcordeon(event, idForm) {
    event.preventDefault();
    let valido = true;
    const form = event.target;

    // Limpiar errores previos
    form.querySelectorAll('.error-border').forEach(el => el.classList.remove('error-border'));
    form.querySelectorAll('.error-radio').forEach(el => el.classList.remove('error-radio'));
    form.querySelectorAll('.error-tab').forEach(el => el.classList.remove('error-tab'));

    // Validar inputs, selects y textareas requeridos
    const camposRequeridos = form.querySelectorAll('input[required]:not([type="radio"]), select[required], textarea[required]');

    camposRequeridos.forEach(campo => {
        if (campo.value.trim() === "") {
            campo.classList.add('error-border');
            valido = false;

            // Marcar header de acordeón correspondiente
            let contenido = campo.closest('.accordion-content');
            if (contenido) {
                let header = contenido.previousElementSibling;
                if (header) header.classList.add('error-tab');
            }
        }
    });

    // Validar radios
    const gruposRadio = new Set();
    const radios = form.querySelectorAll('input[type="radio"][required]');

    radios.forEach(radio => gruposRadio.add(radio.name));

    gruposRadio.forEach(nombreGrupo => {
        const seleccionado = form.querySelector(`input[name="${nombreGrupo}"]:checked`);

        if (!seleccionado) {
            const primerRadio = form.querySelector(`input[name="${nombreGrupo}"]`);
            if (primerRadio) {
                const contenedor = primerRadio.closest('.radio-group');
                if (contenedor) contenedor.classList.add('error-radio');

                // Marcar también el header de la pestaña
                let contenido = primerRadio.closest('.accordion-content');
                if (contenido) {
                    let header = contenido.previousElementSibling;
                    if (header) header.classList.add('error-tab');
                }
            }
            valido = false;
        }
    });

    if (!valido) {
        alert("Por favor, completa los campos requeridos.");
        return false;
    }
  
    // En caso de ser un formulario de cotización manejar la accción
    if(idForm == 'formAcordeonCotiz'){
       const data = form.dataset.accion; 
       if(data === 'guardar'){
           form.action = "actions/crearCotizacion.php";
       }else if(data === 'editar'){
           form.action = "actions/editarCotizacion.php";
       }
    }

    form.submit();
}


// limpa los campos rojos de error del fomulario
function limpiarFormAcordeon(form = null) {
    if (!form) {
        form = document.querySelector('form');
        if (!form) return;
    }
    form.querySelectorAll('.error-border').forEach(el => {
        el.classList.remove('error-border');
    });
    form.querySelectorAll('.error-radio').forEach(el => {
        el.classList.remove('error-radio');
    });
    form.querySelectorAll('.error-tab').forEach(el => {
        el.classList.remove('error-tab');
    });
}


function desplazarPanel(idPestana) {
   const lista = document.getElementById('list-prod-details');
   if (lista && lista.offsetParent !== null) {
      lista.style.display = 'none';
   }

    const panel = document.getElementById(idPestana);
    const header = panel.previousElementSibling; // <div class="accordion-header">

    // Cerrar todos los paneles excepto el actual
    document.querySelectorAll(".accordion-content").forEach(sec => {
        if (sec.id !== idPestana) {
            sec.style.maxHeight = null;
            sec.classList.remove("open");
            sec.style.overflowY = "hidden";

            // Quitar color del header
            sec.previousElementSibling.classList.remove("mostrando");
        }
    });

    if (panel.style.maxHeight) {
        // Cerrar
        panel.style.maxHeight = null;
        panel.classList.remove("open");
        panel.style.overflowY = "hidden";
        header.classList.remove("mostrando");
    } else {
        // Abrir
        panel.classList.add("open");
        header.classList.add("mostrando");

        const alturaReal = panel.scrollHeight;
        const alturaMaxima = window.innerHeight * 0.53;

        if (alturaReal > alturaMaxima) {
            panel.style.maxHeight = alturaMaxima + "px";
            panel.style.overflowY = "auto";
        } else {
            panel.style.maxHeight = alturaReal + "px";
            panel.style.overflowY = "hidden";
        }
    }
}


function desplazarPanelList() {
    const panel = document.getElementById('pest3');
    panel.style.maxHeight = '300px';
    panel.style.overflowY = 'auto';

    // Cerrar todos los paneles excepto pest3
    document.querySelectorAll(".accordion-content").forEach(sec => {
        if (sec.id !== 'pest3') {
            sec.style.maxHeight = null;
            sec.classList.remove("open");
            sec.style.overflowY = "hidden";
            // Quitar color del header
            sec.previousElementSibling.classList.remove("mostrando");
        }
    });
}
function mostrarDesc(idMonto, idPorcentaje) {
    const tipo = document.getElementById('tipoDescuento').value;
    const monto = document.getElementById(idMonto);
    const porcentaje = document.getElementById(idPorcentaje);

    // Ocultar ambos inputs y quitar required
    monto.style.display = 'none';
    monto.required = false;

    porcentaje.style.display = 'none';
    porcentaje.required = false;
    if (tipo === 'monto') {
        monto.style.display = 'inline-block';
        monto.required = true;
    } else if (tipo === 'porcentaje') {
        porcentaje.style.display = 'inline-block';
        porcentaje.required = true;
    }

    //para mostrar/ocultar textos del total del form
    const contenedorTxt = document.querySelector(".total-general");
    const totalgeneralTxt = contenedorTxt.querySelectorAll("p")[0];
    const totalDescTxt = contenedorTxt.querySelectorAll("p")[1];
    if (tipo !== "") {
        totalgeneralTxt.style.display = "none";
        totalDescTxt.style.display = "block";
    } else {
        totalgeneralTxt.style.display = "block";
        totalDescTxt.style.display = "none";
    }

    calcularTotalconDescuento();
}
function gestionarFechaEntrega() {
    const si = document.getElementById("fechaEntregaSi").checked;
    const campo = document.getElementById("campo-fechaEntrega");
    const inputFecha = document.getElementById("fechaEntrega");
    if (si) {
        campo.style.display = "flex";
        inputFecha.required = true;
    } else {
        campo.style.display = "none";
        inputFecha.required = false;
        inputFecha.value = "";
    }
}
//para el form acordeon
function gestionarFechaEntrega2() {
    const si = document.getElementById("fechaEntregaSi2").checked;
    const campo = document.getElementById("campo-fechaEntrega2");
    const inputFecha = document.getElementById("fechaEntrega2");
    if (si) {
        campo.style.display = "flex";
        inputFecha.required = true;
    } else {
        campo.style.display = "none";
        inputFecha.required = false;
        inputFecha.value = "";
    }
}









function editarCuenta(cuenta) {
    plop('formBank');
    
    gestionarBtns('formBank' , 'editar');
    const titulo = document.getElementById('txt-formBank');
    titulo.textContent = 'Editar cuenta de banco';

    console.log(cuenta);
    document.getElementById("id_cuenta").value = cuenta.id;
    document.getElementById("titularCuenta").value = cuenta.titularCuenta;
    document.getElementById("numeroCuenta").value = cuenta.numeroCuenta;
    document.getElementById("nombreBanco").value = cuenta.nombreBanco;
  
    document.getElementById("fechaCaducidadQR").value = cuenta.fechaCaducidadQR;
    document.getElementById("titularQrTienda").value = cuenta.titular;

    document.getElementById("imagenQR").required = false;
}
