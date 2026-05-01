<?php
require 'includes/conexion.php';

function conectarDB() {
    global $conexion;
    
    // Verificar si la conexión está activa
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    
    return $conexion;
}

// Función para obtener datos de ventas por período
function obtenerVentasPorPeriodo($periodo, $fechaInicio = null, $fechaFin = null) {
    $conexion = conectarDB();
    $ventas = [];
    
    switch($periodo) {
        case 'dia':
            $sql = "SELECT DATE(fecha_venta) as fecha, SUM(total_venta) as total 
                    FROM ventas 
                    WHERE DATE(fecha_venta) = CURDATE() 
                    GROUP BY DATE(fecha_venta)";
            break;
        case 'semana':
            $sql = "SELECT YEARWEEK(fecha_venta) as semana, SUM(total_venta) as total 
                    FROM ventas 
                    WHERE YEARWEEK(fecha_venta) = YEARWEEK(CURDATE()) 
                    GROUP BY YEARWEEK(fecha_venta)";
            break;
        case 'mes':
            $sql = "SELECT DATE_FORMAT(fecha_venta, '%Y-%m') as mes, SUM(total_venta) as total 
                    FROM ventas 
                    WHERE MONTH(fecha_venta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_venta) = YEAR(CURDATE()) 
                    GROUP BY DATE_FORMAT(fecha_venta, '%Y-%m')";
            break;
        case 'año':
            $sql = "SELECT YEAR(fecha_venta) as año, SUM(total_venta) as total 
                    FROM ventas 
                    WHERE YEAR(fecha_venta) = YEAR(CURDATE()) 
                    GROUP BY YEAR(fecha_venta)";
            break;
        case 'personalizado':
            if ($fechaInicio && $fechaFin) {
                // Usar prepared statements para prevenir inyección SQL
                $stmt = $conexion->prepare("SELECT DATE(fecha_venta) as fecha, SUM(total_venta) as total 
                        FROM ventas 
                        WHERE fecha_venta BETWEEN ? AND ? 
                        GROUP BY DATE(fecha_venta) 
                        ORDER BY fecha_venta");
                $stmt->bind_param("ss", $fechaInicio, $fechaFin);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $ventas[] = $row;
                    }
                }
                $stmt->close();
                // NO cerrar la conexión global aquí
                return $ventas;
            }
            break;
    }
    
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
    }
    
    // NO cerrar la conexión global
    return $ventas;
}

// Función para obtener productos más vendidos
function obtenerProductosMasVendidos($limit = 10) {
    $conexion = conectarDB();
    $productos = [];
    
    // Usar prepared statement para prevenir inyección SQL
    $stmt = $conexion->prepare("SELECT 
                lp.nombre,
                lp.categoria,
                SUM(dv.cantidad) as total_cantidad,
                SUM(dv.sub_total) as total_ventas,
                AVG(dv.precio_venta) as precio_promedio,
                AVG(dp.precio_unitario) as costo_promedio,
                (SUM(dv.sub_total) - (SUM(dv.cantidad) * AVG(dp.precio_unitario))) as margen_total
            FROM detalle_venta dv
            JOIN lista_productos lp ON dv.codigo = lp.codigo
            JOIN detalle_producto dp ON dv.id_detalle = dp.id_detalle
            GROUP BY dv.codigo
            ORDER BY total_cantidad DESC
            LIMIT ?");
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    
    $stmt->close();
    // NO cerrar la conexión global
    return $productos;
}

// Función para obtener ventas por cliente
function obtenerVentasPorCliente() {
    $conexion = conectarDB();
    $clientes = [];
    
    $sql = "SELECT 
                cc.id_cliente,
                cc.nombre,
                cc.empresa,
                cc.departamento,
                COUNT(v.id_venta) as total_ventas,
                SUM(v.total_venta) as total_comprado
            FROM cartera_clientes cc
            LEFT JOIN ventas v ON cc.id_cliente = v.id_cliente
            GROUP BY cc.id_cliente
            ORDER BY total_comprado DESC";
    
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
    
    // NO cerrar la conexión global
    return $clientes;
}

// Función para obtener ventas por empresa
function obtenerVentasPorEmpresa() {
    $conexion = conectarDB();
    $empresas = [];
    
    $sql = "SELECT 
                cc.empresa,
                COUNT(v.id_venta) as total_ventas,
                SUM(v.total_venta) as total_comprado,
                COUNT(DISTINCT cc.id_cliente) as total_clientes
            FROM cartera_clientes cc
            LEFT JOIN ventas v ON cc.id_cliente = v.id_cliente
            WHERE cc.empresa IS NOT NULL AND cc.empresa != ''
            GROUP BY cc.empresa
            ORDER BY total_comprado DESC";
    
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $empresas[] = $row;
        }
    }
    
    // NO cerrar la conexión global
    return $empresas;
}

