/*
1)Historial de venta
2)Historial de materia prima
*/


//====================HISTORIAL DE VENTA==========================
const ventasEnImpresion = window.__ventasEnImpresion || new Set();
window.__ventasEnImpresion = ventasEnImpresion;

function obtenerOverlayImpresion() {
    let overlay = document.getElementById('overlay-impresion-venta');
    if (overlay) {
        return overlay;
    }

    overlay = document.createElement('div');
    overlay.id = 'overlay-impresion-venta';
    overlay.style.position = 'fixed';
    overlay.style.inset = '0';
    overlay.style.background = 'rgba(0, 0, 0, 0.45)';
    overlay.style.display = 'none';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '9999';

    const box = document.createElement('div');
    box.style.background = '#fff';
    box.style.padding = '16px 20px';
    box.style.borderRadius = '10px';
    box.style.fontSize = '14px';
    box.style.fontWeight = '600';
    box.style.color = '#0d3b66';
    box.style.boxShadow = '0 8px 30px rgba(0,0,0,.2)';
    box.textContent = 'Enviando a impresora...';

    overlay.appendChild(box);
    document.body.appendChild(overlay);

    return overlay;
}

function mostrarCargandoImpresion(mensaje) {
    const overlay = obtenerOverlayImpresion();
    const box = overlay.firstChild;
    if (box) {
        box.textContent = mensaje || 'Enviando a impresora...';
    }
    overlay.style.display = 'flex';
}

function ocultarCargandoImpresion() {
    const overlay = document.getElementById('overlay-impresion-venta');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Botón para imprimir venta
function imprimirVenta(idVenta, boton = null) {
    if (ventasEnImpresion.has(String(idVenta))) {
        // En lugar de alert, muestra mensaje suave bajo el botón
        if (boton) {
            _mostrarMsgBoton(boton, '⚠ Ya se está imprimiendo');
        }
        return;
    }

    ventasEnImpresion.add(String(idVenta));

    if (boton) {
        boton.disabled = true;
        boton.dataset.originalText = boton.textContent;
        boton.innerHTML = '<span class="btn-spinner"></span>Imprimiendo...';
    }

    mostrarCargandoImpresion(`Imprimiendo venta #${idVenta}...`);

    const isElectronPrinterAvailable =
        typeof window !== 'undefined' &&
        window.electronAPI &&
        typeof window.electronAPI.printTicket === 'function';

    if (!isElectronPrinterAvailable) {
        window.open(`functions/crearPdfVenta.php?id_venta=${encodeURIComponent(idVenta)}`, '_blank');
        if (boton) _mostrarMsgBoton(boton, 'PDF abierto para imprimir');
        _limpiarBoton(boton, idVenta);
        return;
    }

    fetch('functions/obtener_ticket_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_venta=${encodeURIComponent(idVenta)}`
    })
        .then(r => r.json())
        .then(async datos => {
            if (!datos.success || !datos.ticket) {
                throw new Error(datos.message || 'No se pudo preparar el ticket.');
            }
            const resultado = await window.electronAPI.printTicket(datos.ticket);
            if (!resultado?.success) {
                throw new Error(resultado?.message || 'La impresora no respondió.');
            }
            if (boton) _mostrarMsgBoton(boton, '✓ Enviado');
        })
        .catch(error => {
            console.error('Error al imprimir venta:', error);
            const rawMessage = (error && error.message) ? error.message : 'Error al imprimir';
            const isPrinterError = /impresora|printer|timeout|conexion|conectar|device/i.test(rawMessage);
            const userMessage = isPrinterError
                ? 'No se encontro la impresora. Verifica que este encendida y conectada.'
                : rawMessage;

            if (isPrinterError) {
                alert(userMessage);
            }
            if (boton) _mostrarMsgBoton(boton, `✗ ${userMessage}`);
        })
        .finally(() => {
            _limpiarBoton(boton, idVenta);
        });
}

