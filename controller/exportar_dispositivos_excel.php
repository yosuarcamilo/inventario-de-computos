<?php
session_start();
require_once '../login/conexion.php';

// Obtener sede_id de la URL
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Configurar headers para descarga
$filename = "dispositivos_sede_" . $sede_id . "_" . date('Y-m-d_H-i-s') . ".xls";
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Obtener datos
$sql = "SELECT * FROM dispositivos WHERE sede_id = '$sede_id' ORDER BY id";
$result = $conn->query($sql);

echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
echo 'table { border-collapse: collapse; width: 100%; margin-top: 20px; }';
echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 11px; }';
echo 'th { background-color: #2E75B6; color: white; font-weight: bold; text-align: center; }';
echo 'tr:nth-child(even) { background-color: #F8F9FA; }';
echo 'tr:hover { background-color: #E3F2FD; }';
echo '.title { font-size: 16px; font-weight: bold; color: #2E75B6; text-align: center; margin-bottom: 10px; }';
echo '.subtitle { font-size: 12px; color: #666; text-align: center; margin-bottom: 20px; }';
echo '.summary { background-color: #E8F4FD; padding: 10px; border: 1px solid #2E75B6; margin-top: 20px; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Título del reporte
echo '<div class="title">📊 REPORTE DE DISPOSITIVOS - SEDE ' . $sede_id . '</div>';
echo '<div class="subtitle">Generado el: ' . date('d/m/Y H:i:s') . '</div>';

if ($result && $result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $headers = array_keys($first_row);
    
    echo '<table>';
    
    // Encabezados
    echo '<tr>';
    foreach ($headers as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr>';
    
    // Datos
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . htmlspecialchars($value ?? '') . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    
    // Resumen
    echo '<div class="summary">';
    echo '<strong>📈 Total de dispositivos: ' . $result->num_rows . '</strong>';
    echo '</div>';
    
} else {
    echo '<div class="summary">';
    echo '<strong>⚠️ No hay dispositivos registrados en esta sede</strong>';
    echo '</div>';
}

echo '</body>';
echo '</html>';

$conn->close();
exit;
?>
