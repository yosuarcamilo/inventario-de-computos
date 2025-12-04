<?php
// Obtener mensajes de la URL si existen
$mensaje = $_GET['mensaje'] ?? '';
$tipoMensaje = $_GET['tipo'] ?? '';

// Obtener sede_id de la URL
$sede_id = $_GET['sede_id'] ?? 1;
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-plus-circle"></i> Agregar Nuevo Dispositivo</h2>
        <p>Complete todos los campos requeridos para registrar un nuevo dispositivo</p>
    </div>

    <?php
    // Mostrar errores de validación si existen
    if (isset($_GET['errores']) && !empty($_GET['errores'])) {
        echo html_entity_decode($_GET['errores']);
    }
    
    // Mostrar mensajes de éxito o error
    if (!empty($mensaje)) {
        $alertClass = ($tipoMensaje === 'error') ? 'alert-danger' : 'alert-success';
        $alertIcon = ($tipoMensaje === 'error') ? 'exclamation-circle' : 'check-circle';
        $alertColor = ($tipoMensaje === 'error') ? '#721c24' : '#155724';
        $alertBg = ($tipoMensaje === 'error') ? '#f8d7da' : '#d4edda';
        $alertBorder = ($tipoMensaje === 'error') ? '#f5c6cb' : '#c3e6cb';
        
        echo '<div class="alert ' . $alertClass . '" style="background: ' . $alertBg . '; color: ' . $alertColor . '; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid ' . $alertBorder . ';">
                <i class="fas fa-' . $alertIcon . '"></i> ' . htmlspecialchars($mensaje) . '
              </div>';
    }
    ?>

    <form id="formAgregarDispositivo" class="device-form" method="POST" action="../controller/agregar_dispositivo_controller.php">
        <div class="form-grid">
            <!-- Información General -->
            <div class="form-section">
                <h3>Información General</h3>
                
                <!-- Campo oculto para la sede -->
                <input type="hidden" id="sede_id" name="sede_id" value="<?php echo $sede_id; ?>">
                
                <div class="form-group">
                    <label for="ubicacion">Ubicación</label>
                    <input type="text" id="ubicacion" name="ubicacion" value="<?php echo isset($_POST['ubicacion']) ? htmlspecialchars($_POST['ubicacion']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="tipo_activo">Tipo de Activo</label>
                    <select id="tipo_activo" name="tipo_activo">
                        <option value="">Seleccione...</option>
                        <option value="Computador">Escritorio</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Servidor">Servidor</option>
                        <option value="Impresora">Impresora</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="placa">Placa del Equipo</label>
                    <input type="text" id="placa" name="placa" placeholder="Ej: PLACA-001, EQ-2024-001">
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca">
                </div>

                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" id="modelo" name="modelo">
                </div>

                <div class="form-group">
                    <label for="claves_duro">capacidad de disco</label>
                    <input type="text" id="claves_duro" name="claves_duro">
                </div>

                <div class="form-group">
                    <label for="ram">RAM</label>
                    <input type="text" id="ram" name="ram" placeholder="Ej: 8GB, 16GB">
                </div>

                <div class="form-group">
                    <label for="procesador">Procesador</label>
                    <input type="text" id="procesador" name="procesador" placeholder="Ej: Intel i5, AMD Ryzen 5">
                </div>


            </div>

            <!-- Periféricos -->
            <div class="form-section">
                <h3>Periféricos</h3>
                
                <div class="form-group">
                    <label for="placa_teclado">Placa Teclado</label>
                    <input type="text" id="placa_teclado" name="placa_teclado">
                </div>

                <div class="form-group">
                    <label for="serial_teclado">Serial Teclado</label>
                    <input type="text" id="serial_teclado" name="serial_teclado">
                </div>

                <div class="form-group">
                    <label for="entrega_teclado">Entrega Teclado</label>
                    <select id="entrega_teclado" name="entrega_teclado">
                        <option value="">Seleccione...</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="obs_teclado">Observaciones Teclado</label>
                    <textarea id="obs_teclado" name="obs_teclado" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="placa_mouse">Placa Mouse</label>
                    <input type="text" id="placa_mouse" name="placa_mouse">
                </div>

                <div class="form-group">
                    <label for="serial_mouse">Serial Mouse</label>
                    <input type="text" id="serial_mouse" name="serial_mouse">
                </div>

                <div class="form-group">
                    <label for="entrega_mouse">Entrega Mouse</label>
                    <select id="entrega_mouse" name="entrega_mouse">
                        <option value="">Seleccione...</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="obs_mouse">Observaciones Mouse</label>
                    <textarea id="obs_mouse" name="obs_mouse" rows="2"></textarea>
                </div>
            </div>

            <!-- Monitor y CPU -->
            <div class="form-section">
                <h3>Monitor y CPU</h3>
                
                <div class="form-group">
                    <label for="placa_monitor">Placa Monitor</label>
                    <input type="text" id="placa_monitor" name="placa_monitor">
                </div>

                <div class="form-group">
                    <label for="serial_monitor">Serial Monitor</label>
                    <input type="text" id="serial_monitor" name="serial_monitor">
                </div>

                <div class="form-group">
                    <label for="entrega_monitor">Entrega Monitor</label>
                    <select id="entrega_monitor" name="entrega_monitor">
                        <option value="">Seleccione...</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="obs_monitor">Observaciones Monitor</label>
                    <textarea id="obs_monitor" name="obs_monitor" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="placa_cpu">Placa CPU</label>
                    <input type="text" id="placa_cpu" name="placa_cpu">
                </div>

                <div class="form-group">
                    <label for="responsable">Responsable</label>
                    <input type="text" id="responsable" name="responsable">
                </div>
            </div>

            <!-- Información Administrativa -->
            <div class="form-section">
                <h3>Información Administrativa</h3>
                
                <div class="form-group">
                    <label for="firma_acta">Firma Acta</label>
                    <select id="firma_acta" name="firma_acta">
                        <option value="">Seleccione...</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="borrado_seguro">Borrado Seguro</label>
                    <select id="borrado_seguro" name="borrado_seguro">
                        <option value="">Seleccione...</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre_borrado">Nombre Borrado</label>
                    <input type="text" id="nombre_borrado" name="nombre_borrado">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Seleccione...</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="En Mantenimiento">En Mantenimiento</option>
                        <option value="Fuera de Servicio">Fuera de Servicio</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="enviar_formulario" class="btn-primary" onclick="return confirmarAgregarDispositivo();">
                <i class="fas fa-save"></i> Guardar Dispositivo
            </button>
            <button type="button" class="btn-secondary" onclick="limpiarFormulario()">
                <i class="fas fa-eraser"></i> Limpiar Formulario
            </button>
            <button type="button" class="btn-info" onclick="verListaDispositivos()">
                <i class="fas fa-list"></i> Ver Lista de Dispositivos
            </button>
        </div>
    </form>
