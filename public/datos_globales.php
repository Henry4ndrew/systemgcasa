<?php
global $datos_empresa;

// Consultar datos de la empresa si no están ya cargados
if (!isset($datos_empresa)) {
    $query = "SELECT facebook, instagram, celular_tienda, celular_fabrica, direccion_fabrica, gps_tienda, gps_fabrica, tiktok, email FROM datos LIMIT 1";
    $result = $conexion->query($query);
    
    if ($result && $result->num_rows > 0) {
        $datos_empresa = $result->fetch_assoc();
    } else {
        $datos_empresa = [];
    }
}

// Función helper para obtener un dato específico
function getDatoEmpresa($key, $default = '') {
    global $datos_empresa;
    return isset($datos_empresa[$key]) && !empty($datos_empresa[$key]) 
        ? htmlspecialchars($datos_empresa[$key]) 
        : $default;
}

// Función para obtener URL de WhatsApp
function getWhatsAppUrl($tipo = 'tienda') {
    global $datos_empresa;
    $numero = ($tipo === 'tienda') 
        ? ($datos_empresa['celular_tienda'] ?? '') 
        : ($datos_empresa['celular_fabrica'] ?? '');
    
    if (!empty($numero)) {
        return 'https://wa.me/591' . preg_replace('/[^0-9]/', '', $numero);
    }
    return '#';
}

// Función para verificar si existe un dato
function existeDatoEmpresa($key) {
    global $datos_empresa;
    return isset($datos_empresa[$key]) && !empty($datos_empresa[$key]);
}
?>