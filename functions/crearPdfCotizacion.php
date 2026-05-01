<?php
// INICIO - CONFIGURACIÓN PARA WINDOWS
session_start();
ob_start(); // IMPORTANTE: Buffer para evitar "Some data has already been output"

// Configurar manejo de errores
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

// Configurar directorios TCPDF
define('K_PATH_MAIN', dirname(__FILE__) . '/../tcpdf/');
define('K_PATH_URL', '');
define('K_PATH_IMAGES', '');

// Directorio temporal específico
$temp_dir = sys_get_temp_dir() . '/tcpdf_cache_' . session_id() . '/';
define('K_PATH_CACHE', $temp_dir);

// Crear y limpiar directorio temporal
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0755, true);
}

// Configuraciones adicionales
define('K_BLANK_IMAGE', '_blank.png');
define('PDF_IMAGE_SCALE_RATIO', 1.25);
define('K_CELL_HEIGHT_RATIO', 1.25);
define('K_TCPDF_THROW_EXCEPTION_ON_ERROR', false); // No lanzar excepciones
define('K_TCPDF_EXTERNAL_CONFIG', true);

// Conexión a BD
require '../includes/conexion.php';

// Verificar ID
if (!isset($_GET['id'])) {
    die("ID de cotización no proporcionado.");
}

$idCotizacion = intval($_GET['id']);

// ========== CONSULTAS A LA BASE DE DATOS ==========

// 1. Obtener datos de la cotización
$query = "SELECT id_cotizacion, titulo, fecha_caducidad, 
                 cuenta_bancaria, fecha_cotizacion 
          FROM cotizaciones WHERE id_cotizacion = $idCotizacion";
$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

if (mysqli_num_rows($resultado) == 0) {
    die("No se encontró la cotización.");
}

$cotizacion = mysqli_fetch_assoc($resultado);

// 2. Obtener datos de la cuenta bancaria
$cuentaBancaria = null;
if (!empty($cotizacion['cuenta_bancaria'])) {
   $idCuenta = $cotizacion['cuenta_bancaria'];
    
    $queryCuenta = "SELECT id, titularCuenta, numeroCuenta, nombreBanco, imagenQR, fechaCaducidadQR
                    FROM cuentas_bancarias 
                    WHERE id = " . intval($idCuenta);
    $resultadoCuenta = mysqli_query($conexion, $queryCuenta);
    
    if ($resultadoCuenta && mysqli_num_rows($resultadoCuenta) > 0) {
        $cuentaBancaria = mysqli_fetch_assoc($resultadoCuenta);
    }
}

// 3. Obtener datos de la venta asociada
$queryVenta = "SELECT id_venta, id_cliente, total_venta, nota, 
                      tipo_descuento, valor_descuento
               FROM ventas 
               WHERE id_cotizacion = $idCotizacion";
$resultadoVenta = mysqli_query($conexion, $queryVenta);

if (!$resultadoVenta) {
    die("Error en la consulta de venta: " . mysqli_error($conexion));
}

$venta = mysqli_num_rows($resultadoVenta) > 0 ? mysqli_fetch_assoc($resultadoVenta) : null;

// 4. Obtener detalles de la venta con imágenes
$detallesVenta = [];
if ($venta) {
    $queryDetalles = "SELECT dv.id_detalle, dv.codigo, dv.newDetail as descripcion, 
                             dv.precio_venta, dv.cantidad, dv.sub_total,
                             lp.caracteristicas, lp.nombre as nombre_producto, dp.medida
                      FROM detalle_venta dv
                      LEFT JOIN lista_productos lp ON dv.codigo = lp.codigo
                      LEFT JOIN detalle_producto dp ON dv.id_detalle = dp.id_detalle
                      WHERE dv.id_venta = ".$venta['id_venta'];
    $resultadoDetalles = mysqli_query($conexion, $queryDetalles);
    
    if ($resultadoDetalles) {
        $detallesVenta = mysqli_fetch_all($resultadoDetalles, MYSQLI_ASSOC);
        
        // Obtener imágenes para cada producto
        foreach ($detallesVenta as &$detalle) {
            $queryImagen = "SELECT ruta_imagen 
                            FROM imagenes 
                            WHERE codigo = '".$detalle['codigo']."' 
                            LIMIT 1";
            $resultadoImagen = mysqli_query($conexion, $queryImagen);
            
            if ($resultadoImagen && mysqli_num_rows($resultadoImagen) > 0) {
                $imagen = mysqli_fetch_assoc($resultadoImagen);
                $detalle['imagen'] = $imagen['ruta_imagen'];
            } else {
                $detalle['imagen'] = null;
            }
        }
    }
}

