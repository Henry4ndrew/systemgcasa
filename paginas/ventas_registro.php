<?php include 'includes/permisos.php' ?>
<?php include 'forms/acordeonVenta.php' ?>


<h3 class="b-naranja f-white pad-left20">Registrar venta</h3>
<div class="b-azul pad20 flex-between">
    <div class="search-box cont-elemts">
        <div class="input-wrapper">
            <input class="input padInput" type="text" id="search-input" oninput="buscarProducto('search-input','resultadoBusqueda')" placeholder="Ingrese nombre o código">
                <i class="fa-solid fa-magnifying-glass"></i>
        </div>

        <button class="btn-load orange" onclick="mostrarFormAcodeonExistente(); limpiarFormAcordeon();">
           <span>Datos de la venta</span>
        </button>
    </div>


    <!-- Scanner de código de barras via COM Port -->
    <div class="scanner-box" id="scannerBox"">
        <div class="scanner-panel">
            <div class="scanner-header">
                <i class="fa-solid fa-barcode scanner-icon"></i>
                <div class="scanner-info">
                    <span class="scanner-title">Scanner COM</span>
                    <div class="scanner-status" id="scannerStatus">
                        <span class="scanner-dot" id="scannerDot"></span>
                        <span class="scanner-text" id="scannerText">Desconectado</span>
                    </div>
                </div>
            </div>
            <div class="scanner-actions">
                <button type="button" class="btn-scanner conectar" id="btnConectarScanner" onclick="conectarScannerCOM()">
                    <i class="fa-solid fa-plug"></i> Conectar
                </button>
                <button type="button" class="btn-scanner desconectar" id="btnDesconectarScanner" onclick="desconectarScannerCOM()" style="display:none;">
                    <i class="fa-solid fa-plug-circle-xmark"></i> Desconectar
                </button>
            </div>
        </div>
        <div class="scanner-last-scan" id="lastScanDisplay" style="display:none;">
            <i class="fa-solid fa-check-circle"></i>
            <span id="lastScanText"></span>
        </div>
    </div>
</div>

<!-- Estilos del scanner COM -->
<style>
.scanner-box {
    width: 350px;
}
.scanner-panel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: linear-gradient(135deg, #0d1b2a, #1b2838);
    border: 2px solid #455a64;
    border-radius: 12px;
    padding: 10px 18px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3), inset 0 0 20px rgba(0, 0, 0, 0.3);
    transition: all 0.4s ease;
}
.scanner-panel.conectado {
    border-color: #00e676;
    box-shadow: 0 0 20px rgba(0, 230, 118, 0.2), inset 0 0 20px rgba(0, 0, 0, 0.3);
    animation: scannerPulse 3s ease-in-out infinite;
}
.scanner-header {
    display: flex;
    align-items: center;
    gap: 12px;
}
.scanner-icon {
    font-size: 2rem;
    color: #546e7a;
    transition: all 0.4s ease;
}
.scanner-panel.conectado .scanner-icon {
    color: #00e676;
    text-shadow: 0 0 15px rgba(0, 230, 118, 0.6);
    animation: scannerIconGlow 2s ease-in-out infinite alternate;
}
.scanner-info {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.scanner-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #b0bec5;
    letter-spacing: 0.5px;
}
.scanner-panel.conectado .scanner-title {
    color: #e0f2f1;
}
.scanner-status {
    display: flex;
    align-items: center;
    gap: 6px;
}
.scanner-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #546e7a;
    transition: all 0.3s ease;
}
.scanner-dot.conectado {
    background: #00e676;
    box-shadow: 0 0 8px rgba(0, 230, 118, 0.7);
    animation: dotBlink 1.5s ease-in-out infinite;
}
.scanner-dot.scanning {
    background: #ffc107;
    box-shadow: 0 0 8px rgba(255, 193, 7, 0.7);
    animation: dotBlink 0.3s ease-in-out infinite;
}
.scanner-dot.success {
    background: #00e676;
    box-shadow: 0 0 12px rgba(0, 230, 118, 0.9);
    animation: none;
}
.scanner-dot.error {
    background: #ff5252;
    box-shadow: 0 0 8px rgba(255, 82, 82, 0.7);
    animation: none;
}
.scanner-text {
    font-size: 0.75rem;
    color: #78909c;
    font-weight: 500;
}
.scanner-panel.conectado .scanner-text {
    color: #80cbc4;
}
.scanner-actions {
    display: flex;
    gap: 8px;
}
.btn-scanner {
    padding: 8px 18px;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}
.btn-scanner.conectar {
    background: linear-gradient(135deg, #00c853, #00e676);
    color: #0d1b2a;
    box-shadow: 0 2px 8px rgba(0, 230, 118, 0.3);
}
.btn-scanner.conectar:hover {
    box-shadow: 0 4px 15px rgba(0, 230, 118, 0.5);
    transform: translateY(-1px);
}
.btn-scanner.desconectar {
    background: linear-gradient(135deg, #d32f2f, #ff5252);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 82, 82, 0.3);
}
.btn-scanner.desconectar:hover {
    box-shadow: 0 4px 15px rgba(255, 82, 82, 0.5);
    transform: translateY(-1px);
}
.scanner-last-scan {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    margin-top: 5px;
    background: rgba(0, 230, 118, 0.1);
    border: 1px solid rgba(0, 230, 118, 0.3);
    border-radius: 8px;
    color: #a5d6a7;
    font-size: 0.8rem;
    animation: fadeInScan 0.3s ease;
}
.scanner-last-scan i {
    color: #00e676;
}
@keyframes fadeInScan {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes scannerPulse {
    0%, 100% { border-color: #00e676; }
    50% { border-color: #00c853; }
}
@keyframes scannerIconGlow {
    0% { text-shadow: 0 0 10px rgba(0, 230, 118, 0.3); }
    100% { text-shadow: 0 0 20px rgba(0, 230, 118, 0.8); }
}
@keyframes dotBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
@media (max-width: 768px) {
    .scanner-panel {
        flex-wrap: wrap;
        justify-content: center;
    }
    .scanner-actions {
        width: 100%;
        justify-content: center;
    }
}



@media (max-width: 768px) {
    .scanner-box {
        max-width: 280px;
    }

    .scanner-panel {
        padding: 8px 12px;
        gap: 8px;
        border-radius: 10px;
    }

    .scanner-icon {
        font-size: 1.5rem;
    }

    .scanner-title {
        font-size: 0.75rem;
    }

    .scanner-text {
        font-size: 0.65rem;
    }

    .btn-scanner {
        padding: 6px 12px;
        font-size: 0.7rem;
    }

    .scanner-actions {
        width: 100%;
        justify-content: center;
    }

    .scanner-last-scan {
        font-size: 0.7rem;
        padding: 5px 10px;
    }
}
</style>




<div class="panelProductos wrap" id="resultadoBusqueda"></div>



<!-- Formulario para cargar los detallles de un producto -->
<form class="formStyle b-azul mediano" id="formDetails" style="z-index:3;">
    <div class="cabecera">
        <h2 id="txtFormDetails" class="f-center"></h2>
        <button type="button" onclick="plop('formDetails')">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <section class="campoForm" id="detallesEncontrados"> </section>
    <br>
</form>















