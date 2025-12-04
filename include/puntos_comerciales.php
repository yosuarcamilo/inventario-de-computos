<?php
session_start();
require_once '../login/conexion.php';

// Obtener sede_id de la URL
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Obtener datos para filtros
$sql_ubicaciones = "SELECT DISTINCT ubicacion FROM puntos_comerciales WHERE sede_id = '$sede_id' ORDER BY ubicacion";
$result_ubicaciones = $conn->query($sql_ubicaciones);
$ubicaciones = [];
if ($result_ubicaciones) {
    while ($row = $result_ubicaciones->fetch_assoc()) {
        $ubicaciones[] = $row['ubicacion'];
    }
}

// Obtener puntos comerciales con filtros
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
$sql = "SELECT * FROM puntos_comerciales WHERE $where_clause ORDER BY numero";
$result = $conn->query($sql);
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-store"></i> Puntos Comerciales</h2>
        <p>Gestiona los puntos comerciales en las diferentes ubicaciones</p>
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
                <i class="fas fa-check-circle"></i> Punto comercial procesado correctamente.
              </div>';
    }
    if (isset($_GET['error']) && $_GET['error'] == '1') {
        $mensaje_error = isset($_GET['mensaje']) ? $_GET['mensaje'] : 'Error al procesar el punto comercial.';
        echo '<div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($mensaje_error) . '
              </div>';
    }
    ?>

    <!-- Botón para agregar punto comercial -->
    <div class="action-bar">
        <button class="btn-primary" onclick="abrirModalAgregar()">
            <i class="fas fa-plus"></i> Agregar Punto Comercial
        </button>
        <button class="btn-success" onclick="exportarPuntosComerciales()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
    </div>

    <!-- Filtros en tiempo real -->
    <div class="filters-section">
        <div class="filter-row">
            <div class="filter-group">
                <label for="filtroNumero">Número:</label>
                <input type="text" id="filtroNumero" placeholder="Buscar por número..." onkeyup="filtrarTiempoReal()">
            </div>
            <div class="filter-group">
                <label for="filtroUbicacion">Ubicación:</label>
                <input type="text" id="filtroUbicacion" placeholder="Buscar por ubicación..." onkeyup="filtrarTiempoReal()">
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

    <!-- Tabla de puntos comerciales -->
    <div class="table-container">
        <table class="data-table" id="tablaPuntosComerciales">
            <thead>
                <tr>
                    <th>ID</th>
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
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['numero']); ?></td>
                            <td><?php echo htmlspecialchars($row['ubicacion']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo $row['estado']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                            <td class="actions">
                                <button class="btn-view" onclick="verPuntoComercial(<?php echo $row['id']; ?>)" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-edit" onclick="editarPuntoComercial(<?php echo $row['id']; ?>)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="eliminarPuntoComercial(<?php echo $row['id']; ?>)" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">No hay puntos comerciales registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para agregar punto comercial -->
<div id="modalAgregarPuntoComercial" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Agregar Punto Comercial</h3>
            <span class="close" onclick="cerrarModalAgregar()">&times;</span>
        </div>
        <form id="formAgregarPuntoComercial" method="POST" action="../controller/puntos_comerciales_controller.php">
            <input type="hidden" name="action" value="agregar">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero" required>
                </div>
                <div class="form-group">
                    <label for="ubicacion">Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" required>
                </div>
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalAgregar()">Cancelar</button>
                <button type="submit" class="btn-primary">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver punto comercial -->
<div id="modalVerPuntoComercial" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-eye"></i> Ver Punto Comercial</h3>
            <span class="close" onclick="cerrarModalVer()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="info-group">
                <label>ID:</label>
                <span id="verId"></span>
            </div>
            <div class="info-group">
                <label>Número:</label>
                <span id="verNumero"></span>
            </div>
            <div class="info-group">
                <label>Ubicación:</label>
                <span id="verUbicacion"></span>
            </div>
            <div class="info-group">
                <label>Estado:</label>
                <span id="verEstado"></span>
            </div>
            <div class="info-group">
                <label>Fecha Creación:</label>
                <span id="verFechaCreacion"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalVer()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal para editar punto comercial -->
<div id="modalEditarPuntoComercial" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Punto Comercial</h3>
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
        </div>
        <form id="formEditarPuntoComercial" method="POST" action="../controller/puntos_comerciales_controller.php">
            <input type="hidden" name="action" value="actualizar">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            <input type="hidden" id="editId" name="id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="editNumero">Número:</label>
                    <input type="text" id="editNumero" name="numero" required>
                </div>
                <div class="form-group">
                    <label for="editUbicacion">Ubicación:</label>
                    <input type="text" id="editUbicacion" name="ubicacion" required>
                </div>
                <div class="form-group">
                    <label for="editEstado">Estado:</label>
                    <select id="editEstado" name="estado" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div id="modalEliminarPuntoComercial" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarModalEliminar()">&times;</span>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este punto comercial?</p>
            <div class="info-group">
                <label>Número:</label>
                <span id="eliminarNumero"></span>
            </div>
            <div class="info-group">
                <label>Ubicación:</label>
                <span id="eliminarUbicacion"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="cerrarModalEliminar()">Cancelar</button>
            <button type="button" class="btn-danger" onclick="confirmarEliminacion()">Eliminar</button>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para puntos comerciales */
.content-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-header {
    margin-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 15px;
}

.section-header h2 {
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header p {
    color: #6c757d;
    margin: 5px 0 0 0;
}

.action-bar {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-primary {
    background: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.btn-danger {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-danger:hover {
    background: #c0392b;
}

/* Filtros */
.filters-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-row {
    display: flex;
    gap: 15px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 150px;
}

.filter-group label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #495057;
}

.filter-group input,
.filter-group select {
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.filter-results {
    margin-top: 10px;
    font-size: 14px;
    color: #6c757d;
}

/* Tabla */
.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.data-table th {
    background: #34495e;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
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

.actions {
    display: flex;
    gap: 5px;
}

.btn-view, .btn-edit, .btn-delete {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-view {
    background: #17a2b8;
    color: white;
}

.btn-view:hover {
    background: #138496;
}

.btn-edit {
    background: #ffc107;
    color: #212529;
}

.btn-edit:hover {
    background: #e0a800;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.no-data {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 20px;
}

/* Modales */
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
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background: #34495e;
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: white;
}

.close:hover {
    opacity: 0.7;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #495057;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.info-group {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.info-group label {
    font-weight: bold;
    color: #495057;
}

.info-group span {
    color: #6c757d;
}

/* Mensajes */
#messageContainer {
    margin-bottom: 20px;
}

#messageContent {
    padding: 15px;
    border-radius: 5px;
    font-weight: bold;
}

.message-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
window.controllerUrlComerciales = '../controller/puntos_comerciales_controller.php';
let puntoComercialAEliminar = null;

// Funciones para modales
function abrirModalAgregar() {
    document.getElementById('modalAgregarPuntoComercial').style.display = 'block';
}

function cerrarModalAgregar() {
    document.getElementById('modalAgregarPuntoComercial').style.display = 'none';
    document.getElementById('formAgregarPuntoComercial').reset();
}

function cerrarModalVer() {
    document.getElementById('modalVerPuntoComercial').style.display = 'none';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarPuntoComercial').style.display = 'none';
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminarPuntoComercial').style.display = 'none';
    puntoComercialAEliminar = null;
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo = 'success') {
    const messageContainer = document.getElementById('messageContainer');
    const messageContent = document.getElementById('messageContent');
    
    messageContent.textContent = mensaje;
    messageContent.className = `message-${tipo}`;
    messageContainer.style.display = 'block';
    
    setTimeout(() => {
        messageContainer.style.display = 'none';
    }, 5000);
}

// Función para recargar tabla
function recargarTabla() {
    const sede_id = new URLSearchParams(window.location.search).get('sede_id') || '1';
    const page = new URLSearchParams(window.location.search).get('page') || 'puntos_comerciales';
    
    if (typeof loadContent === 'function') {
        loadContent(page, sede_id);
    } else {
        window.location.href = `?page=${page}&sede_id=${sede_id}`;
    }
}

// Función para ver punto comercial
function verPuntoComercial(id) {
    fetch(`../controller/obtener_punto_comercial.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('verId').textContent = data.data.id;
                document.getElementById('verNumero').textContent = data.data.numero;
                document.getElementById('verUbicacion').textContent = data.data.ubicacion;
                document.getElementById('verEstado').textContent = data.data.estado;
                document.getElementById('verFechaCreacion').textContent = new Date(data.data.fecha_creacion).toLocaleString();
                document.getElementById('modalVerPuntoComercial').style.display = 'block';
            } else {
                mostrarMensaje('Error al obtener datos del punto comercial', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al obtener datos del punto comercial', 'error');
        });
}

// Función para editar punto comercial
function editarPuntoComercial(id) {
    fetch(`../controller/obtener_punto_comercial.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('editId').value = data.data.id;
                document.getElementById('editNumero').value = data.data.numero;
                document.getElementById('editUbicacion').value = data.data.ubicacion;
                document.getElementById('editEstado').value = data.data.estado;
                document.getElementById('modalEditarPuntoComercial').style.display = 'block';
            } else {
                mostrarMensaje('Error al obtener datos del punto comercial', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al obtener datos del punto comercial', 'error');
        });
}

// Función para eliminar punto comercial
function eliminarPuntoComercial(id) {
    fetch(`../controller/obtener_punto_comercial.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                puntoComercialAEliminar = data.data.id;
                document.getElementById('eliminarNumero').textContent = data.data.numero;
                document.getElementById('eliminarUbicacion').textContent = data.data.ubicacion;
                document.getElementById('modalEliminarPuntoComercial').style.display = 'block';
            } else {
                mostrarMensaje('Error al obtener datos del punto comercial', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensaje('Error al obtener datos del punto comercial', 'error');
        });
}

// Función para confirmar eliminación
function confirmarEliminacion() {
    if (!puntoComercialAEliminar) return;
    
    const formData = new FormData();
    formData.append('action', 'eliminar');
    formData.append('id', puntoComercialAEliminar);
    
    fetch(window.controllerUrlComerciales, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            cerrarModalEliminar();
            recargarTabla();
        } else {
            mostrarMensaje(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Error al procesar solicitud', 'error');
    });
}

// Event listeners para formularios
document.addEventListener('DOMContentLoaded', function() {
    // Formulario agregar - permitir envío tradicional
    document.getElementById('formAgregarPuntoComercial').addEventListener('submit', function(e) {
        // No prevenir el envío por defecto, permitir que se envíe tradicionalmente
        // El controlador manejará la redirección
    });
    
    // Formulario editar - permitir envío tradicional
    document.getElementById('formEditarPuntoComercial').addEventListener('submit', function(e) {
        // No prevenir el envío por defecto, permitir que se envíe tradicionalmente
        // El controlador manejará la redirección
    });
});

// Funciones de filtrado en tiempo real
function filtrarTiempoReal() {
    const filtroNumero = document.getElementById('filtroNumero').value.toLowerCase();
    const filtroUbicacion = document.getElementById('filtroUbicacion').value.toLowerCase();
    const filtroEstado = document.getElementById('filtroEstado').value.toLowerCase();
    
    const tabla = document.getElementById('tablaPuntosComerciales');
    const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    let contador = 0;
    
    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const numero = fila.cells[1].textContent.toLowerCase();
        const ubicacion = fila.cells[2].textContent.toLowerCase();
        const estado = fila.cells[3].textContent.toLowerCase();
        
        const coincideNumero = !filtroNumero || numero.includes(filtroNumero);
        const coincideUbicacion = !filtroUbicacion || ubicacion.includes(filtroUbicacion);
        const coincideEstado = !filtroEstado || estado.includes(filtroEstado);
        
        if (coincideNumero && coincideUbicacion && coincideEstado) {
            fila.style.display = '';
            contador++;
        } else {
            fila.style.display = 'none';
        }
    }
    
    document.getElementById('contador_resultados').textContent = `Mostrando ${contador} de ${filas.length} registros`;
}

function limpiarFiltros() {
    document.getElementById('filtroNumero').value = '';
    document.getElementById('filtroUbicacion').value = '';
    document.getElementById('filtroEstado').value = '';
    filtrarTiempoReal();
}

// Función para exportar puntos comerciales
function exportarPuntosComerciales() {
    // Obtener sede_id del campo oculto
    const sedeIdElement = document.getElementById('current_sede_id');
    const sedeId = sedeIdElement ? sedeIdElement.value : '1';
    
    // Obtener valores de los filtros con los IDs correctos
    const filtroUbicacionElement = document.getElementById('filtroUbicacion');
    const filtroEstadoElement = document.getElementById('filtroEstado');
    
    const filtroUbicacion = filtroUbicacionElement ? filtroUbicacionElement.value : '';
    const filtroEstado = filtroEstadoElement ? filtroEstadoElement.value : '';
    
    let url = `../controller/exportar_puntos_comerciales.php?sede_id=${sedeId}`;
    if (filtroUbicacion) url += `&filtro_ubicacion=${encodeURIComponent(filtroUbicacion)}`;
    if (filtroEstado) url += `&filtro_estado=${encodeURIComponent(filtroEstado)}`;
    
    console.log('Exportando con sede_id:', sedeId); // Para debug
    window.open(url, '_blank');
}

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