// Función para obtener distribución geográfica
function obtenerDistribucionGeografica($departamento = null) {
    $conexion = conectarDB();
    $distribucion = [];
    
    if ($departamento) {
        // Usar prepared statement para prevenir inyección SQL
        $stmt = $conexion->prepare("SELECT 
                cc.departamento,
                COUNT(DISTINCT cc.id_cliente) as total_clientes,
                COUNT(v.id_venta) as total_ventas,
                SUM(v.total_venta) as total_comprado
            FROM cartera_clientes cc
            LEFT JOIN ventas v ON cc.id_cliente = v.id_cliente
            WHERE cc.departamento = ?
            GROUP BY cc.departamento ORDER BY total_comprado DESC");
        
        $stmt->bind_param("s", $departamento);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $distribucion[] = $row;
            }
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT 
                cc.departamento,
                COUNT(DISTINCT cc.id_cliente) as total_clientes,
                COUNT(v.id_venta) as total_ventas,
                SUM(v.total_venta) as total_comprado
            FROM cartera_clientes cc
            LEFT JOIN ventas v ON cc.id_cliente = v.id_cliente
            GROUP BY cc.departamento ORDER BY total_comprado DESC";
        
        $result = $conexion->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $distribucion[] = $row;
            }
        }
    }
    
    // NO cerrar la conexión global
    return $distribucion;
}

// Función para obtener ventas por ubicación
function obtenerVentasPorUbicacion() {
    $conexion = conectarDB();
    $ubicaciones = [];
    
    $sql = "SELECT 
                a.id_lugar,
                a.lugar,
                COUNT(v.id_venta) as total_ventas,
                SUM(v.total_venta) as total_ventas_monto,
                AVG(v.total_venta) as promedio_venta
            FROM ambiente a
            LEFT JOIN ventas v ON a.id_lugar = v.lugar_venta
            GROUP BY a.id_lugar
            ORDER BY total_ventas_monto DESC";
    
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ubicaciones[] = $row;
        }
    }
    
    // NO cerrar la conexión global
    return $ubicaciones;
}

// Función para obtener comparación entre ubicaciones
function obtenerComparacionUbicaciones() {
    $conexion = conectarDB();
    $comparacion = [];
    
    $sql = "SELECT 
                a.lugar,
                COUNT(v.id_venta) as ventas_totales,
                SUM(v.total_venta) as monto_total,
                AVG(v.total_venta) as ticket_promedio,
                COUNT(DISTINCT v.id_cliente) as clientes_unicos
            FROM ambiente a
            LEFT JOIN ventas v ON a.id_lugar = v.lugar_venta
            GROUP BY a.id_lugar
            ORDER BY monto_total DESC";
    
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $comparacion[] = $row;
        }
    }
    
    // NO cerrar la conexión global
    return $comparacion;
}

// Función para obtener todos los departamentos de Bolivia
function obtenerDepartamentosBolivia() {
    return [
        'La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 
        'Potosí', 'Tarija', 'Chuquisaca', 'Beni', 'Pando'
    ];
}
?>