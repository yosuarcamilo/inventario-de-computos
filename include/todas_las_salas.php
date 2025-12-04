<?php
session_start();
require_once '../login/conexion.php';

// Obtener la sede seleccionada del select
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1;

// Una sola consulta para obtener todas las ubicaciones únicas de todas las tablas
$sql_todas_ubicaciones = "
    SELECT DISTINCT ubicacion FROM (
        SELECT ubicacion FROM dispositivos WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM puntos_datos WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM puntos_regulares WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM puntos_ap WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM aires_acondicionados WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM puntos_comerciales WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
        UNION
        SELECT ubicacion FROM puntos_switch WHERE sede_id = '$sede_id' AND ubicacion IS NOT NULL AND ubicacion != ''
    ) AS todas_las_ubicaciones
    ORDER BY ubicacion
";

$result_ubicaciones = $conn->query($sql_todas_ubicaciones);
$ubicaciones_disponibles = [];

if ($result_ubicaciones && $result_ubicaciones->num_rows > 0) {
    while($row = $result_ubicaciones->fetch_assoc()) {
        $ubicaciones_disponibles[] = $row['ubicacion'];
    }
}

// Obtener estadísticas por ubicación (comparación exacta)
$ubicaciones_stats = [];
foreach($ubicaciones_disponibles as $ubicacion) {
    // Contar dispositivos por ubicación
    $sql_dispositivos = "SELECT COUNT(*) as total FROM dispositivos WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_dispositivos = $conn->query($sql_dispositivos);
    $total_dispositivos = $result_dispositivos->fetch_assoc()['total'];
    
    // Contar puntos de datos por ubicación
    $sql_puntos_datos = "SELECT COUNT(*) as total FROM puntos_datos WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_puntos_datos = $conn->query($sql_puntos_datos);
    $total_puntos_datos = $result_puntos_datos->fetch_assoc()['total'];
    
    // Contar puntos regulares por ubicación
    $sql_puntos_regulares = "SELECT COUNT(*) as total FROM puntos_regulares WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_puntos_regulares = $conn->query($sql_puntos_regulares);
    $total_puntos_regulares = $result_puntos_regulares->fetch_assoc()['total'];
    
    // Contar puntos AP por ubicación
    $sql_puntos_ap = "SELECT COUNT(*) as total FROM puntos_ap WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_puntos_ap = $conn->query($sql_puntos_ap);
    $total_puntos_ap = $result_puntos_ap->fetch_assoc()['total'];
    
    // Contar aires acondicionados por ubicación
    $sql_aires = "SELECT COUNT(*) as total FROM aires_acondicionados WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_aires = $conn->query($sql_aires);
    $total_aires = $result_aires->fetch_assoc()['total'];
    
    // Contar puntos comerciales por ubicación
    $sql_puntos_comerciales = "SELECT COUNT(*) as total FROM puntos_comerciales WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_puntos_comerciales = $conn->query($sql_puntos_comerciales);
    $total_puntos_comerciales = $result_puntos_comerciales->fetch_assoc()['total'];
    
    // Contar puntos switch por ubicación
    $sql_puntos_switch = "SELECT COUNT(*) as total FROM puntos_switch WHERE sede_id = '$sede_id' AND ubicacion = '" . $conn->real_escape_string($ubicacion) . "'";
    $result_puntos_switch = $conn->query($sql_puntos_switch);
    $total_puntos_switch = $result_puntos_switch->fetch_assoc()['total'];
    
    $ubicaciones_stats[$ubicacion] = [
        'dispositivos' => $total_dispositivos,
        'puntos_datos' => $total_puntos_datos,
        'puntos_regulares' => $total_puntos_regulares,
        'puntos_ap' => $total_puntos_ap,
        'aires' => $total_aires,
        'puntos_comerciales' => $total_puntos_comerciales,
        'puntos_switch' => $total_puntos_switch
    ];
}