// 5. Obtener datos del pie de página
$queryCotizacion = "SELECT id_dataPiePag FROM cotizaciones WHERE id_cotizacion = $idCotizacion";
$resultCotizacion = mysqli_query($conexion, $queryCotizacion);

if (!$resultCotizacion || mysqli_num_rows($resultCotizacion) === 0) {
    die("No se encontró la cotización con ID: $idCotizacion");
}

$rowCotizacion = mysqli_fetch_assoc($resultCotizacion);
$idPiePagina = $rowCotizacion['id_dataPiePag'];

$queryPiePagina = "SELECT direccion, direction_tienda, celular_contacto, celular_fabrica, correo, url_firma, url_logo, nombre_firma, cargo_firma
                   FROM pie_pagina_cotizacion 
                   WHERE id = $idPiePagina";
$resultPiePagina = mysqli_query($conexion, $queryPiePagina);

if (!$resultPiePagina || mysqli_num_rows($resultPiePagina) === 0) {
    die("No se encontró la información del pie de página para esta cotización");
}

$rowPiePagina = mysqli_fetch_assoc($resultPiePagina);

// ========== FIN DE CONSULTAS ==========

// Función para formatear fechas
function formatearFecha($fecha) {
    if (empty($fecha) || $fecha == '0000-00-00' || $fecha == '0000-00-00 00:00:00') {
        return 'No especificada';
    }
    return date("d-m-Y", strtotime($fecha));
}

// Función para validar fecha
function esFechaValida($fecha) {
    return !empty($fecha) && $fecha != '0000-00-00' && $fecha != '0000-00-00 00:00:00';
}

// Función para calcular días
function calcularDiasRestantes($fechaInicio, $fechaFin) {
    if (!esFechaValida($fechaInicio) || !esFechaValida($fechaFin)) {
        return false;
    }
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);
    if ($fin < $inicio) {
        return false;
    }
    $diferencia = $inicio->diff($fin);
    return $diferencia->days;
}
function espacioSuficiente($pdf, $alturaNecesaria = 20) {
    return ($pdf->GetY() + $alturaNecesaria) < ($pdf->getPageHeight() - 40);
}



// DESPUÉS DE OBTENER TODOS LOS DATOS, CREAR EL PDF
require '../tcpdf/tcpdf.php';

// Crear PDF con configuración segura
$pdf = new TCPDF('P', 'mm', 'Letter', true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('Sistema de Cotizaciones');
$pdf->SetAuthor('GCasa Club');
$pdf->SetTitle('Cotización ' . $idCotizacion);
$pdf->SetSubject('Cotización');

// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(12); // Espacio para el pie de página
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true); // ACTIVAR PIE DE PÁGINA AUTOMÁTICO

// Configurar fuentes
$pdf->SetFont('helvetica', '', 10);
$pdf->SetAutoPageBreak(true, 25);

// PREPARAR TEXTO DEL PIE DE PÁGINA
$textoPie = '';
if (!empty($rowPiePagina)) {
    $contacto = [];
    if (!empty($rowPiePagina['direccion'])) 
        $contacto[] = htmlspecialchars($rowPiePagina['direccion']);
    if (!empty($rowPiePagina['celular_contacto'])) 
        $contacto[] = 'Cel: ' . htmlspecialchars($rowPiePagina['celular_contacto']);
    if (!empty($rowPiePagina['correo'])) 
        $contacto[] = htmlspecialchars($rowPiePagina['correo']);
    
    if (!empty($contacto)) {
        $textoPie = implode(' | ', $contacto) . ' | www.gcasaclub.com';
    } else {
        $textoPie = 'www.gcasaclub.com';
    }
} else {
    $textoPie = 'www.gcasaclub.com';
}

