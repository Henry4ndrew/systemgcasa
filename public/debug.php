<?php
require '../includes/conexion.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Debug de Base de Datos</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 20px;
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
        }
        h4 {
            color: #666;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .warning {
            color: orange;
            font-weight: bold;
        }
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #2ecc71;
            color: white;
        }
        .badge-warning {
            background: #f39c12;
            color: white;
        }
        .badge-danger {
            background: #e74c3c;
            color: white;
        }
        .stats {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .stats p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔍 Debug de Base de Datos - Sistema GCasaClub</h2>";

// ==================== 1. VERIFICAR PRODUCTOS ====================
echo "<h3>📦 Productos con tienda_virtual = 'si'</h3>";
$query = "SELECT * FROM lista_productos WHERE tienda_virtual = 'si' ORDER BY categoria, nombre";
$result = mysqli_query($conexion, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Tienda Virtual</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $tv_status = ($row['tienda_virtual'] == 'si') ? "<span class='badge badge-success'>✓ si</span>" : "<span class='badge badge-danger'>✗ no</span>";
        echo "<tr>";
        echo "<td><strong>{$row['codigo']}</strong></td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['categoria']}</td>";
        echo "<td>{$tv_status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success'>✅ Total: " . mysqli_num_rows($result) . " productos encontrados</p>";
} else {
    echo "<p class='error'>❌ No se encontraron productos con tienda_virtual='si'</p>";
}

// ==================== 2. CATEGORÍAS EXISTENTES ====================
echo "<h3>🏷️ Categorías existentes</h3>";
$query = "SELECT DISTINCT categoria, COUNT(*) as total FROM lista_productos GROUP BY categoria";
$result = mysqli_query($conexion, $query);
echo "<table>";
echo "<tr><th>Categoría</th><th>Total Productos</th><th>Con tienda_virtual='si'</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    $cat = $row['categoria'];
    $total = $row['total'];
    
    // Contar productos con tienda_virtual='si' en esta categoría
    $query_tv = "SELECT COUNT(*) as tv_count FROM lista_productos WHERE categoria = '$cat' AND tienda_virtual = 'si'";
    $result_tv = mysqli_query($conexion, $query_tv);
    $tv_count = mysqli_fetch_assoc($result_tv)['tv_count'];
    
    $status = ($tv_count > 0) ? "<span class='badge badge-success'>$tv_count productos</span>" : "<span class='badge badge-warning'>Sin productos</span>";
    echo "<tr>";
    echo "<td><strong>'$cat'</strong></td>";
    echo "<td>$total</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";

// ==================== 3. IMÁGENES DE PRODUCTOS ====================
echo "<h3>🖼️ Imágenes de productos (tabla: imagenes)</h3>";

// Verificar si la tabla existe
$check_table = "SHOW TABLES LIKE 'imagenes'";
$table_exists = mysqli_query($conexion, $check_table);

if (mysqli_num_rows($table_exists) > 0) {
    $query = "SELECT i.*, p.nombre as producto_nombre, p.categoria 
              FROM imagenes i 
              LEFT JOIN lista_productos p ON i.codigo = p.codigo 
              ORDER BY i.codigo, i.id_imagen";
    $result = mysqli_query($conexion, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Código Producto</th><th>Producto</th><th>Categoría</th><th>Ruta de Imagen</th><th>Vista Previa</th><th>Estado</th></tr>";
        
        $total_imagenes = 0;
        $imagenes_validas = 0;
        $imagenes_invalidas = 0;
        
        while ($row = mysqli_fetch_assoc($result)) {
            $total_imagenes++;
            $ruta = $row['ruta_imagen'];
            $ruta_completa = '../' . $ruta;
            $existe = file_exists($ruta_completa);
            
            // Verificar si la imagen existe físicamente
            if ($existe) {
                $estado = "<span class='badge badge-success'>✓ Existe</span>";
                $preview = "<img src='$ruta_completa' class='image-preview' onerror=\"this.style.display='none'\">";
                $imagenes_validas++;
            } else {
                $estado = "<span class='badge badge-danger'>✗ No encontrada</span>";
                $preview = "<span class='warning'>⚠️ Archivo no encontrado</span>";
                $imagenes_invalidas++;
            }
            
            echo "<tr>";
            echo "<td>{$row['id_imagen']}</td>";
            echo "<td><strong>{$row['codigo']}</strong></td>";
            echo "<td>{$row['producto_nombre']}</td>";
            echo "<td>{$row['categoria']}</td>";
            echo "<td><code style='font-size:11px;'>{$row['ruta_imagen']}</code></td>";
            echo "<td>$preview</td>";
            echo "<td>$estado</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div class='stats'>";
        echo "<p><strong>📊 Estadísticas de imágenes:</strong></p>";
        echo "<p>• Total de registros: <strong>$total_imagenes</strong></p>";
        echo "<p class='success'>• Imágenes válidas: <strong>$imagenes_validas</strong></p>";
        if ($imagenes_invalidas > 0) {
            echo "<p class='error'>• Imágenes no encontradas: <strong>$imagenes_invalidas</strong></p>";
        }
        echo "</div>";
        
    } else {
        echo "<p class='warning'>⚠️ No hay registros en la tabla 'imagenes'</p>";
    }
} else {
    echo "<p class='error'>❌ La tabla 'imagenes' no existe. Debes crearla con: <br>
    <code>CREATE TABLE imagenes (
        id_imagen INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL,
        ruta_imagen VARCHAR(255) NOT NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );</code></p>";
}

// ==================== 4. DETALLES DE PRODUCTOS ====================
echo "<h3>📋 Detalles de productos (tabla: detalle_producto)</h3>";
$query = "SELECT d.*, p.nombre as producto_nombre, p.categoria 
          FROM detalle_producto d 
          LEFT JOIN lista_productos p ON d.codigo = p.codigo 
          ORDER BY d.codigo, d.medida";
$result = mysqli_query($conexion, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>ID Detalle</th><th>Código</th><th>Producto</th><th>Medida</th><th>Detalle</th><th>Precio Unitario</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id_detalle']}</td>";
        echo "<td><strong>{$row['codigo']}</strong></td>";
        echo "<td>{$row['producto_nombre']}</td>";
        echo "<td>{$row['medida']}</td>";
        echo "<td>{$row['detalle']}</td>";
        echo "<td class='success'>$" . number_format($row['precio_unitario'], 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success'>✅ Total: " . mysqli_num_rows($result) . " detalles encontrados</p>";
} else {
    echo "<p class='warning'>⚠️ No hay registros en la tabla 'detalle_producto'</p>";
}

// ==================== 5. VERIFICAR RUTAS DE CARPETAS ====================
echo "<h3>📁 Verificación de carpetas</h3>";
$carpetas = ['../public/img/', '../public/img/productos/', '../uploads/', '../public/uploads/'];
echo "<table>";
echo "<tr><th>Carpeta</th><th>Estado</th></tr>";
foreach ($carpetas as $carpeta) {
    if (file_exists($carpeta)) {
        echo "<tr><td><code>$carpeta</code></td><td class='success'>✓ Existe</td></tr>";
    } else {
        echo "<tr><td><code>$carpeta</code></td><td class='error'>✗ No existe</td></tr>";
    }
}
echo "</table>";

// ==================== 6. SUGERENCIAS ====================
echo "<h3>💡 Sugerencias</h3>";
echo "<ul>";
echo "<li>Las categorías deben coincidir EXACTAMENTE con lo que buscas en el código.</li>";
echo "<li>Las rutas de imágenes deben ser relativas a la carpeta 'public/'</li>";
echo "<li>Para imágenes de ejemplo, colócalas en: <code>public/img/productos/</code></li>";
echo "<li>Verifica que los productos tengan <strong>tienda_virtual = 'si'</strong></li>";
echo "</ul>";

// ==================== 7. PRODUCTOS POR CATEGORÍA (MUESTRA) ====================
echo "<h3>🔍 Muestra de productos por categoría (con tienda_virtual='si')</h3>";
$categorias = ['hotelera', 'hogar', 'hospitalaria', 'institucional', 'otros'];
foreach ($categorias as $cat) {
    $query = "SELECT COUNT(*) as total FROM lista_productos WHERE categoria = '$cat' AND tienda_virtual = 'si'";
    $result = mysqli_query($conexion, $query);
    $total = mysqli_fetch_assoc($result)['total'];
    
    $icono = ($total > 0) ? "✅" : "❌";
    echo "<p>$icono <strong>$cat</strong>: $total productos disponibles en tienda virtual</p>";
}

echo "</div></body></html>";
?>