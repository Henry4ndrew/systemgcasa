let imagenesSeleccionadas = new DataTransfer();
let contadorId = 0; 

function previsualizarImagenes() {
    const input = document.getElementById("imagen");
    const previewContainer = document.getElementById("imagePreviewContainer");
    previewContainer.innerHTML = ""; 
    
    // Procesar todos los archivos
    for (let i = 0; i < input.files.length; i++) {
        const archivo = input.files[i];
        
        // Verificar si es archivo .jfif
        if (archivo.name.toLowerCase().endsWith('.jfif')) {
            console.log(`Convirtiendo imagen .jfif: ${archivo.name}`);
            convertirJfifAPng(archivo);
        } else {
            // Para archivos permitidos, agregar directamente
            archivo.idUnico = `imagen-${contadorId++}`;
            imagenesSeleccionadas.items.add(archivo);
        }
    }
    
    // Actualizar el input.files
    input.files = imagenesSeleccionadas.files;
    
    // Mostrar previsualización de los archivos procesados
    mostrarPrevisualizacion();
}

function generarNombreUnico(archivoOriginal) {
    const timestamp = new Date().getTime();
    const random = Math.floor(Math.random() * 10000);
    const nombreBase = archivoOriginal.name.split('.').slice(0, -1).join('.') || 'imagen';
    return `${nombreBase}_convertida_${timestamp}_${random}.png`;
}

function convertirJfifAPng(archivoJfif) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            // Crear canvas para la conversión
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            
            // Convertir a PNG
            canvas.toBlob(function(blob) {
                // Generar nombre único para el archivo convertido
                const nombreUnico = generarNombreUnico(archivoJfif);
                
                // Crear nuevo archivo PNG con nombre único
                const nuevoArchivo = new File([blob], nombreUnico, { type: 'image/png' });
                
                // Asignar ID único y agregar a la lista
                nuevoArchivo.idUnico = `imagen-${contadorId++}`;
                nuevoArchivo.esConvertida = true; // Marcar como convertida
                imagenesSeleccionadas.items.add(nuevoArchivo);
                
                console.log(`✓ Conversión exitosa: ${archivoJfif.name} → ${nombreUnico}`);
                console.log(`📁 Nuevo nombre único: ${nombreUnico}`);
                
                // Actualizar input y mostrar previsualización
                document.getElementById("imagen").files = imagenesSeleccionadas.files;
                mostrarPrevisualizacion();
                
            }, 'image/png');
        };
        
        img.src = e.target.result;
    };
    
    reader.readAsDataURL(archivoJfif);
}

function mostrarPrevisualizacion() {
    const previewContainer = document.getElementById("imagePreviewContainer");
    previewContainer.innerHTML = "";
    
    for (let i = 0; i < imagenesSeleccionadas.files.length; i++) {
        const archivo = imagenesSeleccionadas.files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imgContainer = document.createElement("div");
            imgContainer.id = archivo.idUnico;
            imgContainer.classList.add("imagen-preview");
            
            const img = document.createElement("img");
            img.src = e.target.result;
            
            const deleteButton = document.createElement("button");
            deleteButton.textContent = "✕";
            deleteButton.classList.add("eliminar-imagen");
            deleteButton.onclick = function() { 
                eliminarImagenDelInput(archivo.idUnico); 
            };
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(deleteButton);
            previewContainer.appendChild(imgContainer);
        };
        
        reader.readAsDataURL(archivo);
    } 
    console.log(`Número de imágenes procesadas: ${imagenesSeleccionadas.files.length}`);
}

function eliminarImagenDelInput(idUnico) {
    // Buscar y eliminar el archivo por su ID único
    for (let i = 0; i < imagenesSeleccionadas.files.length; i++) {
        if (imagenesSeleccionadas.files[i].idUnico === idUnico) {
            imagenesSeleccionadas.items.remove(i);
            break;
        }
    }
    // Actualizar el input
    document.getElementById("imagen").files = imagenesSeleccionadas.files;
    // Eliminar la previsualización
    const elemento = document.getElementById(idUnico);
    if (elemento) {
        elemento.remove();
    }
    console.log(`Imagen eliminada. Total: ${imagenesSeleccionadas.files.length}`);
}










function editarProd(codigo, nombre, categoria, caracteristicas, tiendaVirtual) {
    plop('formProducto');

    gestionarBtns('formProducto' , 'editar');
    document.getElementById('txt-formProducto').textContent = "Editar Producto";

    document.getElementById("imagen").required = false;
    document.getElementById('idProd').value = codigo;

    document.getElementById('codeProd').value = codigo; 
    document.getElementById('nameProd').value = nombre;
    document.getElementById('categProd').value = categoria;
    document.getElementById('charProd').value = caracteristicas;
    document.getElementById('disponible').value = tiendaVirtual;
}
function editarDetalleProd(idDetalle, precio, medida, detalle) {
    plop('formDetailProd');

    gestionarBtns('formDetailProd' , 'editar');
    document.getElementById('txt-formDetailProd').textContent = "Editar detalle de producto";

    document.getElementById('id_prod_detail').value = idDetalle;
    document.getElementById('medida_disp').value = medida;
    document.getElementById('detail_prod').value = detalle; 
    document.getElementById('price_prod').value = precio;
}








function agregarDetalleProd2(codeProd) {
    console.log(codeProd); 
    mostrarForm('formDetailProd');
    document.getElementById('id_prod_detail').value = codeProd;
}

function mostrarDetalles(codigo) {
    const formularioDetalle = document.getElementById(`detail_${codigo}`);
    formularioDetalle.style.display = formularioDetalle.style.display === 'none' ? 'block' : 'none';
}

function mostrarImagenes(codigo) {
    const formularioImagen = document.getElementById(`img_${codigo}`);
    formularioImagen.style.display = formularioImagen.style.display === 'none' ? 'block' : 'none';
    
}






//metodo onsubmit para eliminar imagenes de la lista de productos
function validarSeleccionImgs() {
    const checkboxes = document.querySelectorAll('input[name="seleccionarImagen[]"]:checked');
    if (checkboxes.length === 0) {
        alert("Debes seleccionar al menos una imagen para eliminar.");
        return false;
    }
    const confirmar = confirm("¿Estás seguro de que deseas eliminar la(s) imagen(es) seleccionada(s)?");
    return confirmar;
}