<?php
// Lista blanca de páginas permitidas
$paginasPermitidas = [
    'dashboard.php' => 'paginas/dashboard.php',
    'ventas_registro.php' => 'paginas/ventas_registro.php',
    'ventas_historial.php' => 'paginas/ventas_historial.php',
    'stock_productos.php' => 'paginas/stock_productos.php',
    'stock_tienda.php' => 'paginas/stock_tienda.php',
    'stock_ferias.php' => 'paginas/stock_ferias.php',
    'stock_materiales.php' => 'paginas/stock_materiales.php',
    'stock_materiales_historial.php' => 'paginas/stock_materiales_historial.php',
    'cotiz_crear.php' => 'paginas/cotiz_crear.php',
    'cotiz_ver.php' => 'paginas/cotiz_ver.php',
    'cobros.php' => 'paginas/cobros.php',
    'cobros_historial.php' => 'paginas/cobros_historial.php',
    'art_productos.php' => 'paginas/art_productos.php',
    'art_materiales.php' => 'paginas/art_materiales.php',
    'clientes.php' => 'paginas/clientes.php',
    'usuarios.php' => 'paginas/usuarios.php',
    'config_options.php' => 'paginas/config_options.php',
    'config_data_cotiz.php' => 'paginas/config_data_cotiz.php',
    'config_accounts.php' => 'paginas/config_accounts.php',
    'web_portada.php' => 'paginas/web_portada.php',
    'web_pdf.php' => 'paginas/web_pdf.php',
    'web_data.php' => 'paginas/web_data.php'
];
$pagina = $_GET['p'] ?? 'dashboard.php';

// Validar contra lista blanca
if (isset($paginasPermitidas[$pagina]) && file_exists($paginasPermitidas[$pagina])) {
    include($paginasPermitidas[$pagina]);
} else {
    http_response_code(404);
}
?>