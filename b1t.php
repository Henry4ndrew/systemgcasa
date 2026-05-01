
<body>


<?php include 'header.php' ?>

<!-- Cuerpo y barra lateral con boton flotante -->
<div class="corp">
    <button class="toggle-btn centrar b-azul" onclick="toggleLateral()"><i class="fa-solid fa-chevron-right"></i></button>
           
    <section class="lateral">
      <nav class="menu-nav">
<?php if ($permiso === 'administrador' || $permiso === 'ventas'): ?>
        <button class="btn-menu" id="btn-dashboard" onclick="cargarPagina('dashboard.php')" data-page="dashboard">
            <i class="fa-solid fa-chart-pie f-gold"></i>
             <p class="f-move">Dashboard</p>
        </button>
      

        <button class="btn-menu" onclick="verSubmenu('ventasSubMenu')">
            <i class="fa-solid fa-cart-shopping f-gold"></i>
                <p class="f-move">Ventas</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-ventasSubMenu"></i>
        </button>
        <div class="subMenu" id="ventasSubMenu">
            <button class="btn-menu" id="btn-ventas_registro" onclick="cargarPagina('ventas_registro.php')" data-page="ventas_registro">
                <i class="fa-solid fa-cart-shopping f-gold"></i>
                <p class="f-move">Registrar venta</p>
            </button>
        
          
            <button class="btn-menu" id="btn-ventas_historial" onclick="cargarPagina('ventas_historial.php')" data-page="ventas_historial">
                <i class="fa-solid fa-cart-shopping f-gold"></i>
                <p class="f-move">Historial de ventas</p>
            </button>         
     
        </div>

    
        <button class="btn-menu" onclick="verSubmenu('almacenSubMenu')">
            <i class="fa-solid fa-warehouse f-gold"></i>
            <p class="f-move">Almacén stock</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-almacenSubMenu"></i>
        </button>
        <div class="subMenu" id="almacenSubMenu">
            <button class="btn-menu" id="btn-stock_productos" onclick="cargarPagina('stock_productos.php')" data-page="stock_productos">
                <i class="fa-solid fa-warehouse f-gold"></i>
                <p class="f-move">Stock Fábrica</p>
            </button>
            <button class="btn-menu" id="btn-stock_tienda" onclick="cargarPagina('stock_tienda.php')" data-page="stock_tienda">
                <i class="fa-solid fa-warehouse f-gold"></i>
                <p class="f-move">Stock Tienda</p>
            </button>
            <button class="btn-menu" id="btn-stock_ferias" onclick="cargarPagina('stock_ferias.php')" data-page="stock_ferias">
                <i class="fa-solid fa-warehouse f-gold"></i>
                <p class="f-move">Stock Ferias</p>
            </button>
            <button class="btn-menu" id="btn-stock_materiales" onclick="cargarPagina('stock_materiales.php')" data-page="stock_materiales">
                <i class="fa-solid fa-warehouse f-gold"></i>
                <p class="f-move">Stock materiales</p>
            </button>         
        </div>


        <button class="btn-menu" onclick="verSubmenu('cotizacionSubMenu')">
            <i class="fa-solid fa-file-lines f-gold"></i>
            <p class="f-move">Cotizaciones</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-cotizacionSubMenu"></i>
        </button>
        <div class="subMenu" id="cotizacionSubMenu">
            <button class="btn-menu" id="btn-cotiz_crear" onclick="cargarPagina('cotiz_crear.php')" data-page="cotiz_crear">
                <i class="fa-solid fa-file-lines f-gold"></i>
                <p class="f-move">Crear cotizaciones</p>
            </button>
            <button class="btn-menu" id="btn-cotiz_ver" onclick="cargarPagina('cotiz_ver.php')" data-page="cotiz_ver">
                <i class="fa-solid fa-file-lines f-gold"></i>
                <p class="f-move">Ver cotizaciones</p>
            </button>         
        </div>
<?php endif; ?>



<?php if ($permiso === 'administrador' || $permiso === 'ventas'): ?>
        <button class="btn-menu" onclick="verSubmenu('cobrosSubMenu')">
            <i class="fa-solid fa-money-bill-wave f-gold"></i>
            <p class="f-move">Cobros</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-cobrosSubMenu"></i>
        </button>
        <div class="subMenu" id="cobrosSubMenu">
            <button class="btn-menu" id="btn-cobros" onclick="cargarPagina('cobros.php')" data-page="cobros">
                <i class="fa-solid fa-money-bill-wave f-gold"></i>
                <p class="f-move">Cobros pend.</p>
            </button>
