<?php
session_start();
require_once '../login/conexion.php';

// Obtener sede_id de la URL
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Obtener datos para filtros
$sql_ubicaciones = "SELECT DISTINCT ubicacion FROM puntos_ap WHERE sede_id = '$sede_id' ORDER BY ubicacion";
$result_ubicaciones = $conn->query($sql_ubicaciones);
$ubicaciones = [];
if ($result_ubicaciones) {
    while ($row = $result_ubicaciones->fetch_assoc()) {
        $ubicaciones[] = $row['ubicacion'];
    }
}

// Obtener puntos AP con filtros
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
$sql = "SELECT * FROM puntos_ap WHERE $where_clause ORDER BY numero";
$result = $conn->query($sql);
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-wifi"></i> Puntos AP</h2>
        <p>Gestiona los puntos de acceso inalámbrico en las diferentes ubicaciones</p>
    </div>

    <!-- Mensajes de respuesta -->
    <div id="messageContainer" style="display: none;">
        <div id="messageContent"></div>
    </div>

    <!-- Campo oculto para sede_id -->
    <input type="hidden" id="current_sede_id" value="<?php echo $sede_id; ?>">

    <!-- Botón para agregar punto AP -->
    <div class="action-bar">
        <button class="btn-primary" onclick="abrirModalAgregar()">
            <i class="fas fa-plus"></i> Agregar Punto AP
        </button>
        <button class="btn-success" onclick="exportarPuntosAP()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-form">
            <div class="filter-group">
                <label for="filtro_ubicacion">Filtro por Ubicación</label>
                <input type="text" id="filtro_ubicacion" 
                       placeholder="Buscar por ubicación..." 
                       onkeyup="filtrarTiempoReal()">
            </div>
            
            <div class="filter-group">
                <label for="filtro_estado">Filtro por Estado</label>
                <select id="filtro_estado" onchange="filtrarTiempoReal()">
                    <option value="">Todos</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="button" class="btn-info" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Contador de resultados -->
    <div id="contador_resultados" class="contador-resultados">
        Mostrando todos los resultados
    </div>

    <!-- Tabla de puntos AP -->
    <div class="table-container">
        <table class="data-table" id="tablaPuntosAP">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['numero']); ?></td>
                            <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                            <td class="actions">
                                <button class="btn-view" onclick="verPuntoAP(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-edit" onclick="editarPuntoAP(<?php echo $row['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="eliminarPuntoAP(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['numero']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">No hay puntos AP registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar punto AP -->
<div id="modalAgregarPunto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Punto AP</h3>
            <span class="close" onclick="cerrarModalAgregar()">&times;</span>
        </div>
        
        <form id="formAgregarPunto" class="modal-form">
            <input type="hidden" name="agregar_punto_ap" value="1">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            
            <div class="form-group">
                <label for="numeroPunto">Número *</label>
                <input type="text" id="numeroPunto" name="numero" required>
            </div>
            
            <div class="form-group">
                <label for="ubicacionPunto">Ubicación *</label>
                <input type="text" id="ubicacionPunto" name="ubicacion" required>
            </div>
            
            <div class="form-group">
                <label for="estadoPunto">Estado *</label>
                <select id="estadoPunto" name="estado" required>
                    <option value="">Seleccione...</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="cerrarModalAgregar()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver punto AP -->
<div id="modalVerPunto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ver Punto AP</h3>
            <span class="close" onclick="cerrarModalVer()">&times;</span>
        </div>
        
        <div class="modal-body" id="modalVerBody">
            <!-- Los datos se cargarán dinámicamente -->
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="cerrarModalVer()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal para editar punto AP -->
<div id="modalEditarPunto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Punto AP</h3>
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
        </div>
        
        <form id="formEditarPunto" class="modal-form">
            <input type="hidden" name="actualizar_punto_ap" value="1">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            <input type="hidden" name="id" id="editId">
            
            <div class="form-group">
                <label for="editNumero">Número *</label>
                <input type="text" id="editNumero" name="numero" required>
            </div>
            
            <div class="form-group">
                <label for="editUbicacion">Ubicación *</label>
                <input type="text" id="editUbicacion" name="ubicacion" required>
            </div>
            
            <div class="form-group">
                <label for="editEstado">Estado *</label>
                <select id="editEstado" name="estado" required>
                    <option value="">Seleccione...</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalConfirmarEliminar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarModalConfirmar()">&times;</span>
        </div>
        
        <div class="modal-body">
            <p><i class="fas fa-exclamation-triangle" style="color: #e74c3c; margin-right: 10px;"></i>¿Estás seguro de que quieres eliminar este punto AP?</p>
            <p><strong>⚠️ Esta acción no se puede deshacer.</strong></p>
        </div>
        
        <form id="formEliminar">
            <input type="hidden" name="eliminar_punto_ap" value="1">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            <input type="hidden" name="id" id="eliminarId">
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="cerrarModalConfirmar()">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
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

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.action-bar {
    margin-bottom: 20px;
}

.btn-primary {
    background: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary:hover {
    background: #2980b9;
}

.filters-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #2E75B6;
    box-shadow: 0 0 0 2px rgba(46, 117, 182, 0.2);
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.data-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #2c3e50;
}

.data-table tr:hover {
    background-color: #f5f5f5;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-activo {
    background-color: #d4edda;
    color: #155724;
}

.status-inactivo {
    background-color: #f8d7da;
    color: #721c24;
}

.status-mantenimiento {
    background-color: #fff3cd;
    color: #856404;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn-view,
.btn-edit,
.btn-delete {
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-view {
    background: #17a2b8;
    color: white;
}

.btn-edit {
    background: #ffc107;
    color: #212529;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.no-data {
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    padding: 40px;
}

/* Modal Styles */
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
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2c3e50;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.modal-form {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #2c3e50;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.modal-body {
    padding: 20px;
}

.modal-actions {
    padding: 20px;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-danger:hover {
    background: #c82333;
}

/* Estilos para filtros en tiempo real */
.contador-resultados {
    background-color: #e8f4fd;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    border-left: 4px solid #2E75B6;
    font-weight: 500;
    color: #2E75B6;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions {
        justify-content: center;
    }
    
    .actions {
        flex-direction: column;
    }
}
</style>

<script>
// Funciones para modales
function abrirModalAgregar() {
    document.getElementById('modalAgregarPunto').style.display = 'block';
}

function cerrarModalAgregar() {
    document.getElementById('modalAgregarPunto').style.display = 'none';
    document.getElementById('formAgregarPunto').reset();
}

function cerrarModalVer() {
    document.getElementById('modalVerPunto').style.display = 'none';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarPunto').style.display = 'none';
}

function cerrarModalConfirmar() {
    document.getElementById('modalConfirmarEliminar').style.display = 'none';
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    let container = document.getElementById('messageContainer');
    let content = document.getElementById('messageContent');
    
    content.innerHTML = `<div class="alert alert-${tipo}">${mensaje}</div>`;
    container.style.display = 'block';
    
    // Ocultar después de 5 segundos
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

// Función para recargar la tabla
function recargarTabla() {
    // Obtener sede_id actual
    const urlParams = new URLSearchParams(window.location.search);
    const sede_id = urlParams.get('sede_id') || 1;
    
    // Usar la función loadContent del sistema AJAX
    if (typeof loadContent === 'function') {
        loadContent('puntos_ap', sede_id);
    } else {
        // Fallback: recargar la página completa
        window.location.href = `../admin/index.php?page=puntos_ap&sede_id=${sede_id}`;
    }
}

// Manejar formulario de agregar
document.getElementById('formAgregarPunto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../controller/puntos_ap_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // ← Aquí se muestra el alert
            cerrarModalAgregar();
            recargarTabla();
        } else {
            mostrarMensaje(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al procesar la solicitud: ' + error.message, 'error');
    });
});

// Manejar formulario de editar
document.getElementById('formEditarPunto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../controller/puntos_ap_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            cerrarModalEditar();
            recargarTabla();
        } else {
            mostrarMensaje(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al procesar la solicitud: ' + error.message, 'error');
    });
});

