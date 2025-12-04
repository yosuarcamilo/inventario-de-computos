<?php
session_start();
require_once '../login/conexion.php';

// Obtener sede_id de la URL
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Obtener datos para filtros
$sql_ubicaciones = "SELECT DISTINCT ubicacion FROM puntos_switch WHERE sede_id = '$sede_id' ORDER BY ubicacion";
$result_ubicaciones = $conn->query($sql_ubicaciones);
$ubicaciones = [];
if ($result_ubicaciones) {
    while ($row = $result_ubicaciones->fetch_assoc()) {
        $ubicaciones[] = $row['ubicacion'];
    }
}

// Obtener puntos switch con filtros
$where_conditions = ["sede_id = '$sede_id'"];
$filtro_ubicacion = $_GET['filtro_ubicacion'] ?? '';
$filtro_estado = $_GET['filtro_estado'] ?? '';

if ($filtro_ubicacion) {
    $where_conditions[] = "ubicacion LIKE '%" . $conn->real_escape_string($filtro_ubicacion) . "%'";
}
if ($filtro_estado) {
    $where_conditions[] = "estado = '" . $conn->real_escape_string($filtro_estado) . "'";
}

$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT * FROM puntos_switch WHERE $where_clause ORDER BY ubicacion, marca, modelo";
$result = $conn->query($sql);

// Obtener todos los datos para JavaScript (sin filtros para que funcione el modal)
$sql_all = "SELECT * FROM puntos_switch WHERE sede_id = '$sede_id' ORDER BY ubicacion, marca, modelo";
$result_all = $conn->query($sql_all);
$puntos_switch_data = [];

// Debug: verificar la consulta
error_log("SQL para JavaScript: " . $sql_all);
error_log("Sede ID: " . $sede_id);

