//===========Web============
function mostrarMiniForm(id) {
    var form = document.getElementById(id).style.display='block';
    limpiarImagen('imagenCli', 'areaImgCli');
}
//no  debe salir de esta hoja esta funcion
function eliminarImagen(id) {
    if (confirm("¿Estás seguro de que quieres eliminar esta imagen?")) {
        window.location.href = 'actions/eliminar_imgCli.php?id=' + id;
    }
}
//========fin data web=======




//========Inicio web doc pdf=======
function mostrarFormPdf(id){
  const form = document.getElementById(id);
  form.style.display='block';
  form.reset();
  resetearFondoPdf('doc_pdf', 'areaPdf', 'pdfIcon')
}
function cambiarFondoPdf(inputId, labelId, iconId) {
    var inputFile = document.getElementById(inputId);
    var label = document.getElementById(labelId);
    var icon = document.getElementById(iconId);
    if (inputFile.files.length > 0) {
        label.style.background = 'var(--gold)';
        icon.classList.remove('fa-file-pdf');
        icon.classList.add('fa-circle-check');
    } else {
        label.style.background = '';
        icon.classList.remove('fa-circle-check');
        icon.classList.add('fa-file-pdf');
    }
}
function resetearFondoPdf(inputId, labelId, iconId) {
    var inputFile = document.getElementById(inputId);
    var label = document.getElementById(labelId);
    var icon = document.getElementById(iconId);
    inputFile.value = "";
    label.style.background = "";
    icon.classList.remove('fa-circle-check');
    icon.classList.add('fa-file-pdf');
}

//========fin docs pdf=======




//========Inicio datos portada=======
function recojerDatosPortada(id, ruta_img, titulo, descripcion) {
    plop('formPortada');

    gestionarBtns('formPortada' , 'editar');
    document.getElementById('txt-formPortada').textContent = "Editar usuario";

    document.getElementById('idPortada').value = id;
    document.getElementById('title').value = titulo;
    document.getElementById('desc').value = descripcion;

    if (ruta_img) {
        let rutaImagenCorrecta = ruta_img.replace(/^(\.\.\/)/, '');
        let areaImg = document.getElementById('areaImg');
        areaImg.innerHTML = `<img src="${rutaImagenCorrecta}" alt="Imagen seleccionada" class="imgSeleccionada">`;
    }
}
//========fin datos portada=======