// Manejar formulario de eliminar
document.getElementById('formEliminar').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../controller/puntos_ap_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            cerrarModalConfirmar();
            recargarTabla();
        } else {
            mostrarMensaje(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al procesar la solicitud: ' + error.message, 'error');
    });
});

// Ver punto AP
function verPuntoAP(id) {
    fetch(`../controller/obtener_punto_ap.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const punto = data.punto;
                document.getElementById('modalVerBody').innerHTML = `
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Número:</strong> ${punto.numero}
                        </div>
                        <div class="info-item">
                            <strong>Ubicación:</strong> ${punto.ubicacion}
                        </div>
                        <div class="info-item">
                            <strong>Estado:</strong> 
                            <span class="status-badge status-${punto.estado.toLowerCase()}">${punto.estado}</span>
                        </div>
                        <div class="info-item">
                            <strong>Fecha de Creación:</strong> ${punto.fecha_creacion}
                        </div>
                    </div>
                `;
                document.getElementById('modalVerPunto').style.display = 'block';
            } else {
                mostrarMensaje('Error al obtener los datos del punto AP', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al obtener los datos del punto AP', 'error');
        });
}

// Editar punto AP
function editarPuntoAP(id) {
    fetch(`../controller/obtener_punto_ap.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const punto = data.punto;
                document.getElementById('editId').value = punto.id;
                document.getElementById('editNumero').value = punto.numero;
                document.getElementById('editUbicacion').value = punto.ubicacion;
                document.getElementById('editEstado').value = punto.estado;
                document.getElementById('modalEditarPunto').style.display = 'block';
            } else {
                mostrarMensaje('Error al obtener los datos del punto AP', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al obtener los datos del punto AP', 'error');
        });
}