// Calcular totales generales
$total_dispositivos_general = array_sum(array_column($ubicaciones_stats, 'dispositivos'));
$total_puntos_datos_general = array_sum(array_column($ubicaciones_stats, 'puntos_datos'));
$total_puntos_regulares_general = array_sum(array_column($ubicaciones_stats, 'puntos_regulares'));
$total_puntos_ap_general = array_sum(array_column($ubicaciones_stats, 'puntos_ap'));
$total_aires_general = array_sum(array_column($ubicaciones_stats, 'aires'));
$total_puntos_comerciales_general = array_sum(array_column($ubicaciones_stats, 'puntos_comerciales'));
$total_puntos_switch_general = array_sum(array_column($ubicaciones_stats, 'puntos_switch'));

$conn->close();
?>

<div class="content-section">
    <div class="section-header">
        <h2><i class="fas fa-map-marker-alt"></i> Todas las ambientes</h2>
        <p>Gestiona y visualiza todas las ambientes únicos con sus dispositivos registrados</p>
    </div>

    <!-- Filtros -->
    <div class="filters-container">
        <div class="filter-group">
            <label for="filtroUbicacion">Filtro por ambiente:</label>
            <select id="filtroUbicacion" class="filter-select">
                <option value="">Todas las ambientes</option>
                <?php foreach($ubicaciones_disponibles as $ubicacion): ?>
                    <option value="<?php echo htmlspecialchars($ubicacion); ?>"><?php echo htmlspecialchars($ubicacion); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Estadísticas generales -->
    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalSalas"><?php echo count($ubicaciones_disponibles); ?></h3>
                <p>Total de ambientes</p>
            </div>
        </div>
        <div class="stat-card dispositivos">
            <div class="stat-icon">
                <i class="fas fa-desktop"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalDispositivos"><?php echo $total_dispositivos_general; ?></h3>
                <p>Total Dispositivos</p>
            </div>
        </div>
        <div class="stat-card puntos-datos">
            <div class="stat-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalPuntosDatos"><?php echo $total_puntos_datos_general; ?></h3>
                <p>Puntos de Datos</p>
            </div>
        </div>
        <div class="stat-card puntos-regulares">
            <div class="stat-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalPuntosRegulares"><?php echo $total_puntos_regulares_general; ?></h3>
                <p>Puntos Regulados</p>
            </div>
        </div>
        <div class="stat-card puntos-ap">
            <div class="stat-icon">
                <i class="fas fa-wifi"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalPuntosAP"><?php echo $total_puntos_ap_general; ?></h3>
                <p>Puntos AP</p>
            </div>
        </div>
        <div class="stat-card aires">
            <div class="stat-icon">
                <i class="fas fa-snowflake"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalAires"><?php echo $total_aires_general; ?></h3>
                <p>Aires Acondicionados</p>
            </div>
        </div>
        <div class="stat-card puntos-comerciales">
            <div class="stat-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalPuntosComerciales"><?php echo $total_puntos_comerciales_general; ?></h3>
                <p>Puntos Comerciales</p>
            </div>
        </div>

        <div class="stat-card puntos-switch">
            <div class="stat-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div class="stat-content">
                <h3 id="totalPuntosSwitch"><?php echo $total_puntos_switch_general; ?></h3>
                <p>Puntos Switch</p>
            </div>
        </div>
    </div>

    <!-- Lista de ambientes -->
    <div class="salas-container">
        <div id="salasList" class="salas-grid">
            <?php if (count($ubicaciones_disponibles) > 0): ?>
                <?php foreach($ubicaciones_disponibles as $ubicacion): ?>
                    <div class="sala-card">
                        <div class="sala-header">
                            <h3 class="sala-nombre"><?php echo htmlspecialchars($ubicacion); ?></h3>
                            <span class="sala-estado activa">Activa</span>
                        </div>
                        <div class="sala-stats">
                            <div class="stat-item dispositivos">
                                <span class="stat-label">Dispositivos</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['dispositivos']; ?></span>
                            </div>
                            <div class="stat-item puntos-datos">
                                <span class="stat-label">Puntos de Datos</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['puntos_datos']; ?></span>
                            </div>
                            <div class="stat-item puntos-regulares">
                                <span class="stat-label">Puntos Regulados</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['puntos_regulares']; ?></span>
                            </div>
                            <div class="stat-item puntos-ap">
                                <span class="stat-label">Puntos AP</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['puntos_ap']; ?></span>
                            </div>
                            <div class="stat-item aires">
                                <span class="stat-label">Aires Acondicionados</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['aires']; ?></span>
                            </div>
 se                             <div class="stat-item puntos-comerciales">
                                <span class="stat-label">Puntos Comerciales</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['puntos_comerciales']; ?></span>
                            </div>
                            <div class="stat-item puntos-switch">
                                <span class="stat-label">Puntos Switch</span>
                                <span class="stat-value"><?php echo $ubicaciones_stats[$ubicacion]['puntos_switch']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-salas">
                    <p>No hay ambientes registrados en esta sede</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
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