// ── helpers internos ────────────────────────────────────────────────
function _limpiarBoton(boton, idVenta) {
    ventasEnImpresion.delete(String(idVenta));
    ocultarCargandoImpresion();
    if (boton) {
        boton.disabled = false;
        boton.textContent = boton.dataset.originalText || 'Imprimir';
    }
}

function _mostrarMsgBoton(boton, texto) {
    const msg = document.createElement('span');
    msg.textContent = texto;
    msg.style.cssText = 'margin-left:8px; font-size:12px; opacity:0.8;';
    boton.parentNode.appendChild(msg);
    setTimeout(() => msg.remove(), 2500);
}

function mostrarCliente(idCliente) {
    const campoCliente = document.getElementById('campoDetailCliente');
    const contenedor = document.getElementById('detail-venta-cliente');
    plop('campoDetailCliente');

    if (campoCliente.style.display === 'none') {
        contenedor.innerHTML = '';
        return;
    }

    fetch('functions/obtener_cliente.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_cliente=${idCliente}`
    })
    .then(respuesta => respuesta.json())
    .then(datos => {
        if (datos.success) {
            let html = `
                <div class="column2 b-blank pad20" style="height:100%">
                    <i class="fa-solid fa-users f-gold userBig"></i>
                    <div class="column2 f-peq">
            `;

            const campos = {
                'Nombre': datos.nombre,
                'Nit': datos.nit,
                'Carnet personal': datos.carnet_ci,
                'Celular personal': datos.telefono,
                'Celular empresa': datos.cel_empresa,
                'Correo': datos.correo,
                'Empresa': datos.empresa,
                'Departamento': datos.departamento,
                'Nota': datos.nota,
                'Fecha de registro': datos.fecha_registro
            };

            for (const [label, valor] of Object.entries(campos)) {
                if (valor && valor.trim() !== '') {
                    if (label === 'Nota') {
                        html += `<p><b>${label}:</b></p><div>${valor}</div>`;
                    } else {
                        html += `<p><b>${label}:</b> ${valor}</p>`;
                    }
                }
            }

            html += `
                    </div>
                </div>
            `;
            contenedor.innerHTML = html;

        } else {
            contenedor.innerHTML = `<p style="color:red;">${datos.message}</p>`;
        }
    })
    .catch(error => {
        console.error('Error al obtener datos del cliente:', error);
        contenedor.innerHTML = '<p style="color:red;">Error al cargar la información del cliente.</p>';
    });
}









function mostrarDetalleVenta(idVenta) {
    const campoDetalle = document.getElementById('campoDetailVenta');
    const contenedor = document.getElementById('detail-venta-content');
    const numVenta = document.getElementById('num_venta');
    plop('campoDetailVenta');
    if (campoDetalle.style.display === 'none') {
        contenedor.innerHTML = '';
        return;
    }
    fetch(`functions/obtener_detalle_venta.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_venta=${idVenta}`
    })
    .then(respuesta => respuesta.json())
    .then(datos => {
        if (datos.success) {
            console.log("Datos recibidos del servidor:", datos);
            numVenta.textContent = datos.id_venta || idVenta;
            construirTablaDetalles(contenedor, datos);
        } else {
            contenedor.innerHTML = `<p style="color:red;">${datos.message}</p>`;
        }
    })
    .catch(error => {
        console.error('Error al obtener detalles:', error);
        contenedor.innerHTML = '<p style="color:red;">Error al cargar los detalles.</p>';
    });
}

