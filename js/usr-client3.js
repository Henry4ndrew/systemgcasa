//validacion de la confirmacion de contrasñea
function validarFormConstrasena() {
        var password = document.getElementById("passwordUser").value;
        var confirmPassword = document.getElementById("confirmPasswordUser").value;
        if (password !== confirmPassword) {
            alert("Las contraseñas no coinciden. Por favor, verifica.");
            return false; 
        }
        return true; 
}


//funcion para recojer datos y editar un perfil
function editarUser(id, nombreUsuario, celular, permiso, fotoPerfilUrl) {
    plop('formUser');

    gestionarBtns('formUser' , 'editar');
    document.getElementById('txt-formUser').textContent = "Editar usuario";
    
    const permisoSelect = document.getElementById('permiso');
    permisoSelect.value = permiso;

    document.getElementById('idUser').value = id;
    document.getElementById('nameUser').value = nombreUsuario; 
    document.getElementById('celUser').value = celular; 
    document.getElementById('passwordUser').value = '';
    document.getElementById('confirmPasswordUser').value = '';
}


function editarCliente(cliente) {
    //Mostramos el fomrulario
    plop('formCliente');
    
    gestionarBtns('formCliente' , 'editar');
    const titulo = document.getElementById('txt-formCliente');
    titulo.textContent = 'Editar cliente';

    document.getElementById("id_cliente").value = cliente.id_cliente;
    document.getElementById("nameCliente").value = cliente.nombre;
    document.getElementById("empresaCliente").value = cliente.empresa;
    document.getElementById("nit").value = cliente.nit;
    document.getElementById("carnetCliente").value =  cliente.carnet_ci;
    document.getElementById("departamento").value = cliente.departamento;
    document.getElementById("correo").value = cliente.correo;
    document.getElementById("celCliente").value = cliente.celular;
    document.getElementById("celEmpresa").value = cliente.cel_empresa;
    document.getElementById("detailCLienteProd").value = cliente.nota;
}



// Cambia el estado de los usuarios, clientes
function stateSwitch(input) {
  const form = input.closest('form');
  const contenedor = input.closest('.switch');
    const hidden = form.querySelector('.estado-input');
    const texto = form.querySelector('.label-text');
  const nuevoEstado = input.checked ? 'activo' : 'inactivo';
  const nuevoTexto = input.checked ? 'Activo' : 'Inactivo';
  if (confirm('¿Desea cambiar el estado?')) {
    texto.textContent = nuevoTexto;
    hidden.value = nuevoEstado;
    form.submit();
  } else {
    input.checked = !input.checked;
    const estadoRevertido = input.checked ? 'activo' : 'inactivo';
    texto.textContent = input.checked ? 'Activo' : 'Inactivo';
    hidden.value = estadoRevertido;
  }
}







// Seccion de imprimir en pdf los clientes
// Función para seleccionar/deseleccionar todos los checkboxes
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.cliente-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateCheckAllState();
}

// Función para actualizar el estado del checkbox "todos"
function updateCheckAllState() {
    const checkboxes = document.querySelectorAll('.cliente-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    const someChecked = Array.from(checkboxes).some(cb => cb.checked);
    
    const checkAll = document.getElementById('checkAll');
    checkAll.checked = allChecked;
    checkAll.indeterminate = someChecked && !allChecked;
}

// Función para exportar los clientes seleccionados
function exportarSeleccionados() {
    const checkboxes = document.querySelectorAll('.cliente-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Por favor, seleccione al menos un cliente para exportar.');
        return;
    }
    
    // Crear un array con los IDs de los clientes seleccionados
    const clientesSeleccionados = Array.from(checkboxes).map(checkbox => checkbox.value);
    
    // También podrías enviar toda la data si necesitas más información
    const clientesData = Array.from(checkboxes).map(checkbox => ({
        id: checkbox.value,
        nombre: checkbox.dataset.nombre,
        celular: checkbox.dataset.celular,
        empresa: checkbox.dataset.empresa,
        departamento: checkbox.dataset.departamento
    }));
    
    // Guardar en el input hidden
    document.getElementById('clientesSeleccionados').value = JSON.stringify(clientesSeleccionados);
    
    // Enviar el formulario
    document.getElementById('formExportarPDF').submit();
}

// Agregar event listeners a los checkboxes individuales
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.cliente-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCheckAllState);
    });
    
    // También actualizar después de búsquedas
    const searchInput = document.getElementById('search-clientes');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            setTimeout(updateCheckAllState, 100);
        });
    }
});