.filters-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 15px;
}

.filter-group label {
    font-weight: 600;
    color: #2c3e50;
    min-width: 150px;
}

.filter-select {
    padding: 10px 15px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    color: #2c3e50;
    min-width: 200px;
}

.filter-select:focus {
    outline: none;
    border-color: #3498db;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-card.total .stat-icon {
    background: #3498db;
}

.stat-card.dispositivos .stat-icon {
    background: #27ae60;
}

.stat-card.puntos-datos .stat-icon {
    background: #f39c12;
}

.stat-card.puntos-regulares .stat-icon {
    background: #9b59b6;
}

.stat-card.puntos-ap .stat-icon {
    background: #e74c3c;
}

.stat-card.aires .stat-icon {
    background: #1abc9c;
}

.stat-card.puntos-comerciales .stat-icon {
    background: #e67e22;
}

.stat-card.puntos-switch .stat-icon {
    background: #8e44ad;
}

.stat-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-size: 14px;
}

.salas-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.salas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.sala-card {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
    cursor: pointer;
}

.sala-card:hover {
    border-color: #3498db;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.sala-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.sala-nombre {
    font-size: 18px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.sala-estado {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.sala-estado.activa {
    background: #d4edda;
    color: #155724;
}

.sala-estado.inactiva {
    background: #f8d7da;
    color: #721c24;
}

.sala-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: white;
    border-radius: 4px;
    border-left: 3px solid;
}

.stat-item.dispositivos {
    border-left-color: #27ae60;
}

.stat-item.puntos-datos {
    border-left-color: #f39c12;
}

.stat-item.puntos-regulares {
    border-left-color: #9b59b6;
}

.stat-item.puntos-ap {
    border-left-color: #e74c3c;
}

.stat-item.aires {
    border-left-color: #1abc9c;
}

.stat-item.puntos-comerciales {
    border-left-color: #e67e22;
}

.stat-item.puntos-switch {
    border-left-color: #8e44ad;
}

.stat-label {
    font-size: 12px;
    color: #7f8c8d;
    font-weight: 500;
}

.stat-value {
    font-size: 16px;
    font-weight: bold;
    color: #2c3e50;
}

.no-salas {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
    font-style: italic;
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .salas-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .filter-group label {
        min-width: auto;
    }
    
    .filter-select {
        min-width: 100%;
    }
}
</style>

<script>
// Función para filtrar ubicaciones
function filtrarUbicaciones() {
    const filtroUbicacion = document.getElementById('filtroUbicacion').value;
    const ubicacionesCards = document.querySelectorAll('.sala-card');
    
    ubicacionesCards.forEach(card => {
        const nombreUbicacion = card.querySelector('.sala-nombre').textContent;
        
        if (!filtroUbicacion || nombreUbicacion === filtroUbicacion) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Event listeners
document.getElementById('filtroUbicacion').addEventListener('change', function() {
    filtrarUbicaciones();
});

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // El filtro ya está configurado para funcionar con los datos reales
    console.log('Página de ubicaciones cargada correctamente');
});
</script>