</div>

<style>
.content-section {
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-header {
    margin-bottom: 30px;
    border-bottom: 2px solid #3498db;
    padding-bottom: 15px;
}

.section-header h2 {
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.section-header p {
    color: #7f8c8d;
    margin: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.form-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.form-section h3 {
    color: #2c3e50;
    margin: 0 0 20px 0;
    font-size: 18px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 60px;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.btn-primary,
.btn-secondary,
.btn-info {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #27ae60;
    color: white;
}

.btn-primary:hover {
    background: #229954;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
}

.btn-info {
    background: #3498db;
    color: white;
}

.btn-info:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.alert {
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert i {
    font-size: 18px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
function limpiarFormulario() {
    document.getElementById('formAgregarDispositivo').reset();
}

function confirmarAgregarDispositivo() {
    return confirm('¿Estás seguro de que deseas agregar este dispositivo?');
}

function verListaDispositivos() {
    // Obtener el sede_id del formulario
    const sedeId = document.getElementById('sede_id').value;
    window.location.href = '../admin/index.php?page=lista_dispositivos&sede_id=' + sedeId;
}

// El sede_id ya está establecido desde PHP
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que el sede_id esté establecido
    const sedeIdInput = document.getElementById('sede_id');
    if (sedeIdInput && !sedeIdInput.value) {
        sedeIdInput.value = '1'; // Valor por defecto
    }
});
</script>