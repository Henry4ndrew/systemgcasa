<?php
session_start();
$permiso = $_SESSION['permiso'];
// Mapeo de páginas y permisos
$mapa_permisos = [
    'dashboard.php'         => ['administrador', 'ventas'],
    'ventas_registro.php'   => ['administrador', 'ventas'],
    'ventas_historial.php'  => ['administrador', 'ventas'],
    'stock_productos.php'   => ['administrador', 'ventas'],
    'stock_tienda.php'      => ['administrador', 'ventas'],
    'stock_ferias.php'      => ['administrador', 'ventas'],
    'stock_materiales.php'  => ['administrador', 'ventas'],
    'stock_materiales_historial.php'  => ['administrador', 'ventas'],
    'cotiz_crear.php'       => ['administrador', 'ventas'],
     'cotiz_ver.php'        => ['administrador', 'ventas'],
     'cobros.php'           => ['administrador', 'ventas'],
     'cobros_historial.php' => ['administrador'],
     'art_productos.php'    => ['administrador', 'ventas'],
     'art_materiales.php'   => ['administrador', 'ventas'],
     'clientes.php'         => ['administrador'],
     'usuarios.php'         => ['administrador'],
     'config_options.php'   => ['administrador'],
     'config_data_cotiz.php'=> ['administrador'],
     'config_accounts.php'  => ['administrador'],
     'web_portada.php'      => ['administrador'],
     'web_pdf.php'          => ['administrador'],
     'web_data.php'         => ['administrador']
];

$pagina_actual = isset($_GET['p']) ? $_GET['p'] : 'dashboard.php';

// Validar permisos
if (isset($mapa_permisos[$pagina_actual]) && !in_array($permiso, $mapa_permisos[$pagina_actual])) {
    echo "<h2 class='f-gold'>Acceso Denegado</h2>";
    exit;
}
?>