<?php endif; ?> 
<?php if ($permiso === 'administrador'): ?>
            <button class="btn-menu" id="btn-cobros_historial" onclick="cargarPagina('cobros_historial.php')" data-page="cobros_historial">
                <i class="fa-solid fa-money-bill-wave f-gold"></i>
                <p class="f-move">Hist. cobros</p>
            </button>    
<?php endif; ?>     
        </div>

<?php if ($permiso === 'administrador' || $permiso === 'ventas'): ?>
        <button class="btn-menu" onclick="verSubmenu('articulosSubMenu')">
            <i class="fa-solid fa-box-open f-gold"></i>
            <p class="f-move">Arículos</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-articulosSubMenu"></i>
        </button>
        <div class="subMenu" id="articulosSubMenu">
            <button class="btn-menu" id="btn-art_productos" onclick="cargarPagina('art_productos.php')" data-page="art_productos">
                <i class="fa-solid fa-box-open f-gold"></i>
                <p class="f-move">Productos</p>
            </button>
            <button class="btn-menu" id="btn-art_materiales" onclick="cargarPagina('art_materiales.php')" data-page="art_materiales">
                <i class="fa-solid fa-box-open f-gold"></i>
                <p class="f-move">Materia prima</p>
            </button>         
        </div>
<?php endif; ?>  
<?php if ($permiso === 'administrador'): ?>
            <button class="btn-menu" id="btn-clientes" onclick="cargarPagina('clientes.php')" data-page="clientes">
                <i class="fa-solid fa-user-tie f-gold"></i>
                <p class="f-move">Clientes</p>
            </button>

            <button class="btn-menu" id="btn-usuarios" onclick="cargarPagina('usuarios.php')" data-page="usuarios">
                <i class="fa-solid fa-users f-gold"></i>
                <p class="f-move">Usuarios</p>
            </button>

        <button class="btn-menu" onclick="verSubmenu('configSubMenu')">
            <i class="fa-solid fa-gear f-gold"></i>
            <p class="f-move">Configuración</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-configSubMenu"></i>
        </button>
        <div class="subMenu" id="configSubMenu">
            <button class="btn-menu" id="btn-config_options" onclick="cargarPagina('config_options.php')" data-page="config_options">
                <i class="fa-solid fa-gear f-gold"></i>
                <p class="f-move">Agregar opciones</p>
            </button>
            <button class="btn-menu" id="btn-config_data_cotiz" onclick="cargarPagina('config_data_cotiz.php')" data-page="config_data_cotiz">
                <i class="fa-solid fa-gear f-gold"></i>
                <p class="f-move">Datos cotización</p>
            </button>  
            <button class="btn-menu" id="btn-config_accounts" onclick="cargarPagina('config_accounts.php')" data-page="config_accounts">
                <i class="fa-solid fa-gear f-gold"></i>
                <p class="f-move">Cuentas bancarias</p>
            </button>          
        </div>


        <button class="btn-menu" onclick="verSubmenu('webSubMenu')">
             <i class="fa-solid fa-globe f-gold"></i>
            <p class="f-move">Página web</p>
            <i class="fas fa-chevron-down menu-flecha" id="flecha-webSubMenu"></i>
        </button>
        <div class="subMenu" id="webSubMenu">
            <button class="btn-menu" id="btn-web_portada" onclick="cargarPagina('web_portada.php')" data-page="web_portada">
                 <i class="fa-solid fa-globe f-gold"></i>
                <p class="f-move">Portada</p>
            </button>
            <button class="btn-menu" id="btn-web_pdf" onclick="cargarPagina('web_pdf.php')" data-page="web_pdf">
                 <i class="fa-solid fa-globe f-gold"></i>
                <p class="f-move">Documentos pdf</p>
            </button>  
            <button class="btn-menu" id="btn-web_data" onclick="cargarPagina('web_data.php')" data-page="web_data">
                 <i class="fa-solid fa-globe f-gold"></i>
                <p class="f-move">Datos empresa</p>
            </button>          
        </div>
