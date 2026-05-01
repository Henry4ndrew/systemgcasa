<div class="accordion-section">
    <div class="accordion-header b-gold f-white f-align" onclick="desplazarPanel('pest1');"><i class="fa-solid fa-1 circle centrar"></i>Datos del cliente</div>
    <div class="accordion-content column" id="pest1">
            <div style="background: rgba(255,255,255,0.2); padding:10px; margin-top:-20px;">
                <div class="input-wrapper" style="width:450px; margin: 0 0 5px 7px;">
                <input class="input padInput" type="text" id="search-client" placeholder="Buscar por nombre de cliente o empresa" oninput="buscarClienteConSugerencias();" onfocus="buscarClienteConSugerencias();">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <div class="item-material listaFlotanteFilas" id="sugerencias-clientes" style="width:100%; display:none; margin-left:7px;"></div>
            </div>
            <?php include 'component-cliente.php'; ?>
    </div>
</div>