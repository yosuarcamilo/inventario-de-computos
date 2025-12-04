<?php
session_start();
require_once '../login/conexion.php';

// Obtener parámetros
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;
$filtro_ubicacion = $_GET['filtro_ubicacion'] ?? '';
$filtro_estado = $_GET['filtro_estado'] ?? '';
$filtro_marca = $_GET['filtro_marca'] ?? '';

// Construir consulta con filtros
$where_conditions = ["sede_id = '$sede_id'"];

if ($filtro_ubicacion) {
    $where_conditions[] = "ubicacion LIKE '%" . $conn->real_escape_string($filtro_ubicacion) . "%'";
}
if ($filtro_estado) {
    $where_conditions[] = "estado = '" . $conn->real_escape_string($filtro_estado) . "'";
}
if ($filtro_marca) {
    $where_conditions[] = "marca LIKE '%" . $conn->real_escape_string($filtro_marca) . "%'";
}

$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT * FROM puntos_switch WHERE $where_clause ORDER BY ubicacion, marca, modelo";

$result = $conn->query($sql);

// Obtener nombre de la sede
$sql_sede = "SELECT nombre FROM sedes WHERE id = '$sede_id'";
$result_sede = $conn->query($sql_sede);
$sede_nombre = $result_sede->num_rows > 0 ? $result_sede->fetch_assoc()['nombre'] : 'Sede Desconocida';

// Configurar headers para descarga
$filename = "puntos_switch_" . strtolower(str_replace(' ', '_', $sede_nombre)) . "_" . date('Y-m-d') . ".xls";
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Generar contenido Excel con HTML
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
echo 'h1 { color: #333; text-align: center; }';
echo 'h2 { color: #666; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<h1>Reporte de Puntos Switch</h1>';
echo '<h2>Sede: ' . htmlspecialchars($sede_nombre) . '</h2>';
echo '<h2>Fecha: ' . date('d/m/Y H:i:s') . '</h2>';

if ($filtro_ubicacion || $filtro_estado || $filtro_marca) {
    echo '<h2>Filtros aplicados:</h2>';
    if ($filtro_ubicacion) echo '<p>• Ubicación: ' . htmlspecialchars($filtro_ubicacion) . '</p>';
    if ($filtro_estado) echo '<p>• Estado: ' . htmlspecialchars($filtro_estado) . '</p>';
    if ($filtro_marca) echo '<p>• Marca: ' . htmlspecialchars($filtro_marca) . '</p>';
}

echo '<br>';

if ($result && $result->num_rows > 0) {
    echo '<table>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Ubicación</th>';
    echo '<th>Marca</th>';
    echo '<th>Modelo</th>';
    echo '<th>Serial</th>';
    echo '<th>Placa</th>';
    echo '<th>Número de Puertos</th>';
    echo '<th>Estado</th>';
    echo '<th>Fecha Creación</th>';
    echo '<th>Fecha Modificación</th>';
    echo '</tr>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['ubicacion']) . '</td>';
        echo '<td>' . htmlspecialchars($row['marca'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['modelo'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['serial'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['placa'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['numero_puertos'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['estado']) . '</td>';
        echo '<td>' . htmlspecialchars($row['fecha_creacion']) . '</td>';
        echo '<td>' . htmlspecialchars($row['fecha_modificacion']) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
    // Resumen
    echo '<br><h2>Resumen:</h2>';
    echo '<p>Total de puntos switch: ' . $result->num_rows . '</p>';
} else {
    echo '<p>No se encontraron puntos switch con los filtros aplicados.</p>';
}

echo '</body></html>';

$conn->close();
?>
