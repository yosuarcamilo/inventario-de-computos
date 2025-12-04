<?php
session_start();
require_once '../login/conexion.php';

// Obtener la sede seleccionada del select
$sede_id = isset($_GET['sede_id']) ? (int)$_GET['sede_id'] : 1; // Por defecto sede 1

// Obtener información de la sede desde la base de datos
$sql_sede = "SELECT * FROM sedes WHERE id = '$sede_id'";
$result_sede = $conn->query($sql_sede);

if ($result_sede->num_rows > 0) {
    $sede = $result_sede->fetch_assoc();
} else {
    // Si no existe la sede, usar la primera disponible
    $sql_sede = "SELECT * FROM sedes ORDER BY id LIMIT 1";
    $result_sede = $conn->query($sql_sede);
    
    if ($result_sede->num_rows > 0) {
        $sede = $result_sede->fetch_assoc();
        $sede_id = $sede['id'];
    } else {
        // Si no hay sedes en la base de datos, crear datos por defecto
        $sede = [
            'id' => $sede_id,
            'nombre' => $sede_id == 1 ? 'SEDE PRINCIPAL' : 'SEDE MINERCOL',
            'ubicacion' => $sede_id == 1 ? 'Bogotá, Colombia' : 'Medellín, Colombia'
        ];
    }
}
    // Si hay error en la base de datos, usar datos por defecto
    $sede = [
        'id' => $sede_id,
        'nombre' => $sede_id == 1 ? 'SEDE PRINCIPAL' : 'SEDE MINERCOL',
        'ubicacion' => $sede_id == 1 ? 'Bogotá, Colombia' : 'Medellín, Colombia'
    ];


// Obtener el nombre del administrador que inició sesión
$nombre_admin = 'Usuario'; // Por defecto

// Intentar obtener el nombre del usuario de diferentes maneras
if (isset($_SESSION['usuario_id'])) {
    $sql = "SELECT nombre FROM usuarios WHERE id = '" . $_SESSION['usuario_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $nombre_admin = $usuario['nombre'];
    }
} elseif (isset($_SESSION['nombre_usuario'])) {
    $nombre_admin = $_SESSION['nombre_usuario'];
} elseif (isset($_SESSION['usuario'])) {
    $nombre_admin = $_SESSION['usuario'];
} else {
    // Si no hay sesión, obtener el primer usuario admin de la base de datos
    $sql = "SELECT nombre FROM usuarios WHERE tipo = 'ADMIN' ORDER BY id LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $nombre_admin = $usuario['nombre'];
    }
}

// Obtener estadísticas de la sede seleccionada
// Total dispositivos en esta sede
$sql = "SELECT COUNT(*) as total FROM dispositivos WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_dispositivos = $result->fetch_assoc()['total'];

// Total ubicaciones únicas en esta sede (de todas las tablas)
$sql_ubicaciones = "
    SELECT COUNT(DISTINCT ubicacion) as total FROM (
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
";
$result = $conn->query($sql_ubicaciones);
$total_ubicaciones = $result->fetch_assoc()['total'];

// Total puntos de datos en esta sede
$sql = "SELECT COUNT(*) as total FROM puntos_datos WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_puntos_datos = $result->fetch_assoc()['total'];

// Total puntos regulares en esta sede
$sql = "SELECT COUNT(*) as total FROM puntos_regulares WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_puntos_regulares = $result->fetch_assoc()['total'];

// Total puntos AP en esta sede
$sql = "SELECT COUNT(*) as total FROM puntos_ap WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_puntos_ap = $result->fetch_assoc()['total'];

// Total aires acondicionados en esta sede
$sql = "SELECT COUNT(*) as total FROM aires_acondicionados WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_aires = $result->fetch_assoc()['total'];

// Total puntos comerciales en esta sede
$sql = "SELECT COUNT(*) as total FROM puntos_comerciales WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_puntos_comerciales = $result->fetch_assoc()['total'];

// Total puntos switch en esta sede
$sql = "SELECT COUNT(*) as total FROM puntos_switch WHERE sede_id = '$sede_id'";
$result = $conn->query($sql);
$total_puntos_switch = $result->fetch_assoc()['total'];

// Debug: mostrar información de la sesión
echo "<!-- Debug: ";
echo "SESSION: " . print_r($_SESSION, true);
echo "Nombre obtenido: " . $nombre_admin;
echo " -->";
?>

<div class="dashboard-welcome">
    <div class="welcome-header">
        <div class="welcome-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="welcome-text">
            <h1>Bienvenido <?php echo htmlspecialchars($nombre_admin); ?></h1>
            <p class="sede-info">Sede actual: <strong><?php echo htmlspecialchars($sede['nombre']); ?></strong></p>
            <p class="sede-ubicacion">Ubicación: Quibdo - Choco</p>
        </div>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-desktop"></i>
            </div>
            <div class="stat-content">
                <h3>Total Dispositivos</h3>
                <p class="stat-number"><?php echo $total_dispositivos; ?></p>
                <p class="stat-label">Equipos registrados en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="stat-content">
                <h3>Total de Ambientes</h3>
                <p class="stat-number"><?php echo $total_ubicaciones; ?></p>
                <p class="stat-label">Ambientes únicos en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="stat-content">
                <h3>Puntos de Datos</h3>
                <p class="stat-number"><?php echo $total_puntos_datos; ?></p>
                <p class="stat-label">Conexiones de red en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="stat-content">
                <h3>Puntos Regulados</h3>
                <p class="stat-number"><?php echo $total_puntos_regulares; ?></p>
                <p class="stat-label">Conexiones eléctricas en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-plug"></i>
            </div>
            <div class="stat-content">
                <h3>Puntos AP</h3>
                <p class="stat-number"><?php echo $total_puntos_ap; ?></p>
                <p class="stat-label">Puntos de acceso en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-snowflake"></i>
            </div>
            <div class="stat-content">
                <h3>Aires Acondicionados</h3>
                <p class="stat-number"><?php echo $total_aires; ?></p>
                <p class="stat-label">Sistemas funcionando en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-content">
                <h3>Puntos Comerciales</h3>
                <p class="stat-number"><?php echo $total_puntos_comerciales; ?></p>
                <p class="stat-label">Puntos comerciales en esta sede</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-network-wired"></i>
            </div>
            <div class="stat-content">
                <h3>Puntos Switch</h3>
                <p class="stat-number"><?php echo $total_puntos_switch; ?></p>
                <p class="stat-label">Puntos switch en esta sede</p>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-welcome {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.welcome-header {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.welcome-icon {
    font-size: 60px;
    margin-right: 30px;
    opacity: 0.9;
}

.welcome-text h1 {
    font-size: 36px;
    margin: 0 0 10px 0;
    font-weight: 300;
}

.sede-info {
    font-size: 18px;
    opacity: 0.9;
    margin: 0 0 5px 0;
}

.sede-ubicacion {
    font-size: 16px;
    opacity: 0.8;
    margin: 0;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #3498db;
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 40px;
    color: #3498db;
    margin-bottom: 15px;
}

.stat-content h3 {
    color: #2c3e50;
    margin: 0 0 10px 0;
    font-size: 18px;
}

.stat-number {
    font-size: 36px;
    font-weight: bold;
    color: #27ae60;
    margin: 0 0 5px 0;
}

.stat-label {
    color: #7f8c8d;
    margin: 0;
    font-size: 14px;
}

@media (max-width: 768px) {
    .welcome-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 20px;
    }
    
    .welcome-icon {
        margin-right: 0;
        margin-bottom: 20px;
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
}
</style>