function construirTablaDetalles(contenedor, datos) {
    const { productos, total_venta } = datos;
    
    // Verificar si hay datos de descuento en el primer producto
    // (todos los productos tendrán los mismos valores ya que provienen de la misma venta)
    const primerProducto = productos[0];
    const tipoDescuento = primerProducto?.tipo_descuento || null;
    const valorDescuento = primerProducto?.valor_descuento || 0;
    
    let tablaHTML = `
        <table class="tablaStyle tabla-detalles col2-big-col5-peq">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Medida</th>
                    <th>Detalle</th>
                    <th>Precio Unit.</th>
                    <th>Precio Venta</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
    `;

    productos.forEach(producto => {
        const rutaImagen = producto.ruta_imagen ? producto.ruta_imagen.replace('../', '') : null;
        const imagenHTML = rutaImagen 
            ? `<img src="${rutaImagen}" alt="Imagen producto" class="img-dentro-tabla">`
            : `<div class="sin-imagen">Sin imagen</div>`;

        // Mostrar características finales
        const caracteristicasHTML = producto.caracteristicas_final && 
                                   producto.caracteristicas_final !== 'Sin características'
            ? `<div class="caracteristicas-producto">
                 <small>${producto.caracteristicas_final}</small>
               </div>`
            : '';

        tablaHTML += `
            <tr>
                <td>${imagenHTML}</td>
                <td>
                    <strong>${producto.nombre || 'N/A'}</strong>
                    ${caracteristicasHTML}
                </td>
                <td>${producto.codigo || 'N/A'}</td>
                <td>${producto.medida || 'N/A'}</td>
                <td>${producto.detalle_final || 'N/A'}</td>
                <td>${parseFloat(producto.precio_unitario || 0).toFixed(2)} <span class="Bs">Bs</span></td>
                <td>${parseFloat(producto.precio_venta || 0).toFixed(2)} <span class="Bs">Bs</span></td>
                <td><div class="f-center">${producto.cantidad || 0}</div></td>
                <td><strong>${parseFloat(producto.sub_total || 0).toFixed(2)} <span class="Bs">Bs</span></strong></td>
            </tr>
        `;
    });
    
    tablaHTML += `
            </tbody>
        </table>
        <div class="f-center pad20 f-white">
            <b><span class="total-venta">Total de Venta: ${parseFloat(total_venta || 0).toFixed(2)}</span> Bs</b>
    `;
    
    // Mostrar información de descuento si existe
    if (tipoDescuento && valorDescuento > 0) {
        const descuentoText = tipoDescuento === 'porcentaje' 
            ? `${valorDescuento}%` 
            : `${parseFloat(valorDescuento).toFixed(2)} Bs`;
            
        tablaHTML += `
            <div class="info-descuento">
                <small>Descuento aplicado: ${descuentoText} (${tipoDescuento})</small>
            </div>
        `;
    }
    
    tablaHTML += `</div>`;

    contenedor.innerHTML = tablaHTML;
}


function filtrarLugarVenta() {
    const filtro = document.getElementById("filtroLugarVenta").value.toLowerCase();
    const tabla = document.getElementById("tablaHistorialVentas");
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    for (let i = 0; i < filas.length; i++) {
        const lugar = filas[i]
            .querySelector("td[id^='v-total-'] p.hora")
            .textContent
            .trim()
            .toLowerCase();

        if (filtro === "todos" || lugar === filtro) {
            filas[i].style.display = "";
        } else {
            filas[i].style.display = "none";
        }
    }
}












//====================HISTORIAL DE MATERIA PRIMA==========================

function openForm(boton) {
    document.querySelectorAll('.formStyle').forEach(form => {
        form.style.display = 'none';
    });
    const td = boton.closest('td');
    const formularios = td.querySelectorAll('.formStyle');
    formularios.forEach(form => {
        form.style.display = 'block';
    });
}
function closeForm() {
    document.querySelectorAll('.formStyle').forEach(form => {
        form.style.display = 'none';
    });
}
function filtrarHistMateriaPrima() {
    const filtro = document.getElementById("filtroLugarVenta").value.toLowerCase();
    const tabla = document.getElementById("tablaHistorialMP");
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    for (let i = 0; i < filas.length; i++) {
        const celdaAccion = filas[i].getElementsByTagName("td")[1];
        if (celdaAccion) {
            const texto = celdaAccion.textContent.toLowerCase();

            if (filtro === "todos" || texto.includes(filtro)) {
                filas[i].style.display = "";
            } else {
                filas[i].style.display = "none";
            }
        }
    }
}

