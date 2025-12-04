<?php
session_start();
require_once '../login/conexion.php';

// Verificar que se recibió el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'ID de punto switch no válido'
    ]);
    exit;
}

$id = (int)$_GET['id'];

// Obtener el punto switch
$sql = "SELECT * FROM puntos_switch WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $punto_switch = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $punto_switch
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Punto switch no encontrado'
    ]);
}

$conn->close();
?>