// CREAR UNA CLASE EXTENDIDA DE TCPDF PARA CONTROLAR EL PIE DE PÁGINA
class MYPDF extends TCPDF {
    private $footerText;
    private $headerText;
    
    public function setFooterText($text) {
        $this->footerText = $text;
    }
    
    public function setHeaderText($text) {
        $this->headerText = $text;
    }
    
    // Page footer
    public function Footer() {
        // Posición a 12 mm del final
        $this->SetY(-12);
        
        // Establecer fuente pequeña
        $this->SetFont('helvetica', '', 6.5);
        $this->SetTextColor(80, 80, 80);
        
        // Línea separadora fina
        $this->SetDrawColor(200, 200, 200);
        $this->Line(15, $this->GetY() - 2, 195, $this->GetY() - 2);
        
        if (!empty($this->footerText)) {
            // Pie de página centrado (línea 1)
            $this->Cell(0, 3, $this->footerText, 0, 0, 'C');
        }
        
        // Número de página y fecha (línea 2)
        $this->Ln(3);
        $pagina = 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $fechaGen = 'Generado: ' . date('d/m/Y H:i');
        $this->Cell(0, 3, $pagina . ' | ' . $fechaGen, 0, 0, 'C');
    }
    
    // Page header (si necesitas header también)
    public function Header() {
        // Puedes agregar header aquí si lo necesitas
    }
}

// USAR LA CLASE PERSONALIZADA EN VEZ DE TCPDF DIRECTAMENTE
$pdf = new MYPDF('P', 'mm', 'Letter', true, 'UTF-8', false);

// Configurar el documento con la nueva clase
$pdf->SetCreator('Sistema de Cotizaciones');
$pdf->SetAuthor('GCasa Club');
$pdf->SetTitle('Cotización ' . $idCotizacion);
$pdf->SetSubject('Cotización');

// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(12);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

// Configurar fuentes
$pdf->SetFont('helvetica', '', 10);
$pdf->SetAutoPageBreak(true, 25);

// ESTABLECER EL TEXTO DEL PIE DE PÁGINA
$pdf->setFooterText($textoPie);

// Agregar página
$pdf->AddPage();

