<?php
// CONFIGURACIÓN INICIAL
session_start();
ob_start(); // Buffer para evitar errores de salida

// Configurar zona horaria de Bolivia
date_default_timezone_set('America/La_Paz'); // Zona horaria de Bolivia

// Configurar manejo de errores
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

// Configurar directorios TCPDF
define('K_PATH_MAIN', dirname(__FILE__) . '/../tcpdf/');
define('K_PATH_URL', '');
define('K_PATH_IMAGES', '');

// Directorio temporal
$temp_dir = sys_get_temp_dir() . '/tcpdf_cache_' . session_id() . '/';
define('K_PATH_CACHE', $temp_dir);

// Crear directorio temporal si no existe
if (!file_exists($temp_dir)) {
    mkdir($temp_dir, 0755, true);
}

// Configuraciones adicionales de TCPDF
define('K_BLANK_IMAGE', '_blank.png');
define('PDF_IMAGE_SCALE_RATIO', 1.25);
define('K_CELL_HEIGHT_RATIO', 1.25);
define('K_TCPDF_THROW_EXCEPTION_ON_ERROR', false);
define('K_TCPDF_EXTERNAL_CONFIG', true);

// Conexión a la base de datos
require '../includes/conexion.php';

// Verificar si se enviaron clientes seleccionados
if (!isset($_POST['clientes_seleccionados']) || empty($_POST['clientes_seleccionados'])) {
    die('No se seleccionaron clientes para exportar.');
}

// Decodificar los IDs de clientes
$ids_clientes = json_decode($_POST['clientes_seleccionados'], true);

if (empty($ids_clientes)) {
    die('No se seleccionaron clientes válidos.');
}

// Crear consulta con los IDs seleccionados y filtro de estado = 'activo'
$placeholders = implode(',', array_fill(0, count($ids_clientes), '?'));
$sql = "SELECT id_cliente, nombre, nit, carnet_ci, departamento, celular,
        cel_empresa, correo, empresa, nota, fecha_registro,
        DATE(fecha_registro) AS solo_fecha,
        TIME(fecha_registro) AS solo_hora
        FROM cartera_clientes 
        WHERE id_cliente IN ($placeholders) 
        AND estado = 'activo'
        ORDER BY fecha_registro DESC";

$stmt = $conexion->prepare($sql);

// Vincular parámetros
if (count($ids_clientes) > 0) {
    $types = str_repeat('i', count($ids_clientes));
    $stmt->bind_param($types, ...$ids_clientes);
    $stmt->execute();
    $result = $stmt->get_result();
    $clientes = [];

    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
} else {
    $clientes = [];
}

// INCLUIR TCPDF
require '../tcpdf/tcpdf.php';

// Función para formatear fecha en formato boliviano
function fechaBoliviana($fecha = null) {
    if ($fecha === null) {
        $fecha = time();
    }
    // Formato: día-mes-año (boliviano)
    return date('d/m/Y', $fecha);
}

// Función para formatear hora boliviana
function horaBoliviana($fecha = null) {
    if ($fecha === null) {
        $fecha = time();
    }
    // Formato: 24 horas
    return date('H:i', $fecha);
}

// Función para obtener fecha y hora completa boliviana
function fechaHoraBoliviana($fecha = null) {
    if ($fecha === null) {
        $fecha = time();
    }
    return date('d/m/Y H:i:s', $fecha);
}

// Fecha actual de Bolivia para usar en el PDF
$fecha_actual_bolivia = fechaHoraBoliviana();
$solo_fecha_bolivia = fechaBoliviana();
$solo_hora_bolivia = horaBoliviana();

// Crear una clase personalizada para el pie de página
class ClientesPDF extends TCPDF {
    private $empresaNombre = "GCasa Club";
    private $paginaActual = 1;
    
