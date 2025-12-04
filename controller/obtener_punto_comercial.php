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

$sql = "SELECT * FROM puntos_comerciales WHERE id = '$id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $punto = $result->fetch_assoc();
    $punto['fecha_creacion'] = date('d/m/Y H:i', strtotime($punto['fecha_creacion']));
    echo json_encode(['success' => true, 'data' => $punto]);
} else {
    echo json_encode(['success' => false, 'message' => 'Punto comercial no encontrado']);
}

$conn->close();
?>
