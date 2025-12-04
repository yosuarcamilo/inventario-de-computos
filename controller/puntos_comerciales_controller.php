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
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'agregar':
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
            
            $errores = $validador->validarPuntoComercial($datos_punto);
            
            if (!empty($errores)) {
                $mensaje_errores = implode('; ', $errores);
                // Redirigir de vuelta a la página con el error
                header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&error=1&mensaje=" . urlencode($mensaje_errores));
                exit;
            }

            // Usar prepared statement
            $stmt = $conn->prepare("INSERT INTO puntos_comerciales (sede_id, numero, ubicacion, estado) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
            }

            $stmt->bind_param("isss", $sede_id, $numero, $ubicacion, $estado);
            if ($stmt->execute()) {
                $inserted_id = $stmt->insert_id;
                // Si es una petición AJAX, devolver JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    jsonResponse(true, "Punto comercial agregado correctamente. ID: $inserted_id", ['redirect' => 'puntos_comerciales']);
                } else {
                    // Si es un envío de formulario tradicional, redirigir
                    $sede_id = $_POST['sede_id'];
                    header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&success=1");
                    exit;
                }
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    jsonResponse(false, 'Error al agregar punto comercial: ' . $stmt->error);
                } else {
                    $sede_id = $_POST['sede_id'];
                    header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&error=1");
                    exit;
                }
            }
            $stmt->close();
            break;

        case 'actualizar':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $numero = trim($_POST['numero']);
            $ubicacion = trim($_POST['ubicacion']);
            $estado = trim($_POST['estado']);

            // Validación básica
            if ($id <= 0 || $numero === '' || $ubicacion === '' || $estado === '') {
                jsonResponse(false, 'Todos los campos son obligatorios.');
            }

            // Obtener sede_id del punto actual
            $stmt_sede = $conn->prepare("SELECT sede_id FROM puntos_comerciales WHERE id = ?");
            $stmt_sede->bind_param("i", $id);
            $stmt_sede->execute();
            $result_sede = $stmt_sede->get_result();
            $sede_id = $result_sede->fetch_assoc()['sede_id'];
            $stmt_sede->close();

            // Validar duplicados antes de actualizar (validación global, excluyendo el registro actual)
            $validador = crearValidador();
            $datos_punto = [
                'numero' => $numero
            ];
            
            $errores = $validador->validarPuntoComercial($datos_punto, $id);
            
            if (!empty($errores)) {
                $mensaje_errores = implode('; ', $errores);
                // Redirigir de vuelta a la página con el error
                header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&error=1&mensaje=" . urlencode($mensaje_errores));
                exit;
            }

            // Usar prepared statement
            $stmt = $conn->prepare("UPDATE puntos_comerciales SET numero = ?, ubicacion = ?, estado = ? WHERE id = ?");
            if (!$stmt) {
                jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
            }

            $stmt->bind_param("sssi", $numero, $ubicacion, $estado, $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        jsonResponse(true, "Punto comercial actualizado correctamente.", ['redirect' => 'puntos_comerciales']);
                    } else {
                        $sede_id = $_POST['sede_id'];
                        header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&success=1");
                        exit;
                    }
                } else {
                    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        jsonResponse(false, 'No se encontró el punto comercial o no hubo cambios.');
                    } else {
                        $sede_id = $_POST['sede_id'];
                        header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&error=1");
                        exit;
                    }
                }
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    jsonResponse(false, 'Error al actualizar punto comercial: ' . $stmt->error);
                } else {
                    $sede_id = $_POST['sede_id'];
                    header("Location: ../admin/index.php?page=puntos_comerciales&sede_id=$sede_id&error=1");
                    exit;
                }
            }
            $stmt->close();
            break;

        case 'eliminar':
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id <= 0) {
                jsonResponse(false, 'ID de punto comercial inválido.');
            }

            // Usar prepared statement
            $stmt = $conn->prepare("DELETE FROM puntos_comerciales WHERE id = ?");
            if (!$stmt) {
                jsonResponse(false, 'Error en la preparación de la consulta: ' . $conn->error);
            }

            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    jsonResponse(true, "Punto comercial eliminado correctamente.", ['redirect' => 'puntos_comerciales']);
                } else {
                    jsonResponse(false, 'No se encontró el punto comercial.');
                }
            } else {
                jsonResponse(false, 'Error al eliminar punto comercial: ' . $stmt->error);
            }
            $stmt->close();
            break;

        default:
            jsonResponse(false, 'Acción no válida.');
            break;
    }

} catch (Exception $e) {
    error_log("Error en puntos_comerciales_controller.php: " . $e->getMessage());
    jsonResponse(false, 'Error interno del servidor.');
}
?>