    // Configurar cabecera
    public function Header() {
        // Logo
        if (file_exists('../assets/logo.png')) {
            $this->Image('../assets/logo.png', 10, 10, 30, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Título
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 15, 'LISTA DE CLIENTES', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        // Fecha de Bolivia
        $this->SetFont('helvetica', '', 9);
        $fecha_hora_bolivia = date('d/m/Y H:i', strtotime('now'));
        $this->Cell(0, 15, $fecha_hora_bolivia . '', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        
        // Línea separadora
        $this->Line(10, 30, 200, 30);
        
        $this->SetY(35);
    }
    
    // Configurar pie de página
        public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        
        // Pie de página centrado SIN hora boliviana
        $pie = 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $this->Cell(0, 10, $pie, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Crear PDF
$pdf = new ClientesPDF('P', 'mm', 'Letter', true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('GCasa Club CRM - Bolivia');
$pdf->SetAuthor('GCasa Club');
$pdf->SetTitle('Lista de Clientes - Bolivia');
$pdf->SetSubject('Reporte de Clientes Activos');

// Configurar márgenes
$pdf->SetMargins(15, 40, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

// Configurar fuente por defecto
$pdf->SetFont('helvetica', '', 10);
$pdf->SetAutoPageBreak(true, 25);

// Agregar una página
$pdf->AddPage();


// Crear tabla SIN columna de estado
$html = '
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: helvetica;
        font-size: 9pt;
        margin-top: 10px;
    }
    th {
        background-color: #ffcc99;
        color: #000;
        font-weight: bold;
        padding: 8px;
        border: 1px solid #666;
        text-align: center;
    }
    td {
        padding: 6px;
        border: 1px solid #666;
        text-align: left;
        vertical-align: top;
    }
    .numero {
        text-align: center;
        width: 30px;
        font-weight: bold;
    }
    .fecha {
        text-align: center;
        font-size: 8pt;
        color: #666;
    }
    .contacto {
        font-size: 8.5pt;
    }
    .identificacion {
        font-size: 8.5pt;
        color: #555;
    }
    .empresa {
        font-weight: bold;
        color: #333;
    }
</style>

<table>
    <thead>
        <tr>
            <th class="numero">#</th>
            <th>Nombre del Cliente</th>
            <th>Identificación</th>
            <th>Contacto</th>
            <th>Empresa</th>
            <th>Departamento</th>
            <th>Fecha de Registro</th> <!-- QUITADO class="fecha" -->
        </tr>
    </thead>
    <tbody>';

$contador = 1;
foreach ($clientes as $cliente) {
    // Determinar tipo de identificación
    $identificacion = '';
    if (!empty($cliente['nit'])) {
        $identificacion = 'NIT: ' . htmlspecialchars($cliente['nit']);
    } elseif (!empty($cliente['carnet_ci'])) {
        $identificacion = 'CI: ' . htmlspecialchars($cliente['carnet_ci']);
    } else {
        $identificacion = 'No especificada';
    }
    
    // Contacto - formato mejorado
    $contacto = '';
    $celulares = [];
    
    if (!empty($cliente['celular'])) {
        $celulares[] = 'Personal: ' . htmlspecialchars($cliente['celular']);
    }
    
    if (!empty($cliente['cel_empresa'])) {
        $celulares[] = 'Empresa: ' . htmlspecialchars($cliente['cel_empresa']);
    }
    
    if (!empty($celulares)) {
        $contacto .= implode('<br>', $celulares);
    }
    
    if (!empty($cliente['correo'])) {
        $contacto .= ($contacto ? '<br><br>' : '') . htmlspecialchars($cliente['correo']);
    }
    
    if (empty($contacto)) {
        $contacto = 'No especificado';
    }
    
    // Empresa
    $empresa = !empty($cliente['empresa']) ? htmlspecialchars($cliente['empresa']) : 'Sin empresa';
    
    // Departamento
    $departamento = !empty($cliente['departamento']) ? htmlspecialchars($cliente['departamento']) : 'No especificado';
    
    // Fecha formateada - mostrar fecha original del registro
    $fecha_registro = date('d/m/Y', strtotime($cliente['fecha_registro']));
    $hora_registro = date('H:i', strtotime($cliente['fecha_registro']));
    
    $html .= '
        <tr nobr="true">
            <td class="numero">' . $contador++ . '</td>
            <td><strong>' . htmlspecialchars($cliente['nombre']) . '</strong></td>
            <td class="identificacion">' . $identificacion . '</td>
            <td class="contacto">' . $contacto . '</td>
            <td class="empresa">' . $empresa . '</td>
            <td>' . $departamento . '</td>
            <td class="fecha">' . $fecha_registro . '<br><small>' . $hora_registro . '</small></td>
        </tr>';
}

$html .= '
    </tbody>
</table>';

// Escribir la tabla
$pdf->writeHTML($html, true, false, true, false, '');

// Agregar información adicional y resumen
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 8, 'RESUMEN DEL REPORTE', 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 6, 'Total de clientes activos:', 0, 0);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 6, count($clientes), 0, 1);

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 6, 'Fecha de generación:', 0, 0);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 6, $fecha_actual_bolivia . '', 0, 1);

// Información sobre la zona horaria
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 4, 'Notas:', 0, 'L');
$pdf->SetFont('helvetica', 'I', 8);
$pdf->MultiCell(0, 4, '• Este reporte incluye únicamente clientes con estado activo seleccionados en el sistema GCasa Club CRM.', 0, 'L');

// Mostrar información de la zona horaria actual
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(0, 100, 0);
$zona_horaria = date_default_timezone_get();
$diferencia_utc = date('P'); // Diferencia en formato +hh:mm

// Limpiar buffer y enviar PDF
ob_end_clean();

// Nombre del archivo con fecha boliviana
$nombre_archivo = 'clientes_activos' . date('Ymd_His', strtotime('now')) . '.pdf';

// Enviar PDF al navegador
$pdf->Output($nombre_archivo, 'D');

// Limpiar archivos temporales
function limpiarTemporalesClientes() {
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

register_shutdown_function('limpiarTemporalesClientes');

// Cerrar conexión
$stmt->close();
$conexion->close();
exit;