// Función segura para mostrar imágenes (mantener igual)
function mostrarImagenSegura($pdf, $ruta, $x, $y, $ancho, $alto = 0) {
    if (!empty($ruta) && file_exists($ruta)) {
        try {
            $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
            
            if ($extension === 'png') {
                try {
                    $pdf->Image($ruta, $x, $y, $ancho, $alto, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
                } catch (Exception $e) {
                    $imageData = @file_get_contents($ruta);
                    if ($imageData !== false) {
                        $pdf->Image('@' . $imageData, $x, $y, $ancho, $alto, '', '', '', false, 300, '', false, false, 0, false, false, false);
                    }
                }
            } else {
                $pdf->Image($ruta, $x, $y, $ancho, $alto, '', '', '', false, 300, '', false, false, 0, false, false, false);
            }
        } catch (Exception $e) {
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->SetXY($x, $y);
            $pdf->Cell($ancho, $alto, '[Imagen no disponible]', 0, 0, 'C');
            $pdf->SetFont('helvetica', '', 10);
        }
    }
}

function optimizarImagenParaPDF($ruta, $maxWidth = 600, $calidad = 65) {
    if (!file_exists($ruta)) {
        return false;
    }

    $info = getimagesize($ruta);
    if (!$info) {
        return false;
    }

    list($width, $height) = $info;
    $mime = $info['mime'];

    // Crear imagen según tipo
    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($ruta);
            break;
        case 'image/png':
            $src = imagecreatefrompng($ruta);
            break;
        default:
            return false;
    }

    // Redimensionar si es muy grande
    if ($width > $maxWidth) {
        $ratio = $height / $width;
        $newWidth = $maxWidth;
        $newHeight = intval($maxWidth * $ratio);
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Fondo blanco (para PNG con transparencia)
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Capturar salida en memoria
    ob_start();
    imagejpeg($dst, null, $calidad); // Convertimos todo a JPG
    $imageData = ob_get_clean();

    imagedestroy($src);
    imagedestroy($dst);

    return base64_encode($imageData);
}






// MOSTRAR LOGO
if (!empty($rowPiePagina['url_logo'])) {
    mostrarImagenSegura($pdf, $rowPiePagina['url_logo'], 160, 10, 40);
}

// FECHA
$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(10, 10);
$fecha = formatearFecha($cotizacion['fecha_cotizacion']);
$pdf->Write(0, 'Fecha: ' . $fecha);

// NÚMERO DE COTIZACIÓN
$pdf->SetXY(10, 20);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(50, 10, "N° " . $idCotizacion, 0, 1, 'L');
$pdf->Ln(10);

// TÍTULO
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 8, $cotizacion['titulo'], 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetAutoPageBreak(true, 35);


// TABLA DE PRODUCTOS
if (!empty($detallesVenta)) {
    $html = '
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: helvetica;
            font-size: 8.5pt;
            table-layout: fixed;
            page-break-inside: auto !important;
        }
        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }
        th {
            background-color: #ffcc99;
            color: #000;
            font-weight: bold !important; 
            text-align: center;
            padding: 4px;
            border: 1px solid #666;
        }
        td {
            border: 1px solid #666;
        }
        .producto-img {
            max-width: 25mm;
            height: 15mm;
            display: inline-block;
            object-fit:cover;
            margin: 2px 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .codigo-producto {
            color: #666;
            font-size: 6.5pt;
        }
        /* Anchos específicos para columnas */
        .col-producto { width: 17%;}
        .col-descripcion { width: 21%; }
        .col-caracteristicas { width: 30%; }
        .col-precio { width: 11%; }
        .col-cantidad { width: 9%; }
        .col-subtotal { width: 12%; }
    </style>
    
    <table>
        <thead>
            <tr>
                <th class="col-producto">Producto</th>
                <th class="col-descripcion">Descripción</th>
                <th class="col-caracteristicas">Características</th>
                <th class="col-precio">Precio</th>
                <th class="col-cantidad">Cantidad</th>
                <th class="col-subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($detallesVenta as $row) {
        $codigoProducto = htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8'). '<br>';
        $nombreProducto = !empty($row['nombre_producto']) ? 
            htmlspecialchars($row['nombre_producto'], ENT_QUOTES, 'UTF-8') : '';
        
        $medida = htmlspecialchars($row['medida'], ENT_QUOTES, 'UTF-8'). '<br>';
        $descripcion = !empty($row['medida']) ? 
        htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8') : '';


        $caracteristicas = !empty($row['caracteristicas']) ? 
            nl2br(htmlspecialchars($row['caracteristicas'], ENT_QUOTES, 'UTF-8')) : '-';
        

        $imagenHTML = '';
        if (!empty($row['imagen']) && file_exists($row['imagen'])) {
            $tamano = filesize($row['imagen']);
            if ($tamano > 800 * 1024) { // mayor a 800 KB
                $base64 = optimizarImagenParaPDF($row['imagen'], 600, 65);
            } else {
                $base64 = base64_encode(file_get_contents($row['imagen']));
            }
            if ($base64) {
                $imagenHTML = '<img class="producto-img" src="@' . $base64 . '">';
            }
        }

        
        $html .= '
            <tr nobr="true">
                <td class="text-left col-producto" style="font-weight: normal; font-size: 8pt;">
                  <span class="codigo-producto">'. $codigoProducto .'</span>
                  '. $nombreProducto .'
                   <div>' . $imagenHTML . '</div>
                </td>
                <td class="text-left col-descripcion" style="font-weight: normal; font-size: 8pt;">
                   <span style="font-weight: bold; color: #666;">' . $medida . '</span>
                  ' . $descripcion . '
                </td>
                <td class="text-left col-caracteristicas" style="font-weight: normal; font-size: 8pt;"><div style="display: flex; justify-content: center; align-items: center; height: 100%;">'. $caracteristicas . '</div></td>
                <td class="col-precio" style="text-align:center; font-weight: normal;"><br><br><br>Bs '. number_format($row['precio_venta'], 2, ',', '.') . '</td>
                <td class="col-cantidad" style="text-align:center; font-weight: normal;"><br><br><br>' . $row['cantidad'] . '</td>
                <td class="col-subtotal" style="text-align:center; font-weight: normal;"><br><br><br>Bs ' . number_format($row['sub_total'], 2, ',', '.') . '</td>
            </tr>';
    }
    
    $html .= '
        </tbody>
    </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '', true);

