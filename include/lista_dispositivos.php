<?php
session_start();
require_once '../login/conexion.php';

$sede_id = $_GET['sede_id'] ?? 1;

// Obtener nombre de la sede
$sql_sede = "SELECT nombre FROM sedes WHERE id = '$sede_id'";
$result_sede = $conn->query($sql_sede);
$sede_nombre = $result_sede->num_rows > 0 ? $result_sede->fetch_assoc()['nombre'] : 'Sede Desconocida';

// Obtener dispositivos de la sede
$sql_dispositivos = "SELECT * FROM dispositivos WHERE sede_id = '$sede_id' ORDER BY id DESC";
$result_dispositivos = $conn->query($sql_dispositivos);
$dispositivos_data = [];

if ($result_dispositivos && $result_dispositivos->num_rows > 0) {
    while($row = $result_dispositivos->fetch_assoc()) {
        $dispositivos_data[] = $row;
    }
}

$conn->close();
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Lista de Dispositivos - <?php echo $sede_nombre; ?></h2>
        <p>Gestiona y visualiza todos los dispositivos registrados en <?php echo $sede_nombre; ?></p>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <label for="filtroNombre">Filtro por Nombre</label>
            <input type="text" id="filtroNombre" placeholder="Buscar por nombre..." onkeyup="filtrarTabla()">
        </div>
        
        <div class="filter-group">
            <label for="filtroTipo">Filtro por Tipo</label>
            <select id="filtroTipo" onchange="filtrarTabla()">
                <option value="">Todos</option>
                <option value="Computador">Escritorio</option>
                <option value="Laptop">Laptop</option>
                <option value="Tablet">Tablet</option>
                <option value="Impresora">Impresora</option>
                <option value="Scanner">Scanner</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filtroEstado">Filtro por Estado</label>
            <select id="filtroEstado" onchange="filtrarTabla()">
                <option value="">Todos</option>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="En Mantenimiento">En Mantenimiento</option>
                <option value="Fuera de Servicio">Fuera de Servicio</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filtroUbicacion">Filtro por Ubicación</label>
            <input type="text" id="filtroUbicacion" placeholder="Buscar por ubicación..." onkeyup="filtrarTabla()">
        </div>

        <!-- Agregar este botón en la sección de filtros o acciones -->
        <div class="filter-actions">
            <button type="button" class="btn-export" onclick="exportarDispositivos()">
                📊 Exportar Dispositivos
            </button>
        </div>
        <!-- Agregar este campo hidden en el formulario de filtros -->
        <input type="hidden" id="current_sede_id" name="current_sede_id" value="<?php echo $sede_id; ?>">
    </div>

    <!-- Tabla de dispositivos -->
    <div class="table-container">
        <table class="data-table" id="tablaDispositivos">
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Ubicación</th>
                    <th>Tipo Activo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Capacidad de Disco</th>
                    <th>RAM</th>
                    <th>Procesador</th>
                    <th>Placa del Equipo</th>
                    <th>Placa Teclado</th>
                    <th>Serial Teclado</th>
                    <th>Entrega Teclado</th>
                    <th>Obs Teclado</th>
                    <th>Placa Mouse</th>
                    <th>Serial Mouse</th>
                    <th>Entrega Mouse</th>
                    <th>Obs Mouse</th>
                    <th>Placa Monitor</th>
                    <th>Serial Monitor</th>
                    <th>Entrega Monitor</th>
                    <th>Obs Monitor</th>
                    <th>Placa CPU</th>
                    <th>Responsable</th>
                    <th>Firma Acta</th>
                    <th>Borrado Seguro</th>
                    <th>Nombre Borrado</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Fecha Actualización</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($dispositivos_data) > 0): ?>
                    <?php foreach($dispositivos_data as $dispositivo): ?>
                        <tr>
                            <td style="display: flex; gap: 10px;">
                                <button class="btn-view" onclick="verDispositivo(<?php echo $dispositivo['id']; ?>)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn-edit" onclick="editarDispositivo(<?php echo $dispositivo['id']; ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn-delete" onclick="eliminarDispositivo(<?php echo $dispositivo['id']; ?>)">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </td>
                            <td><?php echo $dispositivo['ubicacion'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['tipo_activo'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['marca'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['modelo'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['claves_duro'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['ram'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['procesador'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['placa'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['placa_teclado'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['serial_teclado'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['entrega_teclado'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['obs_teclado'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['placa_mouse'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['serial_mouse'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['entrega_mouse'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['obs_mouse'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['placa_monitor'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['serial_monitor'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['entrega_monitor'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['obs_monitor'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['placa_cpu'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['responsable'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['firma_acta'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['borrado_seguro'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['nombre_borrado'] ?? ''; ?></td>
                            <td><span class="estado-badge estado-<?php echo strtolower(str_replace(' ', '-', $dispositivo['estado'] ?? '')); ?>"><?php echo $dispositivo['estado'] ?? ''; ?></span></td>
                            <td><?php echo $dispositivo['fecha'] ?? ''; ?></td>
                            <td><?php echo $dispositivo['fecha_actualizacion'] ?? ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="27" class="no-data">No hay dispositivos registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para ver dispositivo -->
<div id="modalVerDispositivo" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Detalles del Dispositivo</h3>
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
                        <label>Tipo Activo:</label>
                        <span id="detalleTipoActivo"></span>
                    </div>
                    <div class="detail-group">
                        <label>Marca:</label>
                        <span id="detalleMarca"></span>
                    </div>
                    <div class="detail-group">
                        <label>Modelo:</label>
                        <span id="detalleModelo"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>capacidad de disco:</label>
                        <span id="detalleClavesDuro"></span>
                    </div>
                    <div class="detail-group">
                        <label>RAM:</label>
                        <span id="detalleRam"></span>
                    </div>
                    <div class="detail-group">
                        <label>Procesador:</label>
                        <span id="detalleProcesador"></span>
                    </div>
                    
                    <div class="detail-group">
                        <label>Placa del Equipo:</label>
                        <span id="detallePlaca"></span>
                    </div>
                    
                    <div class="detail-group">
                        <label>Estado:</label>
                        <span id="detalleEstado"></span>
                    </div>
                    <div class="detail-group">
                        <label>Placa Teclado:</label>
                        <span id="detallePlacaTeclado"></span>
                    </div>
                    <div class="detail-group">
                        <label>Serial Teclado:</label>
                        <span id="detalleSerialTeclado"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Entrega Teclado:</label>
                        <span id="detalleEntregaTeclado"></span>
                    </div>
                    <div class="detail-group">
                        <label>Obs Teclado:</label>
                        <span id="detalleObsTeclado"></span>
                    </div>
                    <div class="detail-group">
                        <label>Placa Mouse:</label>
                        <span id="detallePlacaMouse"></span>
                    </div>
                    <div class="detail-group">
                        <label>Serial Mouse:</label>
                        <span id="detalleSerialMouse"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Entrega Mouse:</label>
                        <span id="detalleEntregaMouse"></span>
                    </div>
                    <div class="detail-group">
                        <label>Obs Mouse:</label>
                        <span id="detalleObsMouse"></span>
                    </div>
                    <div class="detail-group">
                        <label>Placa Monitor:</label>
                        <span id="detallePlacaMonitor"></span>
                    </div>
                    <div class="detail-group">
                        <label>Serial Monitor:</label>
                        <span id="detalleSerialMonitor"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Entrega Monitor:</label>
                        <span id="detalleEntregaMonitor"></span>
                    </div>
                    <div class="detail-group">
                        <label>Obs Monitor:</label>
                        <span id="detalleObsMonitor"></span>
                    </div>
                    <div class="detail-group">
                        <label>Placa CPU:</label>
                        <span id="detallePlacaCpu"></span>
                    </div>
                    <div class="detail-group">
                        <label>Responsable:</label>
                        <span id="detalleResponsable"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Firma Acta:</label>
                        <span id="detalleFirmaActa"></span>
                    </div>
                    <div class="detail-group">
                        <label>Borrado Seguro:</label>
                        <span id="detalleBorradoSeguro"></span>
                    </div>
                    <div class="detail-group">
                        <label>Nombre Borrado:</label>
                        <span id="detalleNombreBorrado"></span>
                    </div>
                    <div class="detail-group">
                        <label>Fecha:</label>
                        <span id="detalleFecha"></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-group">
                        <label>Fecha Actualización:</label>
                        <span id="detalleFechaActualizacion"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="cerrarModalVer()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalConfirmarEliminar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarModalConfirmar()">&times;</span>
        </div>
        
        <div class="modal-body">
            <p><i class="fas fa-exclamation-triangle" style="color: #e74c3c; margin-right: 10px;"></i>¿Estás seguro de que quieres eliminar este dispositivo?</p>
            <p><strong>⚠️ Esta acción no se puede deshacer y eliminará permanentemente todos los datos del dispositivo.</strong></p>
            <p style="color: #7f8c8d; font-size: 14px;">Haz clic en "Eliminar" solo si estás completamente seguro.</p>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="cerrarModalConfirmar()">Cancelar</button>
            <button type="button" class="btn-delete" onclick="confirmarEliminacion()">Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal para editar dispositivo -->
<div id="modalEditarDispositivo" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Dispositivo</h3>
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
        </div>
        
        <form method="POST" action="../controller/actualizar_dispositivo_controller.php" class="edit-form" 
            onsubmit="return confirmarActualizacionDispositivo();">
            <input type="hidden" name="actualizar_dispositivo" value="1">
        <script>
        function confirmarActualizacionDispositivo() {
            return confirm('¿Estás seguro de que deseas actualizar este dispositivo?');
        }
        </script>
            <input type="hidden" name="dispositivo_id" id="editDispositivoId">
            <input type="hidden" name="sede_id" value="<?php echo $sede_id; ?>">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="editUbicacion">Ubicación</label>
                        <input type="text" id="editUbicacion" name="ubicacion">
                    </div>
                    
                    <div class="form-group">
                        <label for="editTipoActivo">Tipo Activo</label>
                        <select id="editTipoActivo" name="tipo_activo">
                            <option value="">Seleccionar tipo</option>
                            <option value="Computador">Escritorio</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Impresora">Impresora</option>
                            <option value="Scanner">Scanner</option>
                            <option value="Otro">Otro</option>
                        </select>
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
                        <label for="editClavesDuro">capacidad de disco</label>
                        <input type="text" id="editClavesDuro" name="claves_duro">
                    </div>
                    
                    <div class="form-group">
                        <label for="editRam">RAM</label>
                        <input type="text" id="editRam" name="ram" placeholder="Ej: 8GB, 16GB">
                    </div>
                    
                    <div class="form-group">
                        <label for="editProcesador">Procesador</label>
                        <input type="text" id="editProcesador" name="procesador" placeholder="Ej: Intel i5, AMD Ryzen 5">
                    </div>
                    
                    <div class="form-group">
                        <label for="editPlaca">Placa del Equipo</label>
                        <input type="text" id="editPlaca" name="placa" placeholder="Ej: PLACA-001, EQ-2024-001">
                    </div>
                    
                    <div class="form-group">
                        <label for="editEstado">Estado</label>
                        <select id="editEstado" name="estado">
                            <option value="">Seleccionar estado</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="En Mantenimiento">En Mantenimiento</option>
                            <option value="Fuera de Servicio">Fuera de Servicio</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Información del Teclado</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editPlacaTeclado">Placa Teclado</label>
                            <input type="text" id="editPlacaTeclado" name="placa_teclado">
                        </div>
                        
                        <div class="form-group">
                            <label for="editSerialTeclado">Serial Teclado</label>
                            <input type="text" id="editSerialTeclado" name="serial_teclado">
                        </div>
                        
                        <div class="form-group">
                            <label for="editEntregaTeclado">Entrega Teclado</label>
                            <input type="text" id="editEntregaTeclado" name="entrega_teclado">
                        </div>
                        
                        <div class="form-group">
                            <label for="editObsTeclado">Obs Teclado</label>
                            <input type="text" id="editObsTeclado" name="obs_teclado">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Información del Mouse</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editPlacaMouse">Placa Mouse</label>
                            <input type="text" id="editPlacaMouse" name="placa_mouse">
                        </div>
                        
                        <div class="form-group">
                            <label for="editSerialMouse">Serial Mouse</label>
                            <input type="text" id="editSerialMouse" name="serial_mouse">
                        </div>
                        
                        <div class="form-group">
                            <label for="editEntregaMouse">Entrega Mouse</label>
                            <input type="text" id="editEntregaMouse" name="entrega_mouse">
                        </div>
                        
                        <div class="form-group">
                            <label for="editObsMouse">Obs Mouse</label>
                            <input type="text" id="editObsMouse" name="obs_mouse">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Información del Monitor</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editPlacaMonitor">Placa Monitor</label>
                            <input type="text" id="editPlacaMonitor" name="placa_monitor">
                        </div>
                        
                        <div class="form-group">
                            <label for="editSerialMonitor">Serial Monitor</label>
                            <input type="text" id="editSerialMonitor" name="serial_monitor">
                        </div>
                        
                        <div class="form-group">
                            <label for="editEntregaMonitor">Entrega Monitor</label>
                            <input type="text" id="editEntregaMonitor" name="entrega_monitor">
                        </div>
                        
                        <div class="form-group">
                            <label for="editObsMonitor">Obs Monitor</label>
                            <input type="text" id="editObsMonitor" name="obs_monitor">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Información Adicional</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editPlacaCpu">Placa CPU</label>
                            <input type="text" id="editPlacaCpu" name="placa_cpu">
                        </div>
                        
                        <div class="form-group">
                            <label for="editResponsable">Responsable</label>
                            <input type="text" id="editResponsable" name="responsable">
                        </div>
                        
                        <div class="form-group">
                            <label for="editFirmaActa">Firma Acta</label>
                            <input type="text" id="editFirmaActa" name="firma_acta">
                        </div>
                        
                        <div class="form-group">
                            <label for="editBorradoSeguro">Borrado Seguro</label>
                            <input type="text" id="editBorradoSeguro" name="borrado_seguro">
                        </div>
                        
                        <div class="form-group">
                            <label for="editNombreBorrado">Nombre Borrado</label>
                            <input type="text" id="editNombreBorrado" name="nombre_borrado">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="btn-primary">Actualizar Dispositivo</button>
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

.filters-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.filter-group input,
.filter-group select {
    padding: 10px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #3498db;
}

.table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 14px;
}

.data-table th {
    background: #3498db;
    color: white;
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
}

.data-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.no-data {
    text-align: center;
    color: #7f8c8d;
    font-style: italic;
    padding: 40px !important;
}

.btn-primary,
.btn-secondary {
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
}

.btn-edit {
    background: #3498db;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 5px;
    transition: background 0.3s;
    font-size: 12px;
}

.btn-edit:hover {
    background: #2980b9;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 12px;
}

.btn-delete:hover {
    background: #c0392b;
}

.btn-view {
    background: #17a2b8;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 5px;
    transition: background 0.3s;
    font-size: 12px;
}

.btn-view:hover {
    background: #138496;
}

.estado-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.estado-activo {
    background: #d4edda;
    color: #155724;
}

.estado-inactivo {
    background: #f8d7da;
    color: #721c24;
}

.estado-mantenimiento {
    background: #fff3cd;
    color: #856404;
}

.estado-fuera-servicio {
    background: #f5c6cb;
    color: #721c24;
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
    max-width: 600px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease-out;
}

.modal-large {
    max-width: 1200px;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
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
    transition: color 0.3s;
}

.close:hover {
    color: #e74c3c;
}

.modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}

/* Device Details Styles */
.device-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 15px;
}

.detail-group {
    display: flex;
    flex-direction: column;
}

.detail-group.full-width {
    grid-column: 1 / -1;
}

.detail-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 14px;
}

.detail-group span {
    color: #34495e;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #3498db;
    font-size: 14px;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    padding: 20px;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .filters-section {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .detail-row {
        grid-template-columns: 1fr 1fr;
    }
    
    .data-table {
        font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
        padding: 8px 4px;
    }
}

/* Estilos para el formulario de edición */
.edit-form {
    margin: 0;
}

.edit-form .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.edit-form .form-section {
    margin-bottom: 25px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 3px solid #3498db;
}

.edit-form .form-section h4 {
    margin: 0 0 15px 0;
    color: #2c3e50;
    font-size: 16px;
}

.edit-form .form-group {
    display: flex;
    flex-direction: column;
}

.edit-form .form-group label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 14px;
}

.edit-form .form-group input,
.edit-form .form-group select {
    padding: 8px 12px;
    border: 2px solid #e9ecef;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.edit-form .form-group input:focus,
.edit-form .form-group select:focus {
    outline: none;
    border-color: #3498db;
}

.edit-form .btn-primary {
    background: #27ae60;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
}

.edit-form .btn-primary:hover {
    background: #229954;
}

.edit-form .btn-secondary {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
}

.edit-form .btn-secondary:hover {
    background: #7f8c8d;
}

/* Estilos para el scroll del modal */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Asegurar que el modal tenga altura máxima en móviles */
@media (max-width: 768px) {
    .modal-body {
        max-height: 60vh;
    }
}

.btn-export {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
}
</style>

<script>
// Datos de dispositivos desde PHP
var dispositivos = <?php echo json_encode($dispositivos_data); ?>;

// Función para abrir el modal de ver
function abrirModalVer() {
    document.getElementById('modalVerDispositivo').style.display = 'block';
}

// Función para cerrar el modal de ver
function cerrarModalVer() {
    document.getElementById('modalVerDispositivo').style.display = 'none';
}

// Función para ver un dispositivo
function verDispositivo(id) {
    const dispositivo = dispositivos.find(d => parseInt(d.id) === parseInt(id));
    if (dispositivo) {
        // Llenar los detalles en el modal
        document.getElementById('detalleUbicacion').textContent = dispositivo.ubicacion || 'N/A';
        document.getElementById('detalleTipoActivo').textContent = dispositivo.tipo_activo || 'N/A';
        document.getElementById('detalleMarca').textContent = dispositivo.marca || 'N/A';
        document.getElementById('detalleModelo').textContent = dispositivo.modelo || 'N/A';
        document.getElementById('detalleClavesDuro').textContent = dispositivo.claves_duro || 'N/A';
        document.getElementById('detalleRam').textContent = dispositivo.ram || 'N/A';
        document.getElementById('detalleProcesador').textContent = dispositivo.procesador || 'N/A';
        document.getElementById('detallePlaca').textContent = dispositivo.placa || 'N/A';
        document.getElementById('detalleEstado').textContent = dispositivo.estado || 'N/A';
        document.getElementById('detallePlacaTeclado').textContent = dispositivo.placa_teclado || 'N/A';
        document.getElementById('detalleSerialTeclado').textContent = dispositivo.serial_teclado || 'N/A';
        document.getElementById('detalleEntregaTeclado').textContent = dispositivo.entrega_teclado || 'N/A';
        document.getElementById('detalleObsTeclado').textContent = dispositivo.obs_teclado || 'N/A';
        document.getElementById('detallePlacaMouse').textContent = dispositivo.placa_mouse || 'N/A';
        document.getElementById('detalleSerialMouse').textContent = dispositivo.serial_mouse || 'N/A';
        document.getElementById('detalleEntregaMouse').textContent = dispositivo.entrega_mouse || 'N/A';
        document.getElementById('detalleObsMouse').textContent = dispositivo.obs_mouse || 'N/A';
        document.getElementById('detallePlacaMonitor').textContent = dispositivo.placa_monitor || 'N/A';
        document.getElementById('detalleSerialMonitor').textContent = dispositivo.serial_monitor || 'N/A';
        document.getElementById('detalleEntregaMonitor').textContent = dispositivo.entrega_monitor || 'N/A';
        document.getElementById('detalleObsMonitor').textContent = dispositivo.obs_monitor || 'N/A';
        document.getElementById('detallePlacaCpu').textContent = dispositivo.placa_cpu || 'N/A';
        document.getElementById('detalleResponsable').textContent = dispositivo.responsable || 'N/A';
        document.getElementById('detalleFirmaActa').textContent = dispositivo.firma_acta || 'N/A';
        document.getElementById('detalleBorradoSeguro').textContent = dispositivo.borrado_seguro || 'N/A';
        document.getElementById('detalleNombreBorrado').textContent = dispositivo.nombre_borrado || 'N/A';
        document.getElementById('detalleFecha').textContent = dispositivo.fecha || 'N/A';
        document.getElementById('detalleFechaActualizacion').textContent = dispositivo.fecha_actualizacion || 'N/A';
        
        abrirModalVer();
    }
}

// Variables globales para la eliminación
var dispositivoAEliminar = null;

// Función para eliminar un dispositivo
function eliminarDispositivo(id) {
    dispositivoAEliminar = id;
    document.getElementById('modalConfirmarEliminar').style.display = 'block';
}

// Función para cerrar el modal de confirmación
function cerrarModalConfirmar() {
    document.getElementById('modalConfirmarEliminar').style.display = 'none';
    dispositivoAEliminar = null;
}

// Función para confirmar la eliminación
function confirmarEliminacion() {
    if (dispositivoAEliminar) {
        // Confirmación adicional antes de eliminar
        if (confirm('¿Estás completamente seguro de que quieres eliminar este dispositivo? Esta acción no se puede deshacer.')) {
            // Crear un formulario temporal para enviar la solicitud
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../controller/eliminar_dispositivo_controller.php';
            
            // Agregar el ID del dispositivo
            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'dispositivo_id';
            inputId.value = dispositivoAEliminar;
            form.appendChild(inputId);
            
            // Agregar el ID de la sede
            var inputSede = document.createElement('input');
            inputSede.type = 'hidden';
            inputSede.name = 'sede_id';
            inputSede.value = <?php echo $sede_id; ?>;
            form.appendChild(inputSede);
            
            // Agregar el formulario al DOM y enviarlo
            document.body.appendChild(form);
            form.submit();
        } else {
            // Si el usuario cancela, cerrar el modal
            cerrarModalConfirmar();
        }
    }
}

// Función para editar un dispositivo
function editarDispositivo(id) {
    // Buscar los datos del dispositivo en la tabla
    var tabla = document.getElementById('tablaDispositivos');
    var filas = tabla.getElementsByTagName('tr');
    
    for (var i = 1; i < filas.length; i++) {
        var fila = filas[i];
        var celdas = fila.getElementsByTagName('td');
        
        // Verificar si esta fila corresponde al dispositivo que queremos editar
        if (celdas.length > 0) {
            var botonEditar = celdas[0].querySelector('button[onclick*="editarDispositivo(' + id + ')"]');
            if (botonEditar) {
                // Llenar el formulario con los datos de la fila
                document.getElementById('editDispositivoId').value = id;
                document.getElementById('editUbicacion').value = celdas[1].textContent.trim();
                document.getElementById('editTipoActivo').value = celdas[2].textContent.trim();
                document.getElementById('editMarca').value = celdas[3].textContent.trim();
                document.getElementById('editModelo').value = celdas[4].textContent.trim();
                document.getElementById('editClavesDuro').value = celdas[5].textContent.trim();
                document.getElementById('editRam').value = celdas[6].textContent.trim();
                document.getElementById('editProcesador').value = celdas[7].textContent.trim();
                document.getElementById('editPlaca').value = celdas[8].textContent.trim();
                document.getElementById('editPlacaTeclado').value = celdas[9].textContent.trim();
                document.getElementById('editSerialTeclado').value = celdas[10].textContent.trim();
                document.getElementById('editEntregaTeclado').value = celdas[11].textContent.trim();
                document.getElementById('editObsTeclado').value = celdas[12].textContent.trim();
                document.getElementById('editPlacaMouse').value = celdas[13].textContent.trim();
                document.getElementById('editSerialMouse').value = celdas[14].textContent.trim();
                document.getElementById('editEntregaMouse').value = celdas[15].textContent.trim();
                document.getElementById('editObsMouse').value = celdas[16].textContent.trim();
                document.getElementById('editPlacaMonitor').value = celdas[17].textContent.trim();
                document.getElementById('editSerialMonitor').value = celdas[18].textContent.trim();
                document.getElementById('editEntregaMonitor').value = celdas[19].textContent.trim();
                document.getElementById('editObsMonitor').value = celdas[20].textContent.trim();
                document.getElementById('editPlacaCpu').value = celdas[21].textContent.trim();
                document.getElementById('editResponsable').value = celdas[22].textContent.trim();
                document.getElementById('editFirmaActa').value = celdas[23].textContent.trim();
                document.getElementById('editBorradoSeguro').value = celdas[24].textContent.trim();
                document.getElementById('editNombreBorrado').value = celdas[25].textContent.trim();
                document.getElementById('editEstado').value = celdas[26].textContent.trim();
                
                // Abrir el modal
                document.getElementById('modalEditarDispositivo').style.display = 'block';
                break;
            }
        }
    }
}

// Función para cerrar el modal de edición
function cerrarModalEditar() {
    document.getElementById('modalEditarDispositivo').style.display = 'none';
}

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function(event) {
    const modalVer = document.getElementById('modalVerDispositivo');
    const modalConfirmar = document.getElementById('modalConfirmarEliminar');
    const modalEditar = document.getElementById('modalEditarDispositivo');
    
    if (event.target == modalVer) {
        cerrarModalVer();
    }
    
    if (event.target == modalConfirmar) {
        cerrarModalConfirmar();
    }
    
    if (event.target == modalEditar) {
        cerrarModalEditar();
    }
}

// Función de filtrado simplificada
function filtrarTabla() {
    var filtroNombre = document.getElementById('filtroNombre').value.toLowerCase();
    var filtroTipo = document.getElementById('filtroTipo').value;
    var filtroEstado = document.getElementById('filtroEstado').value;
    var filtroUbicacion = document.getElementById('filtroUbicacion').value.toLowerCase();
    
    var tabla = document.getElementById('tablaDispositivos');
    var filas = tabla.getElementsByTagName('tr');
    
    for (var i = 1; i < filas.length; i++) {
        var fila = filas[i];
        var celdas = fila.getElementsByTagName('td');
        
        if (celdas.length > 23) {
            var ubicacion = celdas[1].textContent.toLowerCase();
            var tipo = celdas[2].textContent;
            var estado = celdas[23].textContent;
            
            var cumpleNombre = filtroNombre === '' || ubicacion.indexOf(filtroNombre) !== -1;
            var cumpleTipo = filtroTipo === '' || tipo === filtroTipo;
            var cumpleEstado = filtroEstado === '' || estado === filtroEstado;
            var cumpleUbicacion = filtroUbicacion === '' || ubicacion.indexOf(filtroUbicacion) !== -1;
            
            if (cumpleNombre && cumpleTipo && cumpleEstado && cumpleUbicacion) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        }
    }
}

// Agregar esta variable global al inicio del script
let sedeActual = <?php echo $sede_id; ?>;

function exportarDispositivos() {
    // Obtener la tabla de la página
    const tabla = document.querySelector('table tbody');
    if (!tabla) {
        alert('❌ No se encontró la tabla de dispositivos');
        return;
    }
    
    const filas = tabla.querySelectorAll('tr');
    if (filas.length === 0) {
        alert('❌ No hay dispositivos para exportar');
        return;
    }
    
    // Obtener encabezados de la tabla (excluyendo la columna de acciones)
    const tablaHeader = document.querySelector('table thead');
    const encabezados = [];
    if (tablaHeader) {
        const headerCells = tablaHeader.querySelectorAll('th');
        headerCells.forEach(cell => {
            const headerText = cell.textContent.trim();
            // Excluir la columna "Acciones"
            if (headerText !== 'Acciones') {
                encabezados.push(headerText);
            }
        });
    }
    
    // Obtener datos de las filas (excluyendo la columna de acciones)
    const datos = [];
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const filaData = [];
        celdas.forEach((celda, index) => {
            // Verificar si esta celda corresponde a la columna de acciones
            const headerCell = tablaHeader.querySelectorAll('th')[index];
            if (headerCell && headerCell.textContent.trim() !== 'Acciones') {
                filaData.push(celda.textContent.trim());
            }
        });
        datos.push(filaData);
    });
    
    // Crear contenido HTML con estilos para Excel
    let htmlContent = `
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 11px; }
            th { background-color: #2E75B6; color: white; font-weight: bold; text-align: center; }
            tr:nth-child(even) { background-color: #F8F9FA; }
            tr:hover { background-color: #E3F2FD; }
            .title { font-size: 16px; font-weight: bold; color: #2E75B6; text-align: center; margin-bottom: 10px; }
            .subtitle { font-size: 12px; color: #666; text-align: center; margin-bottom: 20px; }
            .summary { background-color: #E8F4FD; padding: 10px; border: 1px solid #2E75B6; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="title">📊 REPORTE DE DISPOSITIVOS - SEDE ${sedeActual || 'Actual'}</div>
        <div class="subtitle">Generado el: ${new Date().toLocaleString()}</div>
        <table>
            <thead>
                <tr>`;
    
    // Agregar encabezados (sin Acciones)
    encabezados.forEach(header => {
        htmlContent += `<th>${header}</th>`;
    });
    
    htmlContent += `
                </tr>
            </thead>
            <tbody>`;
    
    // Agregar datos (sin Acciones)
    datos.forEach(fila => {
        htmlContent += '<tr>';
        fila.forEach(celda => {
            htmlContent += `<td>${celda}</td>`;
        });
        htmlContent += '</tr>';
    });
    
    htmlContent += `
            </tbody>
        </table>
        <div class="summary">
            <strong>📈 Total de dispositivos exportados: ${datos.length}</strong>
        </div>
    </body>
    </html>`;
    
    // Crear y descargar archivo
    const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `dispositivos_sede_${sedeActual || 'actual'}_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.xls`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Mostrar mensaje de éxito
    alert(`✅ Se exportaron ${datos.length} dispositivos.`);
}

// Función para actualizar la sede cuando cambie el filtro
function actualizarSedeExportacion(sedeId) {
    sedeActual = sedeId;
}
</script>