<?php endif; ?>
    </nav>
    </section>

    <main class="cuerpo" id="contenido">
        <!-- Aquí se cargará el contenido dinámico -->
        <p>Bienvenida al panel de administración.</p>
    </main>
</div>




<style>
/* inicio de todo */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  overflow-x: hidden;
  visibility: hidden;
   font-family: 'Poppins', sans-serif;
}


/* boton del lateral */
.toggle-btn {
  position: fixed;
  left: 0;
  top: 70px;
  width: 20px;
  height: 70px;
  border: none;
  border-radius: 0 30px 30px 0;
  cursor: pointer;
  z-index: 4;
  box-shadow:var(--s2);
  transition: left 0.3s ease;
  color:white;
}
.toggle-btn:hover {
  background:var(--orange-meta);
}
.toggle-btn.mostrado {
  left: 220px;   /* o 220px*/
}


.menu-nav{
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0;
}
.lateral {
  box-sizing:border-box;
  background: var(--blue-meta);
  position: fixed;
  top: 60px;
  left: -220px;
  width: 220px;
  height: calc(100vh - 60px);
  padding: 1rem;
  display:flex;
  flex-direction:column;
  overflow-y: auto;
  z-index: 4;
  transition: left 0.3s ease;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}
.lateral.mostrado {
  left: 0;
}
.cuerpo {
  position:relative;
  margin-top:60px;
  width:100%;
  padding-left:25px;
  padding-top:10px;
  height: calc(100vh - 60px);
  transition: margin-left 0.3s ease;
  overflow-x:auto;
}
.cuerpo.completo {
  margin-left: 220px;
}
.corp {
  display: flex;
}






.btn-menu{
    position: relative;
    display:flex;
    padding:10px;
    margin:0;
    border:none;
    background: rgba(2, 2, 2, 0.2);
     backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.3s ease;
    margin-bottom:2px;
    width:100%;
}
.btn-menu i{
  font-size:1rem;
}
.btn-menu:hover{
    background:var(--orange-meta);
}

.pintado{
   background:var(--orange-meta);
   text-shadow:var(--txt-sh);
   color:white;
    box-shadow: var(--s2);
    width:100%;
}
.menu-flecha {
  position: absolute;
  inset-inline-end: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.8rem;
  transition: transform 0.2s ease;
  color:white;
}
.menu-flecha.girar {
  transform: translateY(-50%) rotate(180deg);
}

/* Estilos básicos para los botones del submenú */
.subMenu button {
    padding: 8px 15px;
    margin: 2px 0;
    text-align: left;
    cursor: pointer;
}
.subMenu {
    max-height: 0;
    overflow: hidden;
    padding-left:5px;
    display: flex;
    flex-direction: column;
    transition: max-height 0.3s ease;
    background: rgba(12, 9, 54, 0.5);
}

.subMenu.active {
    display: flex;
    max-height: 200px;
}










.img-dentro-tabla{
    width:120px;
}
.tabla-detalles{
    font-size:0.8rem;
    padding-top:-10px;
}
.tabla-detalles thead, .tablaStyle thead {
  position: sticky;
  top: -10px;
  z-index: 2;
}
.Bs{
    color: #14b83aff;
    font-weight:500;
}

.f-center{
   text-align:center;
}
.userBig{
    font-size:4rem;
    display:flex;
    margin-bottom:20px;
    justify-content:center;
}




.txtArea-none{
  max-height:90px;
  overflow-y:auto;
  background:#f0f0f0;
  margin:3px;
}
.txtArea-none:focus{
  outline: none; /* quita el borde azul de focus en la mayoría de navegadores */
  box-shadow: none;
}
</style>

    
<!-- AAqui van los scripts -->
<script src="js/lateral18.js"></script>
<script src="js/global14.js"></script>
<?php $ventaCotizVer = file_exists(__DIR__ . '/js/venta-cotiz13.js') ? filemtime(__DIR__ . '/js/venta-cotiz13.js') : time(); ?>
<script src="js/venta-cotiz13.js?v=<?php echo $ventaCotizVer; ?>"></script>
<script src="js/buscadores4.js"></script>
<script src="js/hist-venta4.js"></script>
<script src="js/stock-material-product22.js"></script>
<script src="js/cobros.js"></script>
<script src="js/prod2.js"></script>
<script src="js/usr-client3.js"></script>
<script src="js/web3.js"></script>































</body>
</html>