// DESDE AQUI ES EL TOTAL===========================================
$pdf->Ln(-5);
$pdf->SetFont('helvetica', '', 9);  // Reducir de 11 a 9

// Total (ya existe, pero con fuente reducida)
$totalCalculado = 0;
foreach ($detallesVenta as $row) {
    $totalCalculado += $row['sub_total'];
}

$pdf->Cell(0, 8, 'Total: Bs '.number_format($totalCalculado, 2, ',', '.'), 0, 1, 'R');  // Reducir de 10 a 8
$pdf->Ln(1);  // Reducir espaciado de 5 a 2

// FECHA DE CADUCIDAD
if (esFechaValida($cotizacion['fecha_caducidad'])) {
    $textoFecha = "Fecha de Caducidad: " . formatearFecha($cotizacion['fecha_caducidad']);
    $diasRestantes = calcularDiasRestantes($cotizacion['fecha_cotizacion'], $cotizacion['fecha_caducidad']);    
    if ($diasRestantes !== false) {
        $diasRestantes += 1;
        $textoFecha .= " ($diasRestantes días)";
    }
    $pdf->SetFont('helvetica', '', 9);  // Reducir de 11 a 9
    $pdf->Cell(0, 8, $textoFecha, 0, 1);  // Reducir de 10 a 8
}

// DATOS DE LA VENTA - CONFIGURACIÓN COMPACTA
if ($venta) {
    // Nota con fuente más pequeña
    if (!empty($venta['nota'])) {
        $pdf->SetFont('helvetica', '', 8);  // Reducir aún más para notas
        // Usar MultiCell más compacto
        $pdf->MultiCell(0, 4, $venta['nota'], 0, 'L');  // Reducir altura de línea
        $pdf->Ln(1);  // Espaciado mínimo
    }
    
    // Descuento con menos espacio
    $pdf->SetFont('helvetica', '', 9);
    if ($venta['tipo_descuento'] && $venta['valor_descuento'] > 0) {
        $pdf->Cell(40, 6, 'Descuento:', 0, 0);  // Reducir ancho y altura
        if ($venta['tipo_descuento'] == 'monto') {
            $pdf->Cell(0, 6, 'Bs ' . number_format($venta['valor_descuento'], 2), 0, 1);
        } elseif ($venta['tipo_descuento'] == 'porcentaje') {
            $pdf->Cell(0, 6, number_format($venta['valor_descuento'], 2) . ' %', 0, 1);
        } else {
            $pdf->Cell(0, 6, number_format($venta['valor_descuento'], 2), 0, 1);
        }
    }
    
    // TOTAL A PAGAR más compacto
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 9);  // Reducir de 11 a 9
    $pdf->Cell(40, 6, 'TOTAL A PAGAR:', 0, 0, '', true);  // Reducir altura
    $pdf->Cell(0, 6, 'Bs ' . number_format($venta['total_venta'], 2), 0, 1, '', true);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Ln(2);
}

// ========== FIRMA DIGITAL COMPACTA ==========
if (!empty($rowPiePagina['url_firma'])) {
    // Verificar espacio disponible para firma (más exigente)
    $espacioNecesarioFirma = 30; // Reducido de ~40-50
    
    // Si hay poco espacio, agregar nueva página
    if ($pdf->GetY() > ($pdf->getPageHeight() - $espacioNecesarioFirma - 25)) {
        $pdf->AddPage();
    }
    
    // Configurar firma más compacta
    $ancho_firma = 35;  // Reducir ancho
    $alto_firma = 12;   // Reducir alto de imagen
    
    // Posición centrada pero más compacta
    $x = ($pdf->GetPageWidth() - $ancho_firma) / 2;
    $y = $pdf->GetY();
    
    // Espacio mínimo antes de la firma
    $pdf->Ln(2);
    $y = $pdf->GetY();
    
    mostrarImagenSegura($pdf, $rowPiePagina['url_firma'], $x, $y, $ancho_firma, $alto_firma);
    
    // Texto de firma más compacto
    $pdf->SetFont('helvetica', 'B', 7);  // Reducir tamaño
    $pdf->SetXY($x, $y + $alto_firma + 1);  // Reducir espacio
    $pdf->Cell($ancho_firma, 3, $rowPiePagina['nombre_firma'], 0, 1, 'C');
    $pdf->SetX($x);
    $pdf->SetFont('helvetica', '', 6.5);  // Fuente más pequeña para cargo
    $pdf->Cell($ancho_firma, 3, $rowPiePagina['cargo_firma'], 0, 0, 'C');
    $pdf->Ln(3);  // Reducir espaciado posterior
}