// Eliminar punto AP
function eliminarPuntoAP(id, numero) {
    if (confirm('¿Estás seguro de que deseas eliminar este punto AP?')) {
        const formData = new FormData();
        formData.append('eliminar_punto_ap', '1');
        formData.append('id', id);
        
        fetch('../controller/puntos_ap_controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                recargarTabla();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar punto AP');
        });
    }
}

// Función para filtrar en tiempo real
function filtrarTiempoReal() {
    let filtroUbicacion = document.getElementById('filtro_ubicacion').value.toLowerCase();
    let filtroEstado = document.getElementById('filtro_estado').value;
    const tabla = document.querySelector('#tablaPuntosAP tbody');
    
    if (!tabla) return;
    
    const filas = tabla.querySelectorAll('tr');
    let contador = 0;
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        let mostrar = true;
        
        // Debug: mostrar información de las celdas
        console.log('Fila:', fila);
        console.log('Celdas:', celdas.length);
        celdas.forEach((celda, index) => {
            console.log(`Celda ${index}:`, celda.textContent.trim());
        });
        
        // Filtrar por ubicación (segunda columna - índice 1)
        if (filtroUbicacion && celdas.length > 1) {
            const ubicacion = celdas[1].textContent.trim().toLowerCase();
            console.log('Ubicación:', ubicacion, 'Filtro:', filtroUbicacion);
            if (!ubicacion.includes(filtroUbicacion)) {
                mostrar = false;
            }
        }
        
        // Filtrar por estado (tercera columna - índice 2)
        if (filtroEstado && celdas.length > 2) {
            const estado = celdas[2].textContent.trim();
            console.log('Estado:', estado, 'Filtro:', filtroEstado);
            if (estado !== filtroEstado) {
                mostrar = false;
            }
        }
        
        // Mostrar/ocultar fila
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) contador++;
    });
    
    // Actualizar contador
    actualizarContador(contador);
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtro_ubicacion').value = '';
    document.getElementById('filtro_estado').value = '';
    filtrarTiempoReal();
}

// Función para actualizar contador
function actualizarContador(total) {
    let contadorElement = document.getElementById('contador_resultados');
    if (contadorElement) {
        contadorElement.textContent = `Mostrando ${total} resultados`;
    }
}

// Función para exportar puntos AP
function exportarPuntosAP() {
    // Obtener sede_id del campo oculto
    const sedeIdElement = document.getElementById('current_sede_id');
    const sedeId = sedeIdElement ? sedeIdElement.value : '1';
    
    const filtroUbicacion = document.getElementById('filtro_ubicacion').value;
    const filtroEstado = document.getElementById('filtro_estado').value;
    
    let url = `../controller/exportar_puntos_ap.php?sede_id=${sedeId}`;
    if (filtroUbicacion) url += `&filtro_ubicacion=${encodeURIComponent(filtroUbicacion)}`;
    if (filtroEstado) url += `&filtro_estado=${encodeURIComponent(filtroEstado)}`;
    
    console.log('Exportando con sede_id:', sedeId); // Para debug
    window.open(url, '_blank');
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    filtrarTiempoReal();
});

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
</script>
