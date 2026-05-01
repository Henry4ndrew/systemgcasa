<form action="actions/agregarPdf.php" class="formStyle mediano b-azul" id="formAddPdf" method="POST" enctype="multipart/form-data" onsubmit="return validarImagen('doc_pdf', 'btnGuardarPdf', 'btnEditarPdf')">
    <div class="cabecera">
        <h2 id="txtFormPdf">Agregar Doc. pdf</h2>
        <button type="button" onclick="plop('formAddPdf')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <br>
        <div class="separador">
            <div class="mitad">
                <div class="elem centrar">
                        <label for="doc_pdf" id="areaPdf" class="square column centrar f-white">
                           <i class="fa-solid fa-file-pdf" id="pdfIcon"></i>Seleccionar Pdf *
                        </label>
                    <input type="file" id="doc_pdf" name="doc_pdf" style="display:none;" onchange="cambiarFondoPdf('doc_pdf', 'areaPdf', 'pdfIcon')" accept="application/pdf">
                </div>
            </div>
            <div class="mitad">
                <div class="elem2 column">
                    <label class="f-peq f-white" for="titlePdf">Título:<span class="a">*</span></label>
                    <input class="input pd" id="titlePdf" type="text" name="titlePdf" placeholder="Título del pdf" required>
                </div>
                <div class="elem2 column">
                    <label class="f-peq f-white" for="descPdf">Descripción:</label>
                    <textarea class="input pd rad7" id="descPdf" name="descPdf" rows="4" maxlength="1000"></textarea>
                </div>
            </div>
        </div>
 
    <section class="containerBtns">
         <button type="submit" id="btnGuardarPdf" class="btn-load verde"><span>Guardar Pdf</span></button>
    </section>
</form> 