//funciones de los buscadores
//funcion para buscar en ls primeras 2 columnas de una tabla
function buscar1C2C(idInput, idTabla) {
    const input = document.getElementById(idInput);
    const filtro = input.value.trim().toLowerCase().replace(/\s+/g, '');
    const tabla = document.getElementById(idTabla);
    const filas = tabla.getElementsByTagName('tr');
    for (let i = 1; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName('td');
        
        let mostrarFila = false;
        
        // Verificar si hay al menos 2 columnas
        if (celdas.length >= 2) {
            const textoCol1 = (celdas[0].textContent || celdas[0].innerText).toLowerCase().replace(/\s+/g, '');
            const textoCol2 = (celdas[1].textContent || celdas[1].innerText).toLowerCase().replace(/\s+/g, '');
            if (textoCol1.includes(filtro) || textoCol2.includes(filtro)) {
                mostrarFila = true;
            }
        }
        fila.style.display = mostrarFila ? '' : 'none';
    }
}
// Función para buscar en las columnas 2 y 3 de una tabla
function buscar2C3C(idInput, idTabla) {
    const input = document.getElementById(idInput);
    const filtro = input.value.trim().toLowerCase().replace(/\s+/g, '');
    const tabla = document.getElementById(idTabla);
    const filas = tabla.getElementsByTagName('tr');
    
    for (let i = 1; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName('td');
        let mostrarFila = false;
        
        // Verificar si hay al menos 3 columnas
        if (celdas.length >= 3) {
            const textoCol2 = (celdas[1].textContent || celdas[1].innerText).toLowerCase().replace(/\s+/g, '');
            const textoCol3 = (celdas[2].textContent || celdas[2].innerText).toLowerCase().replace(/\s+/g, '');
            
            if (textoCol2.includes(filtro) || textoCol3.includes(filtro)) {
                mostrarFila = true;
            }
        }
        fila.style.display = mostrarFila ? '' : 'none';
    }
}

function buscar3C4C(idInput, idTabla) {
    const input = document.getElementById(idInput);
    const filtro = input.value.trim().toLowerCase().replace(/\s+/g, '');
    const tabla = document.getElementById(idTabla);
    const filas = tabla.getElementsByTagName('tr');
    
    for (let i = 1; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName('td');
        let mostrarFila = false;
        
        // Verificar si hay al menos 4 columnas
        if (celdas.length >= 4) {
            const textoCol3 = (celdas[2].textContent || celdas[2].innerText).toLowerCase().replace(/\s+/g, '');
            const textoCol4 = (celdas[3].textContent || celdas[3].innerText).toLowerCase().replace(/\s+/g, '');
            
            if (textoCol3.includes(filtro) || textoCol4.includes(filtro)) {
                mostrarFila = true;
            }
        }
        fila.style.display = mostrarFila ? '' : 'none';
    }
}


function normalizarTexto(texto) {
    return texto
        .toLowerCase()
        .normalize("NFD")                 
        .replace(/[\u0300-\u036f]/g, "") 
        .replace(/\s+/g, "");           
}

function buscar1C3C(inputId, tablaId) {
    const input = document.getElementById(inputId);
    const filtro = normalizarTexto(input.value);
    const tabla = document.getElementById(tablaId);
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    for (let i = 0; i < filas.length; i++) {
        const celdaID = filas[i].getElementsByTagName("td")[0];
        const celdaUsuario = filas[i].getElementsByTagName("td")[2];
        if (celdaID && celdaUsuario) {
            const textoID = normalizarTexto(celdaID.textContent);
            const textoUsuario = normalizarTexto(celdaUsuario.textContent);
            if (
                textoID.includes(filtro) ||
                textoUsuario.includes(filtro)
            ) {
                filas[i].style.display = "";
            } else {
                filas[i].style.display = "none";
            }
        }
    }
}




function filtrarPorFecha(tablaId, columnaFecha) {
    const desde = document.getElementById("fechaDesde").value;
    const hasta = document.getElementById("fechaHasta").value;
    const tabla = document.getElementById(tablaId);
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

    for (let i = 0; i < filas.length; i++) {
        const celda = filas[i].getElementsByTagName("td")[columnaFecha];
        if (!celda) continue;
        const fechaTexto = celda.querySelector("b").textContent.trim();
        const [dia, mes, anio] = fechaTexto.split("-");
        const fechaFila = `${anio}-${mes}-${dia}`;
        let mostrar = true;
        if (desde && fechaFila < desde) {
            mostrar = false;
        }
        if (hasta && fechaFila > hasta) {
            mostrar = false;
        }
        filas[i].style.display = mostrar ? "" : "none";
    }
}