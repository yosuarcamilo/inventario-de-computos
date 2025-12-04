<?php
session_start();
require_once '../login/conexion.php';

// Obtener sede_id de la URL
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Configurar headers para descarga
$filename = "dispositivos_sede_" . $sede_id . "_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear archivo CSV
$output = fopen('php://output', 'w');

// Agregar BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Obtener datos de dispositivos
$sql = "SELECT * FROM dispositivos WHERE sede_id = '$sede_id' ORDER BY id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Obtener la primera fila para crear los encabezados
    $first_row = $result->fetch_assoc();
    $headers = array_keys($first_row);
    
    // Escribir encabezados
    fputcsv($output, $headers);
    
    // Escribir la primera fila
    fputcsv($output, $first_row);
    
    // Escribir el resto de filas
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // Si no hay datos
    fputcsv($output, ['No hay dispositivos registrados en esta sede']);
}

fclose($output);
$conn->close();
exit;
?>
