<?php
session_start();
require_once '../login/conexion.php';

// Verificar si se recibió un ID válido
if (!isset($_POST['dispositivo_id']) || empty($_POST['dispositivo_id'])) {
    echo "<script>alert('ID no válido'); window.location.href='../admin/index.php?page=lista_dispositivos&sede_id=" . ($_POST['sede_id'] ?? 1) . "';</script>";
    exit;
}

$dispositivo_id = intval($_POST['dispositivo_id']);
$sede_id = intval($_POST['sede_id'] ?? 1);

// Verificar que el dispositivo existe y pertenece a la sede
$sql_verificar = "SELECT id, ubicacion, tipo_activo FROM dispositivos WHERE id = '$dispositivo_id' AND sede_id = '$sede_id'";
$result_verificar = $conn->query($sql_verificar);

if ($result_verificar && $result_verificar->num_rows > 0) {
    $dispositivo = $result_verificar->fetch_assoc();
    
    // Eliminar el dispositivo
    $sql_eliminar = "DELETE FROM dispositivos WHERE id = '$dispositivo_id'";
    $result_eliminar = $conn->query($sql_eliminar);
    
    if ($result_eliminar) {
        echo "<script>alert('Dispositivo eliminado correctamente'); window.location.href='../admin/index.php?page=lista_dispositivos&sede_id=$sede_id';</script>";
    } else {
        echo "<script>alert('Error al eliminar el dispositivo'); window.location.href='../admin/index.php?page=lista_dispositivos&sede_id=$sede_id';</script>";
    }
} else {
    echo "<script>alert('Dispositivo no encontrado'); window.location.href='../admin/index.php?page=lista_dispositivos&sede_id=$sede_id';</script>";
}

$conn->close();
exit;
?>
