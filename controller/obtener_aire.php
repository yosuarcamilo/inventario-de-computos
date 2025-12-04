<?php
session_start();
require_once '../login/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit;
}

$sql = "SELECT * FROM aires_acondicionados WHERE id = '$id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $aire = $result->fetch_assoc();
    
    // AGREGAR ESTA LÍNEA PARA DEBUGGEAR:
    error_log("Datos del aire: " . json_encode($aire));
    
    echo json_encode([
        'success' => true, 
        'message' => 'Aire obtenido correctamente',
        'data' => $aire
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Aire no encontrado']);
}

$conn->close();
?>
