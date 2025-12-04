<?php
session_start();
require_once '../login/conexion.php';

// Verificar si se recibió la petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Método no permitido';
    header('Location: ../admin/index.php?page=lista_dispositivos&sede_id=1');
    exit;
}

// Verificar si se envió el formulario de actualización
if (!isset($_POST['actualizar_dispositivo'])) {
    $_SESSION['error_message'] = 'Formulario no válido';
    header('Location: ../admin/index.php?page=lista_dispositivos&sede_id=1');
    exit;
}

// Obtener y validar los datos
$dispositivo_id = intval($_POST['dispositivo_id'] ?? 0);
$sede_id = intval($_POST['sede_id'] ?? 1);

if ($dispositivo_id <= 0) {
    $_SESSION['error_message'] = 'ID de dispositivo no válido';
    header('Location: ../admin/index.php?page=lista_dispositivos&sede_id=' . $sede_id);
    exit;
}

// Verificar que el dispositivo existe y pertenece a la sede
$sql_verificar = "SELECT id FROM dispositivos WHERE id = '$dispositivo_id' AND sede_id = '$sede_id'";
$result_verificar = $conn->query($sql_verificar);

if (!$result_verificar || $result_verificar->num_rows === 0) {
    $_SESSION['error_message'] = 'Dispositivo no encontrado';
    header('Location: ../admin/index.php?page=lista_dispositivos&sede_id=' . $sede_id);
    exit;
}

// Preparar los datos para la actualización
$ubicacion = $conn->real_escape_string($_POST['ubicacion'] ?? '');
$tipo_activo = $conn->real_escape_string($_POST['tipo_activo'] ?? '');
$marca = $conn->real_escape_string($_POST['marca'] ?? '');
$modelo = $conn->real_escape_string($_POST['modelo'] ?? '');
$claves_duro = $conn->real_escape_string($_POST['claves_duro'] ?? '');
$ram = $conn->real_escape_string($_POST['ram'] ?? '');
$procesador = $conn->real_escape_string($_POST['procesador'] ?? '');
$placa = $conn->real_escape_string($_POST['placa'] ?? '');
$placa_teclado = $conn->real_escape_string($_POST['placa_teclado'] ?? '');
$serial_teclado = $conn->real_escape_string($_POST['serial_teclado'] ?? '');
$entrega_teclado = $conn->real_escape_string($_POST['entrega_teclado'] ?? '');
$obs_teclado = $conn->real_escape_string($_POST['obs_teclado'] ?? '');
$placa_mouse = $conn->real_escape_string($_POST['placa_mouse'] ?? '');
$serial_mouse = $conn->real_escape_string($_POST['serial_mouse'] ?? '');
$entrega_mouse = $conn->real_escape_string($_POST['entrega_mouse'] ?? '');
$obs_mouse = $conn->real_escape_string($_POST['obs_mouse'] ?? '');
$placa_monitor = $conn->real_escape_string($_POST['placa_monitor'] ?? '');
$serial_monitor = $conn->real_escape_string($_POST['serial_monitor'] ?? '');
$entrega_monitor = $conn->real_escape_string($_POST['entrega_monitor'] ?? '');
$obs_monitor = $conn->real_escape_string($_POST['obs_monitor'] ?? '');
$placa_cpu = $conn->real_escape_string($_POST['placa_cpu'] ?? '');
$responsable = $conn->real_escape_string($_POST['responsable'] ?? '');
$firma_acta = $conn->real_escape_string($_POST['firma_acta'] ?? '');
$borrado_seguro = $conn->real_escape_string($_POST['borrado_seguro'] ?? '');
$nombre_borrado = $conn->real_escape_string($_POST['nombre_borrado'] ?? '');
$estado = $conn->real_escape_string($_POST['estado'] ?? '');

// Construir la consulta SQL
$sql_update = "UPDATE dispositivos SET 
    ubicacion = '$ubicacion',
    tipo_activo = '$tipo_activo',
    marca = '$marca',
    modelo = '$modelo',
    claves_duro = '$claves_duro',
    ram = '$ram',
    procesador = '$procesador',
    placa = '$placa',
    placa_teclado = '$placa_teclado',
    serial_teclado = '$serial_teclado',
    entrega_teclado = '$entrega_teclado',
    obs_teclado = '$obs_teclado',
    placa_mouse = '$placa_mouse',
    serial_mouse = '$serial_mouse',
    entrega_mouse = '$entrega_mouse',
    obs_mouse = '$obs_mouse',
    placa_monitor = '$placa_monitor',
    serial_monitor = '$serial_monitor',
    entrega_monitor = '$entrega_monitor',
    obs_monitor = '$obs_monitor',
    placa_cpu = '$placa_cpu',
    responsable = '$responsable',
    firma_acta = '$firma_acta',
    borrado_seguro = '$borrado_seguro',
    nombre_borrado = '$nombre_borrado',
    estado = '$estado',
    fecha_actualizacion = NOW()
    WHERE id = '$dispositivo_id'";

// Ejecutar la actualización
$result_update = $conn->query($sql_update);

if ($result_update) {
    echo "<script>
        alert('Dispositivo actualizado correctamente');
        window.location.href = '../admin/index.php?page=lista_dispositivos&sede_id=" . $sede_id . "';
    </script>";
    $conn->close();
    exit;
} else {
    echo "<script>
        alert('Error al actualizar el dispositivo: " . addslashes($conn->error) . "');
        window.location.href = '../admin/index.php?page=lista_dispositivos&sede_id=" . $sede_id . "';
    </script>";
    $conn->close();
    exit;
}

$conn->close();

// Redirigir de vuelta a la lista de dispositivos
header('Location: ../admin/index.php?page=lista_dispositivos&sede_id=' . $sede_id);
exit;
?>
