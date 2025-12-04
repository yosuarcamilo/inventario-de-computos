<?php
session_start();
require_once '../login/conexion.php';
require_once '../includes/validaciones.php';

// Función para enviar respuesta JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        jsonResponse(false, 'Método no permitido');
    } else {
        header('Location: ../admin/index.php?page=puntos_switch&error=1');
        exit;
    }
}

// Obtener acción
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'agregar':
        agregarPuntoSwitch();
        break;
    case 'actualizar':
        actualizarPuntoSwitch();
        break;
    case 'eliminar':
        eliminarPuntoSwitch();
        break;
    default:
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Acción no válida');
        } else {
            header('Location: ../admin/index.php?page=puntos_switch&error=1');
            exit;
        }
}

function agregarPuntoSwitch() {
    global $conn;
    
    // Validar datos requeridos
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    if (empty($estado)) {
        $estado = 'Activo'; // Valor por defecto
    }
    $sede_id = (int)($_POST['sede_id'] ?? 0);
    
    if (empty($ubicacion) || empty($estado) || $sede_id <= 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Todos los campos requeridos deben ser completados');
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1");
            exit;
        }
    }
    
    // Obtener datos opcionales
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $placa = trim($_POST['placa'] ?? '');
    $numero_puertos = !empty($_POST['numero_puertos']) ? (int)$_POST['numero_puertos'] : null;
    
    // Validar duplicados antes de insertar (validación global)
    $validador = crearValidador();
    $datos_punto = [
        'modelo' => $modelo,
        'placa' => $placa,
        'serial' => $serial
    ];
    
    $errores = $validador->validarPuntoSwitch($datos_punto);
    
    if (!empty($errores)) {
        $mensaje_errores = implode('; ', $errores);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Se encontraron duplicados: ' . $mensaje_errores);
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1&mensaje=" . urlencode($mensaje_errores));
            exit;
        }
    }
    
    // Insertar nuevo punto switch
    $sql = "INSERT INTO puntos_switch (sede_id, ubicacion, marca, modelo, serial, placa, numero_puertos, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssis", $sede_id, $ubicacion, $marca, $modelo, $serial, $placa, $numero_puertos, $estado);
    
    if ($stmt->execute()) {
        $inserted_id = $conn->insert_id;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(true, "Punto switch agregado correctamente. ID: $inserted_id", ['redirect' => 'puntos_switch']);
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&success=1");
            exit;
        }
    } else {
        error_log("Error al insertar punto switch: " . $stmt->error);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Error al agregar el punto switch');
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1");
            exit;
        }
    }
}

function actualizarPuntoSwitch() {
    global $conn;
    
    // Validar datos requeridos
    $id = (int)($_POST['id'] ?? 0);
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    if (empty($estado)) {
        $estado = 'Activo'; // Valor por defecto
    }
    $sede_id = (int)($_POST['sede_id'] ?? 0);
    
    if ($id <= 0 || empty($ubicacion) || empty($estado) || $sede_id <= 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Todos los campos requeridos deben ser completados');
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1");
            exit;
        }
    }
    
    // Obtener datos opcionales
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $serial = trim($_POST['serial'] ?? '');
    $placa = trim($_POST['placa'] ?? '');
    $numero_puertos = trim($_POST['numero_puertos'] ?? '');
    
    // Validar duplicados antes de actualizar (validación global, excluyendo el registro actual)
    $validador = crearValidador();
    $datos_punto = [
        'modelo' => $modelo,
        'placa' => $placa,
        'serial' => $serial
    ];
    
    $errores = $validador->validarPuntoSwitch($datos_punto, $id);
    
    if (!empty($errores)) {
        $mensaje_errores = implode('; ', $errores);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Se encontraron duplicados: ' . $mensaje_errores);
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1&mensaje=" . urlencode($mensaje_errores));
            exit;
        }
    }
    
    // Actualizar punto switch
    $sql = "UPDATE puntos_switch SET ubicacion = ?, marca = ?, modelo = ?, serial = ?, placa = ?, numero_puertos = ?, estado = ? WHERE id = ? AND sede_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $ubicacion, $marca, $modelo, $serial, $placa, $numero_puertos, $estado, $id, $sede_id);
    
    if ($stmt->execute()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(true, "Punto switch actualizado correctamente", ['redirect' => 'puntos_switch']);
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&success=1");
            exit;
        }
    } else {
        error_log("Error al actualizar punto switch: " . $stmt->error);
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            jsonResponse(false, 'Error al actualizar el punto switch');
        } else {
            header("Location: ../admin/index.php?page=puntos_switch&sede_id=$sede_id&error=1");
            exit;
        }
    }
}

function eliminarPuntoSwitch() {
    global $conn;
    
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        jsonResponse(false, 'ID de punto switch no válido');
    }
    
    // Eliminar punto switch
    $sql = "DELETE FROM puntos_switch WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, "Punto switch eliminado correctamente");
    } else {
        error_log("Error al eliminar punto switch: " . $stmt->error);
        jsonResponse(false, 'Error al eliminar el punto switch');
    }
}

$conn->close();
?>
