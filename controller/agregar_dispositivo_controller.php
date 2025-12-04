<?php
session_start();
require_once '../login/conexion.php';
require_once '../includes/validaciones.php';

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_formulario'])) {
    // Obtener sede_id del formulario
    $sede_id = $_POST['sede_id'] ?? 1;
    
    // Verificar si la sede existe (el campo se llama 'id', no 'sede_id')
    $check_sede = "SELECT id FROM sedes WHERE id = '$sede_id'";
    $result_sede = $conn->query($check_sede);
    
    if ($result_sede->num_rows == 0) {
        // Si la sede no existe, usar sede_id = 1 por defecto
        $sede_id = 1;
        error_log("Sede ID no encontrado, usando sede_id = 1");
    }
    
    error_log("Usando sede_id: " . $sede_id);
    $ubicacion = $_POST['ubicacion'] ?? '';
    $tipo_activo = $_POST['tipo_activo'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $claves_duro = $_POST['claves_duro'] ?? '';
    $ram = $_POST['ram'] ?? '';
    $procesador = $_POST['procesador'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $placa_teclado = $_POST['placa_teclado'] ?? '';
    $serial_teclado = $_POST['serial_teclado'] ?? '';
    $entrega_teclado = $_POST['entrega_teclado'] ?? 'No';
    $obs_teclado = $_POST['obs_teclado'] ?? '';
    $placa_mouse = $_POST['placa_mouse'] ?? '';
    $serial_mouse = $_POST['serial_mouse'] ?? '';
    $entrega_mouse = $_POST['entrega_mouse'] ?? 'No';
    $obs_mouse = $_POST['obs_mouse'] ?? '';
    $placa_monitor = $_POST['placa_monitor'] ?? '';
    $serial_monitor = $_POST['serial_monitor'] ?? '';
    $entrega_monitor = $_POST['entrega_monitor'] ?? 'No';
    $obs_monitor = $_POST['obs_monitor'] ?? '';
    $placa_cpu = $_POST['placa_cpu'] ?? '';
    $responsable = $_POST['responsable'] ?? '';
    $firma_acta = $_POST['firma_acta'] ?? 'No';
    $borrado_seguro = $_POST['borrado_seguro'] ?? 'No';
    $nombre_borrado = $_POST['nombre_borrado'] ?? '';
    $estado = $_POST['estado'] ?? 'Activo';
    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $registro = $_POST['registro'] ?? 'REG-' . date('YmdHis');
    $fecha_actualizacion = $_POST['fecha_actualizacion'] ?? null;
    
    // Validar duplicados antes de insertar (validación global)
    $validador = crearValidador();
    $datos_dispositivo = [
        'placa' => $placa,
    ];
    
    $errores = $validador->validarDispositivo($datos_dispositivo);
    
    if (!empty($errores)) {
        $mensaje = "Error: Se encontraron duplicados en el dispositivo.";
        $errores_html = $validador->mostrarErrores($errores);
        // Redirigir de vuelta al formulario con errores y sede_id
        header("Location: ../admin/index.php?page=agregar_dispositivo&sede_id=" . $sede_id . "&mensaje=" . urlencode($mensaje) . "&tipo=error&errores=" . urlencode($errores_html));
        exit();
    }
    
    $sql = "INSERT INTO dispositivos (
        sede_id, ubicacion, tipo_activo, marca, modelo, claves_duro, ram, procesador, placa,
        placa_teclado, serial_teclado, entrega_teclado, obs_teclado,
        placa_mouse, serial_mouse, entrega_mouse, obs_mouse,
        placa_monitor, serial_monitor, entrega_monitor, obs_monitor,
        placa_cpu, responsable, firma_acta, borrado_seguro, nombre_borrado,
        estado, fecha, registro, fecha_actualizacion
    ) VALUES (
        '$sede_id', '$ubicacion', '$tipo_activo', '$marca', '$modelo', '$claves_duro', '$ram', '$procesador', '$placa',
        '$placa_teclado', '$serial_teclado', '$entrega_teclado', '$obs_teclado',
        '$placa_mouse', '$serial_mouse', '$entrega_mouse', '$obs_mouse',
        '$placa_monitor', '$serial_monitor', '$entrega_monitor', '$obs_monitor',
        '$placa_cpu', '$responsable', '$firma_acta', '$borrado_seguro', '$nombre_borrado',
        '$estado', '$fecha', '$registro', '$fecha_actualizacion'
    )";
    
    if ($conn->query($sql)) {
        // Guardar mensaje de éxito en sesión para evitar re-submit
        $_SESSION['success_message'] = 'Dispositivo agregado exitosamente';
        // Redirigir al panel principal con sede_id
        header("Location: ../admin/index.php?page=lista_dispositivos&sede_id=" . $sede_id);
        exit();
    } else {
        $mensaje = "Error al guardar dispositivo: " . $conn->error;
        // Redirigir de vuelta al formulario con error y sede_id
        header("Location: ../admin/index.php?page=agregar_dispositivo&sede_id=" . $sede_id . "&mensaje=" . urlencode($mensaje) . "&tipo=error");
        exit();
    }
}
?>
