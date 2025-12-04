<?php
// Configurar para devolver solo JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
require_once '../login/conexion.php';
require_once '../includes/validaciones.php';

// Función para devolver respuesta JSON
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido');
}

try {
    if (isset($_POST['agregar_punto_regular'])) {
        $sede_id = isset($_POST['sede_id']) ? (int)$_POST['sede_id'] : 1;
        $numero = trim($_POST['numero']);
        $ubicacion = trim($_POST['ubicacion']);
        $estado = trim($_POST['estado']);

        // Validación básica
        if ($numero === '' || $ubicacion === '' || $estado === '') {
            jsonResponse(false, 'Todos los campos son obligatorios.');
        }

        // Validar duplicados antes de insertar (validación global)
        $validador = crearValidador();
        $datos_punto = [
            'numero' => $numero
        ];
        
        $errores = $validador->validarPuntoRegular($datos_punto);
        
        if (!empty($errores)) {
            $mensaje_errores = implode('; ', $errores);
            jsonResponse(false, 'Se encontraron duplicados: ' . $mensaje_errores);
        }

        // Usar prepared statement
        $stmt = $conn->prepare("INSERT INTO puntos_regulares (sede_id, numero, ubicacion, estado) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
        }

        $stmt->bind_param("isss", $sede_id, $numero, $ubicacion, $estado);
        if ($stmt->execute()) {
            $inserted_id = $stmt->insert_id;
            jsonResponse(true, "Punto regular agregado correctamente. ID: $inserted_id", ['redirect' => 'puntos_regulares']);
        } else {
            jsonResponse(false, 'Error al agregar punto regular: ' . $stmt->error);
        }
        $stmt->close();
    }

    if (isset($_POST['actualizar_punto_regular'])) {
        $sede_id = isset($_POST['sede_id']) ? (int)$_POST['sede_id'] : 1;
        $id = (int)$_POST['id'];
        $numero = trim($_POST['numero']);
        $ubicacion = trim($_POST['ubicacion']);
        $estado = trim($_POST['estado']);

        if ($numero === '' || $ubicacion === '' || $estado === '') {
            jsonResponse(false, 'Todos los campos son obligatorios.');
        }

        // Validar duplicados antes de actualizar (validación global, excluyendo el registro actual)
        $validador = crearValidador();
        $datos_punto = [
            'numero' => $numero
        ];
        
        $errores = $validador->validarPuntoRegular($datos_punto, $id);
        
        if (!empty($errores)) {
            $mensaje_errores = implode('; ', $errores);
            jsonResponse(false, 'Se encontraron duplicados: ' . $mensaje_errores);
        }

        $stmt = $conn->prepare("UPDATE puntos_regulares SET numero = ?, ubicacion = ?, estado = ? WHERE id = ? AND sede_id = ?");
        if (!$stmt) {
            jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
        }

        $stmt->bind_param("sssii", $numero, $ubicacion, $estado, $id, $sede_id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Punto regular actualizado correctamente.', ['redirect' => 'puntos_regulares']);
        } else {
            jsonResponse(false, 'Error al actualizar punto regular: ' . $stmt->error);
        }
        $stmt->close();
    }

    if (isset($_POST['eliminar_punto_regular'])) {
        $sede_id = isset($_POST['sede_id']) ? (int)$_POST['sede_id'] : 1;
        $id = (int)$_POST['id'];

        $stmt = $conn->prepare("DELETE FROM puntos_regulares WHERE id = ? AND sede_id = ?");
        if (!$stmt) {
            jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
        }

        $stmt->bind_param("ii", $id, $sede_id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Punto regular eliminado correctamente.', ['redirect' => 'puntos_regulares']);
        } else {
            jsonResponse(false, 'Error al eliminar punto regular: ' . $stmt->error);
        }
        $stmt->close();
    }

    // Si no se reconoce la acción
    jsonResponse(false, 'Acción no reconocida');

} catch (Exception $e) {
    jsonResponse(false, 'Error del servidor: ' . $e->getMessage());
}
?>