// ========== CUENTA BANCARIA MÁS COMPACTA ==========
if (!empty($cuentaBancaria)) {

    $espacioNecesarioCuenta = 40;
    if ($pdf->GetY() > ($pdf->getPageHeight() - $espacioNecesarioCuenta - 25)) {
        $pdf->AddPage();
    }

    $htmlCuenta = '
    <div style="background-color:#ffefb8; border:1px solid #b4b4b4; padding:2mm; width:60mm; margin:0 auto;">
        <table cellpadding="0" cellspacing="0" style="width:100%; font-size:7.5pt;">
            <tr>';

    /* QR IZQUIERDA */
    if (!empty($cuentaBancaria['imagenQR']) && file_exists($cuentaBancaria['imagenQR'])) {
        $qrData = @file_get_contents($cuentaBancaria['imagenQR']);
        if ($qrData !== false) {
            $base64 = base64_encode($qrData);
            $htmlCuenta .= '
                <td style="width:30mm; text-align:center; vertical-align:middle;">
                    <div><img src="@' . $base64 . '" width="28mm" height="28mm"></div>
                </td>';
        }
    }

    /* DATOS DERECHA */
    $htmlCuenta .= '
                <td style="vertical-align:middle;">
                    <div><b>&nbsp;&nbsp;Titular:</b> ' . htmlspecialchars($cuentaBancaria['titularCuenta']) . '</div>
                    <div><b>&nbsp;&nbsp;N° cuenta:</b> ' . htmlspecialchars($cuentaBancaria['numeroCuenta']) . '</div>
                    <div><b>&nbsp;&nbsp;Banco:</b> ' . htmlspecialchars($cuentaBancaria['nombreBanco']) . '</div>';
    if (!empty($cuentaBancaria['fechaCaducidadQR'])) {
        $htmlCuenta .= '
                    <div><b>&nbsp;&nbsp;Válido hasta:</b> ' . htmlspecialchars($cuentaBancaria['fechaCaducidadQR']) . '</div>';
    }

    $htmlCuenta .= '
                </td>
            </tr>
        </table>
    </div>';

    $pdf->Ln(2);
    $pdf->writeHTML($htmlCuenta, true, false, true, false, '');
    $pdf->Ln(1);
}


// ========== FUNCIÓN PARA VERIFICAR ESPACIO ==========
// Agregar esta función si no existe
function hayEspacioSuficiente($pdf, $alturaNecesaria = 30) {
    $margenInferior = $pdf->getFooterMargin();
    $alturaPagina = $pdf->getPageHeight();
    $posicionActual = $pdf->GetY();
    
    // Dejar al menos 25mm para el pie de página
    $espacioDisponible = $alturaPagina - $posicionActual - $margenInferior - 25;
    
    return $espacioDisponible >= $alturaNecesaria;
}
}
// LIMPIAR BUFFER Y ENVIAR PDF
ob_end_clean();

// Enviar PDF al navegador
$pdf->Output("Cotizacion_{$idCotizacion}.pdf", 'D');

// Limpiar archivos temporales después de enviar
function limpiarTemporales() {
    $temp_dir = sys_get_temp_dir() . '/tcpdf_cache_' . session_id() . '/';
    if (file_exists($temp_dir)) {
        $files = glob($temp_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        @rmdir($temp_dir);
    }
}

register_shutdown_function('limpiarTemporales');

mysqli_close($conexion);
exit;