if ($result_all && $result_all->num_rows > 0) {
    while ($row = $result_all->fetch_assoc()) {
        $puntos_switch_data[] = $row;
    }
    error_log("Datos cargados para JavaScript: " . count($puntos_switch_data) . " registros");
} else {
    error_log("No se encontraron datos para sede_id: " . $sede_id);
    if (!$result_all) {
        error_log("Error en consulta: " . $conn->error);
    }
}
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-network-wired"></i> Puntos Switch</h2>
        <p>Gestiona los puntos switch en las diferentes ambientes</p>
    </div>

    <!-- Mensajes de respuesta -->
    <div id="messageContainer" style="display: none;">
        <div id="messageContent"></div>
    </div>

    <!-- Campo oculto para sede_id -->
    <input type="hidden" id="current_sede_id" value="<?php echo $sede_id; ?>">

    <?php
    // Mostrar mensajes de éxito o error
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo '<div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> Punto switch procesado correctamente.
              </div>';
    }
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        $mensaje_error = isset($_GET['mensaje']) ? $_GET['mensaje'] : 'Error al procesar el punto switch.';
        echo '<div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($mensaje_error) . '
              </div>';
    }
    ?>

    <!-- Botón para agregar punto switch -->
    <div class="action-bar">
        <button class="btn-primary" onclick="abrirModalAgregar()">
            <i class="fas fa-plus"></i> Agregar Punto Switch
        </button>
        <button class="btn-success" onclick="exportarPuntosSwitch()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
    </div>

    <!-- Filtros en tiempo real -->
    <div class="filters-section">
        <div class="filter-row">
            <div class="filter-group">
                <label for="filtroUbicacion">Ubicación:</label>
                <input type="text" id="filtroUbicacion" placeholder="Buscar por ubicación..." onkeyup="filtrarTiempoReal()">
            </div>
            <div class="filter-group">
                <label for="filtroMarca">Marca:</label>
                <input type="text" id="filtroMarca" placeholder="Buscar por marca..." onkeyup="filtrarTiempoReal()">
            </div>
            <div class="filter-group">
                <label for="filtroEstado">Estado:</label>
                <select id="filtroEstado" onchange="filtrarTiempoReal()">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                </select>
            </div>
            <div class="filter-group">
                <button class="btn-secondary" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </button>
            </div>
        </div>
        <div class="filter-results">
            <span id="contador_resultados">Mostrando todos los registros</span>
        </div>
    </div>

    <!-- Tabla de puntos switch -->
    <div class="table-container">
        <table id="tablaPuntosSwitch" class="data-table">
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Ubicación</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Serial</th>
                    <th>Placa</th>
                    <th>Puertos</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($punto = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="actions">
                                <button class="btn-view" onclick="verPuntoSwitch(<?php echo $punto['id']; ?>)" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-edit" onclick="editarPuntoSwitch(<?php echo $punto['id']; ?>)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="eliminarPuntoSwitch(<?php echo $punto['id']; ?>)" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            <td><?php echo htmlspecialchars($punto['ubicacion']); ?></td>
                            <td><?php echo htmlspecialchars($punto['marca'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($punto['modelo'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($punto['serial'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($punto['placa'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($punto['numero_puertos'] ?? ''); ?></td>
                            <td>
                                <?php 
                                $estado = $punto['estado'] ?? 'Sin Estado';
                                $estado_lower = strtolower(str_replace(' ', '-', $estado));
                                ?>
                                <span class="status-badge status-<?php echo $estado_lower; ?>">
                                    <?php echo htmlspecialchars($estado); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($punto['fecha_creacion'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="no-data">No se encontraron puntos switch</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar punto switch -->
<div id="modalAgregarPuntoSwitch" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Agregar Punto Switch</h3>
            <span class="close" onclick="cerrarModalAgregar()">&times;</span>
        </div>
        
        <form method="POST" action="../controller/puntos_switch_controller.php" id="formAgregarPuntoSwitch">
            <input type="hidden" name="action" value="agregar">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="ubicacion">Ubicación *</label>
                        <input type="text" id="ubicacion" name="ubicacion" required placeholder="Ej: Laboratorio 101">
                    </div>
                    
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" placeholder="Ej: Cisco, HP, TP-Link">
                    </div>
                    
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" name="modelo" placeholder="Ej: Catalyst 2960">
                    </div>
                    
                    <div class="form-group">
                        <label for="serial">Serial</label>
                        <input type="text" id="serial" name="serial" placeholder="Ej: FOC1234X567">
                    </div>
                    
                    <div class="form-group">
                        <label for="placa">Placa</label>
                        <input type="text" id="placa" name="placa" placeholder="Ej: SW001-PLACA">
                    </div>
                    
                    <div class="form-group">
                        <label for="numero_puertos">Número de Puertos</label>
                        <input type="number" id="numero_puertos" name="numero_puertos" placeholder="Ej: 24, 48" min="1" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select id="estado" name="estado" required>
                            <option value="">Seleccionar estado</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalAgregar()">Cancelar</button>
                <button type="submit" class="btn-primary">Agregar Punto Switch</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver punto switch -->
<div id="modalVerPuntoSwitch" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Detalles del Punto Switch</h3>
            <span class="close" onclick="cerrarModalVer()">&times;</span>
        </div>
        
        <div class="modal-body">
            <div class="device-details">
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Ubicación:</label>
                        <span id="detalleUbicacion"></span>
                    </div>
                    <div class="detail-group">
                        <label>Marca:</label>
                        <span id="detalleMarca"></span>
                    </div>
                    <div class="detail-group">
                        <label>Modelo:</label>
                        <span id="detalleModelo"></span>
                    </div>
                    <div class="detail-group">
                        <label>Serial:</label>
                        <span id="detalleSerial"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Placa:</label>
                        <span id="detallePlaca"></span>
                    </div>
                    <div class="detail-group">
                        <label>Número de Puertos:</label>
                        <span id="detalleNumeroPuertos"></span>
                    </div>
                    <div class="detail-group">
                        <label>Estado:</label>
                        <span id="detalleEstado"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Fecha Creación:</label>
                        <span id="detalleFechaCreacion"></span>
                    </div>
                    <div class="detail-group">
                        <label>Fecha Modificación:</label>
                        <span id="detalleFechaModificacion"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalVer()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal para editar punto switch -->
<div id="modalEditarPuntoSwitch" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Punto Switch</h3>
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
        </div>
        
        <form method="POST" action="../controller/puntos_switch_controller.php" id="formEditarPuntoSwitch">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="editUbicacion">Ubicación *</label>
                        <input type="text" id="editUbicacion" name="ubicacion" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editMarca">Marca</label>
                        <input type="text" id="editMarca" name="marca">
                    </div>
                    
                    <div class="form-group">
                        <label for="editModelo">Modelo</label>
                        <input type="text" id="editModelo" name="modelo">
                    </div>
                    
                    <div class="form-group">
                        <label for="editSerial">Serial</label>
                        <input type="text" id="editSerial" name="serial">
                    </div>
                    
                    <div class="form-group">
                        <label for="editPlaca">Placa</label>
                        <input type="text" id="editPlaca" name="placa">
                    </div>
                    
                    <div class="form-group">
                        <label for="editNumeroPuertos">Número de Puertos</label>
                        <input type="number" id="editNumeroPuertos" name="numero_puertos" min="1" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="editEstado">Estado *</label>
                        <select id="editEstado" name="estado" required>
                            <option value="">Seleccionar estado</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="btn-primary">Actualizar Punto Switch</button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales
window.puntosSwitch = <?php echo json_encode($puntos_switch_data); ?>;

// Debug: verificar que los datos se cargan correctamente
console.log('Puntos Switch cargados:', window.puntosSwitch);
console.log('Cantidad de puntos switch:', window.puntosSwitch.length);

// Verificar si hay datos
if (window.puntosSwitch.length === 0) {
    console.warn('⚠️ No hay datos de puntos switch cargados');
} else {
    console.log('✅ Datos cargados correctamente');
}

// Funciones de modal
function abrirModalAgregar() {
    document.getElementById('modalAgregarPuntoSwitch').style.display = 'block';
}

function cerrarModalAgregar() {
    document.getElementById('modalAgregarPuntoSwitch').style.display = 'none';
    document.getElementById('formAgregarPuntoSwitch').reset();
}

function cerrarModalVer() {
    document.getElementById('modalVerPuntoSwitch').style.display = 'none';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarPuntoSwitch').style.display = 'none';
}

// Función para ver punto switch
function verPuntoSwitch(id) {
    console.log('Buscando punto switch con ID:', id);
    console.log('Datos disponibles:', puntosSwitch);
    
    // Mostrar alerta de información
    showAlert('Información del Punto Switch', 'Cargando detalles del punto switch...', 'info');
    
    // Simular carga (en un caso real, podrías hacer una petición AJAX)
    setTimeout(() => {
        const punto = window.puntosSwitch.find(p => parseInt(p.id) === parseInt(id));
        console.log('Punto encontrado:', punto);
        
        if (punto) {
            // Llenar los detalles en el modal
            document.getElementById('detalleUbicacion').textContent = punto.ubicacion || 'N/A';
            document.getElementById('detalleMarca').textContent = punto.marca || 'N/A';
            document.getElementById('detalleModelo').textContent = punto.modelo || 'N/A';
            document.getElementById('detalleSerial').textContent = punto.serial || 'N/A';
            document.getElementById('detallePlaca').textContent = punto.placa || 'N/A';
            document.getElementById('detalleNumeroPuertos').textContent = punto.numero_puertos || 'N/A';
            document.getElementById('detalleEstado').textContent = punto.estado || 'N/A';
            document.getElementById('detalleFechaCreacion').textContent = punto.fecha_creacion || 'N/A';
            document.getElementById('detalleFechaModificacion').textContent = punto.fecha_modificacion || 'N/A';
            
            // Abrir el modal
            document.getElementById('modalVerPuntoSwitch').style.display = 'block';
            
            // Cerrar alerta
            hideAlert();
        } else {
            showAlert('Error', 'No se encontró el punto switch seleccionado. ID: ' + id, 'error');
        }
    }, 500);
}

// Función para editar punto switch
function editarPuntoSwitch(id) {
    console.log('Editando punto switch con ID:', id);
    console.log('Datos disponibles:', puntosSwitch);
    
    showAlert('Editar Punto Switch', 'Preparando formulario de edición...', 'info');
    
    setTimeout(() => {
        const punto = window.puntosSwitch.find(p => parseInt(p.id) === parseInt(id));
        console.log('Punto encontrado para editar:', punto);
        
        if (punto) {
            // Llenar el formulario de edición
            document.getElementById('editId').value = punto.id;
            document.getElementById('editUbicacion').value = punto.ubicacion || '';
            document.getElementById('editMarca').value = punto.marca || '';
            document.getElementById('editModelo').value = punto.modelo || '';
            document.getElementById('editSerial').value = punto.serial || '';
            document.getElementById('editPlaca').value = punto.placa || '';
            document.getElementById('editNumeroPuertos').value = punto.numero_puertos || '';
            document.getElementById('editEstado').value = punto.estado || '';
            
            // Abrir el modal
            document.getElementById('modalEditarPuntoSwitch').style.display = 'block';
            
            // Cerrar alerta
            hideAlert();
        } else {
            showAlert('Error', 'No se encontró el punto switch para editar. ID: ' + id, 'error');
        }
    }, 300);
}

// Función para eliminar punto switch
function eliminarPuntoSwitch(id) {
    showAlert('Confirmar Eliminación', '¿Estás seguro de que deseas eliminar este punto switch?', 'warning', true);
    
    // Crear botones de confirmación
    const confirmBtn = document.createElement('button');
    confirmBtn.textContent = 'Sí, Eliminar';
    confirmBtn.className = 'btn-delete';
    confirmBtn.style.marginRight = '10px';
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancelar';
    cancelBtn.className = 'btn-secondary';
    
    // Agregar botones al alert
    const alertContent = document.getElementById('alertContent');
    alertContent.appendChild(confirmBtn);
    alertContent.appendChild(cancelBtn);
    
    // Event listeners
    confirmBtn.onclick = () => {
        hideAlert();
        showAlert('Eliminando...', 'Procesando eliminación del punto switch', 'info');
        
        const formData = new FormData();
        formData.append('action', 'eliminar');
        formData.append('id', id);
        
        fetch('../controller/puntos_switch_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Éxito', 'Punto switch eliminado correctamente', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('Error', 'Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error', 'Error al eliminar el punto switch', 'error');
        });
    };
    
    cancelBtn.onclick = () => {
        hideAlert();
    };
}

// Función de filtrado en tiempo real
function filtrarTiempoReal() {
    const filtroUbicacion = document.getElementById('filtroUbicacion').value.toLowerCase();
    const filtroMarca = document.getElementById('filtroMarca').value.toLowerCase();
    const filtroEstado = document.getElementById('filtroEstado').value;
    const tabla = document.querySelector('#tablaPuntosSwitch tbody');
    
    if (!tabla) return;
    
    const filas = tabla.querySelectorAll('tr');
    let contador = 0;
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length > 1) {
            const ubicacion = celdas[1].textContent.toLowerCase();
            const marca = celdas[2].textContent.toLowerCase();
            const estado = celdas[7].textContent.trim();
            
            const cumpleFiltros = 
                ubicacion.includes(filtroUbicacion) &&
                marca.includes(filtroMarca) &&
                (filtroEstado === '' || estado === filtroEstado);
            
            if (cumpleFiltros) {
                fila.style.display = '';
                contador++;
            } else {
                fila.style.display = 'none';
            }
        }
    });
    
    // Actualizar contador
    actualizarContador(contador);
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroUbicacion').value = '';
    document.getElementById('filtroMarca').value = '';
    document.getElementById('filtroEstado').value = '';
    filtrarTiempoReal();
}

// Función para actualizar contador
function actualizarContador(total) {
    const contadorElement = document.getElementById('contador_resultados');
    if (contadorElement) {
        contadorElement.textContent = `Mostrando ${total} resultados`;
    }
}

// Función para exportar puntos switch
function exportarPuntosSwitch() {
    showAlert('Exportando...', 'Preparando archivo Excel de puntos switch', 'info');
    
    setTimeout(() => {
        // Obtener sede_id del campo oculto
        const sedeIdElement = document.getElementById('current_sede_id');
        const sedeId = sedeIdElement ? sedeIdElement.value : '1';
        
        const filtroUbicacion = document.getElementById('filtroUbicacion').value;
        const filtroEstado = document.getElementById('filtroEstado').value;
        const filtroMarca = document.getElementById('filtroMarca').value;
        
        let url = `../controller/exportar_puntos_switch.php?sede_id=${sedeId}`;
        if (filtroUbicacion) url += `&filtro_ubicacion=${encodeURIComponent(filtroUbicacion)}`;
        if (filtroEstado) url += `&filtro_estado=${encodeURIComponent(filtroEstado)}`;
        if (filtroMarca) url += `&filtro_marca=${encodeURIComponent(filtroMarca)}`;
        
        console.log('Exportando con sede_id:', sedeId); // Para debug
        
        // Abrir en nueva ventana
        const newWindow = window.open(url, '_blank');
        
        if (newWindow) {
            showAlert('Éxito', 'Archivo Excel generado correctamente. Se abrirá en una nueva ventana.', 'success');
            setTimeout(() => {
                hideAlert();
            }, 2000);
        } else {
            showAlert('Error', 'No se pudo abrir la ventana de descarga. Verifica que no esté bloqueada por el navegador.', 'error');
        }
    }, 500);
}

// Funciones de alerta
function showAlert(title, message, type = 'info', showButtons = false) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <div class="alert-header">
            <h4>${title}</h4>
            <span class="alert-close" onclick="hideAlert()">&times;</span>
        </div>
        <div id="alertContent" class="alert-body">
            <p>${message}</p>
        </div>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto-hide después de 3 segundos si no es una alerta con botones
    if (!showButtons) {
        setTimeout(() => {
            hideAlert();
        }, 3000);
    }
}

function hideAlert() {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer && alertContainer.children.length > 0) {
        alertContainer.removeChild(alertContainer.lastChild);
    }
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    `;
    document.body.appendChild(container);
    return container;
}

// Función mejorada de filtrado en tiempo real
function filtrarTiempoReal() {
    const filtroUbicacion = document.getElementById('filtroUbicacion').value.toLowerCase();
    const filtroMarca = document.getElementById('filtroMarca').value.toLowerCase();
    const filtroEstado = document.getElementById('filtroEstado').value;
    const tabla = document.querySelector('#tablaPuntosSwitch tbody');
    
    if (!tabla) return;
    
    const filas = tabla.querySelectorAll('tr');
    let contador = 0;
    let hasFilters = filtroUbicacion || filtroMarca || filtroEstado;
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length > 1) {
            const ubicacion = celdas[1].textContent.toLowerCase();
            const marca = celdas[2].textContent.toLowerCase();
            const estado = celdas[7].textContent.trim();
            
            const cumpleFiltros = 
                ubicacion.includes(filtroUbicacion) &&
                marca.includes(filtroMarca) &&
                (filtroEstado === '' || estado === filtroEstado);
            
            if (cumpleFiltros) {
                fila.style.display = '';
                contador++;
            } else {
                fila.style.display = 'none';
            }
        }
    });
    
    // Actualizar contador
    actualizarContador(contador);
    
    // Mostrar mensaje si no hay resultados
    if (contador === 0 && hasFilters) {
        showAlert('Sin resultados', 'No se encontraron puntos switch que coincidan con los filtros aplicados', 'warning');
    }
}

// Event listeners para formularios
document.addEventListener('DOMContentLoaded', function() {
    // Formulario de agregar
    const formAgregar = document.getElementById('formAgregarPuntoSwitch');
    if (formAgregar) {
        formAgregar.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Agregando...', 'Procesando nuevo punto switch', 'info');
            
            const formData = new FormData(this);
            
            fetch('../controller/puntos_switch_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Éxito', 'Punto switch agregado correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error', 'Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error', 'Error al agregar el punto switch', 'error');
            });
        });
    }
    
    // Formulario de editar
    const formEditar = document.getElementById('formEditarPuntoSwitch');
    if (formEditar) {
        formEditar.addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('Actualizando...', 'Procesando actualización del punto switch', 'info');
            
            const formData = new FormData(this);
            
            fetch('../controller/puntos_switch_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Éxito', 'Punto switch actualizado correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error', 'Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error', 'Error al actualizar el punto switch', 'error');
            });
        });
    }
});

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function(event) {
    const modales = document.querySelectorAll('.modal');
    modales.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
</script>

<style>
/* Estilos específicos para Puntos Switch */
.content-section {
    padding: 20px;
    background: #f8f9fa;
    min-height: 100vh;
}

.section-header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header h2 {
    color: #2c3e50;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header p {
    color: #7f8c8d;
    margin: 0;
}

.action-bar {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.btn-primary, .btn-success, .btn-secondary {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-success {
    background: #27ae60;
    color: white;
}

.btn-success:hover {
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

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    flex-wrap: wrap;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    font-weight: 500;
    margin-bottom: 5px;
    color: #2c3e50;
}

.filter-group input, .filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.filter-group input:focus, .filter-group select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.filter-results {
    padding-top: 15px;
    border-top: 1px solid #eee;
    color: #7f8c8d;
    font-size: 14px;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #34495e;
    color: white;
    padding: 15px 12px;
    text-align: left;
    font-weight: 500;
    font-size: 14px;
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn-view, .btn-edit, .btn-delete {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}

.btn-view {
    background: #3498db;
    color: white;
}

.btn-view:hover {
    background: #2980b9;
}

.btn-edit {
    background: #f39c12;
    color: white;
}

.btn-edit:hover {
    background: #e67e22;
}

.btn-delete {
    background: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background: #c0392b;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.status-activo {
    background: #d4edda;
    color: #155724;
}

.status-inactivo {
    background: #f8d7da;
    color: #721c24;
}

.status-mantenimiento {
    background: #fff3cd;
    color: #856404;
}

.status-sin-estado {
    background: #e2e3e5;
    color: #6c757d;
}

.no-data {
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    padding: 40px;
}

/* Estilos para modales */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-large {
    max-width: 800px;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.close {
    font-size: 28px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 5px;
    color: #2c3e50;
}

.form-group input, .form-group select {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.device-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.detail-group {
    display: flex;
    flex-direction: column;
}

.detail-group label {
    font-weight: 500;
    color: #7f8c8d;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.detail-group span {
    color: #2c3e50;
    font-size: 14px;
    font-weight: 500;
}

/* Estilos para alertas */
.alert {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 15px;
    overflow: hidden;
    animation: slideIn 0.3s ease-out;
}

.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    font-weight: 500;
}

.alert-header h4 {
    margin: 0;
    font-size: 16px;
}

.alert-close {
    font-size: 20px;
    cursor: pointer;
    color: #aaa;
    line-height: 1;
}

.alert-close:hover {
    color: #000;
}

.alert-body {
    padding: 0 20px 15px 20px;
}

.alert-body p {
    margin: 0;
    color: #555;
    line-height: 1.4;
}

.alert-info {
    border-left: 4px solid #3498db;
}

.alert-info .alert-header {
    background: #e3f2fd;
    color: #1976d2;
}

.alert-success {
    border-left: 4px solid #27ae60;
}

.alert-success .alert-header {
    background: #e8f5e8;
    color: #2e7d32;
}

.alert-warning {
    border-left: 4px solid #f39c12;
}

.alert-warning .alert-header {
    background: #fff3e0;
    color: #f57c00;
}

.alert-error {
    border-left: 4px solid #e74c3c;
}

.alert-error .alert-header {
    background: #ffebee;
    color: #d32f2f;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .action-bar {
        flex-direction: column;
    }
    
    .filter-row {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-row {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        margin: 10% auto;
        width: 95%;
    }
    
    .data-table {
        font-size: 12px;
    }
    
    .data-table th, .data-table td {
        padding: 8px 6px;
    }
    
    #alertContainer {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
