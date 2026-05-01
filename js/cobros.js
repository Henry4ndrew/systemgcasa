//DE AQUI PAR ABAJO SECCION DE COBROS
let saldoActual = 0;

function mostrarformCobro(boton) {
    const datosCobro = JSON.parse(boton.getAttribute('data-cobro'));
    
    plop('formCobro');
    document.getElementById('id_venta_cobro').value = datosCobro.id_venta;
    
    const saldoFormateado = formatearDecimal(datosCobro.saldo);
    document.getElementById('saldo-cobro').value = saldoFormateado;
    
    saldoActual = parseFloat(datosCobro.saldo);
    
    // Mostrar los 3 datos específicos que quieres
    const contenedor = document.getElementById('datos-venta-cobro');
    contenedor.innerHTML = `
        <div class="itemMaterial pad10">
            <p ><b>Cliente-Empresa:</b></p>
            <p>${datosCobro.cliente_nombre}</p>
            <p>${datosCobro.cliente_empresa || ''}</p>
            <p><b>Total Venta: </b>${formatearDecimal(datosCobro.total_venta)} Bs</p>
        </div>
    `;
    
    gestionarCampoFecha(saldoActual);
    actualizarCambio(0);
}

function calcularSaldo(idAnticipo, idSaldo) {
    const anticipoInput = parseFloat(document.getElementById(idAnticipo).value) || 0;
    const saldoCalculado = document.getElementById(idSaldo);
    
    // calcular saldo restante
    const saldo = saldoActual - anticipoInput;
    
    // Gestionar el campo de fecha según el nuevo saldo
    gestionarCampoFecha(saldo);
    
    // Actualizar el cambio si el saldo es negativo
    actualizarCambio(saldo);
    
    // Formatear el saldo para mostrar
    let saldoFormateado;
    if (saldo > 0) {
        saldoFormateado = formatearDecimal(saldo.toFixed(2));
    } else {
        saldoFormateado = '0';
    }
    
    saldoCalculado.value = saldoFormateado;
}

function gestionarCampoFecha(saldo) {
    const campoFecha = document.getElementById('campoFechaSigPago');
    const inputFecha = document.getElementById('fechaSigPago');
    
    if (saldo <= 0) {
        // Ocultar campo y quitar required si saldo es 0 o negativo
        campoFecha.style.display = 'none';
        inputFecha.removeAttribute('required');
        inputFecha.value = ''; // Limpiar el valor
    } else {
        // Mostrar campo y agregar required si saldo es positivo
        campoFecha.style.display = 'flex';
        inputFecha.setAttribute('required', 'required');
        
        // Opcional: establecer fecha mínima como hoy + 1 día
        const hoy = new Date();
        const manana = new Date(hoy);
        manana.setDate(hoy.getDate() + 1);
        const fechaMin = manana.toISOString().split('T')[0];
        inputFecha.min = fechaMin;
    }
}

function actualizarCambio(saldo) {
    const cambioElemento = document.getElementById('cambioCobro');
    
    if (saldo < 0) {
        // Si el saldo es negativo, mostrar el excedente como cambio
        const cambio = Math.abs(saldo).toFixed(2);
        const cambioFormateado = formatearDecimal(cambio);
        cambioElemento.textContent = cambioFormateado;
        cambioElemento.style.color = '#28a745'; // Verde para cambio positivo
    } else {
        // Si el saldo es 0 o positivo, mostrar 0
        cambioElemento.textContent = '0';
        cambioElemento.style.color = ''; // Restablecer color
    }
}

function limpiarFecha(idFecha) {
    document.getElementById(idFecha).value = '';
}
function filtrarTablaPorEstado() {
    const estado = document.getElementById('filtro-estado').value;
    
    // Ocultar/mostrar secciones
    const resumen = document.querySelector('.resumen-pagos');
    const tfoot = document.querySelector('#tabla-cobros tfoot');
    
    if (resumen) resumen.style.display = estado === '' ? 'block' : 'none';
    if (tfoot) tfoot.style.display = estado === '' ? '' : 'none';
    
    // Filtrar filas
    document.querySelectorAll('#tabla-cobros tbody tr').forEach(fila => {
        if (fila.cells.length > 1) { // Solo filas con datos
            const badge = fila.querySelector('.badge');
            if (badge) {
                const estadoFila = badge.className.split('estado-')[1];
                fila.style.display = estado === '' || estadoFila === estado ? '' : 'none';
            }
        }
    });
}