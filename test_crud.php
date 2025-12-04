<?php
session_start();
require_once 'login/conexion.php';

echo "<h1>🧪 Prueba de CRUD - 4 Secciones</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
.success { background: #d4edda; color: #155724; }
.error { background: #f8d7da; color: #721c24; }
.info { background: #d1ecf1; color: #0c5460; }
.btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-primary { background: #007bff; color: white; }
.btn-success { background: #28a745; color: white; }
.btn-danger { background: #dc3545; color: white; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background: #f2f2f2; }
</style>";

$sede_id = 1;

// Función para verificar si una tabla existe
function tablaExiste($conn, $nombre_tabla) {
    $sql = "SHOW TABLES LIKE '$nombre_tabla'";
    $result = $conn->query($sql);
    return $result && $result->num_rows > 0;
}

// Función para insertar datos de prueba
function insertarDatosPrueba($conn, $tabla, $sede_id, $datos) {
    $campos = implode(', ', array_keys($datos));
    $valores = "'" . implode("', '", array_values($datos)) . "'";
    $sql = "INSERT INTO $tabla ($campos) VALUES ($valores)";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

// Función para mostrar datos de una tabla
function mostrarDatos($conn, $tabla, $sede_id) {
    $sql = "SELECT * FROM $tabla WHERE sede_id = '$sede_id' ORDER BY id DESC LIMIT 5";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>";
        while ($field = $result->fetch_field()) {
            echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay datos en la tabla $tabla</p>";
    }
}

echo "<h2>📋 Verificación de Tablas</h2>";

$tablas = [
    'puntos_datos' => 'Puntos de Datos',
    'puntos_regulares' => 'Puntos Regulares', 
    'puntos_ap' => 'Puntos AP',
    'aires_acondicionados' => 'Aires Acondicionados'
];

foreach ($tablas as $tabla => $nombre) {
    if (tablaExiste($conn, $tabla)) {
        echo "<div class='test-section success'>";
        echo "<h3>✅ Tabla '$nombre' existe</h3>";
        
        // Mostrar datos existentes
        echo "<h4>Datos existentes:</h4>";
        mostrarDatos($conn, $tabla, $sede_id);
        
        // Botón para insertar datos de prueba
        echo "<form method='POST' style='margin-top: 10px;'>";
        echo "<input type='hidden' name='insertar_prueba' value='$tabla'>";
        echo "<button type='submit' class='btn btn-primary'>Insertar Datos de Prueba</button>";
        echo "</form>";
        
        echo "</div>";
    } else {
        echo "<div class='test-section error'>";
        echo "<h3>❌ Tabla '$nombre' NO existe</h3>";
        echo "<p>Esta tabla debe ser creada para que el sistema funcione.</p>";
        echo "</div>";
    }
}

// Procesar inserción de datos de prueba
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar_prueba'])) {
    $tabla = $_POST['insertar_prueba'];
    
    switch ($tabla) {
        case 'puntos_datos':
            $datos = [
                'sede_id' => $sede_id,
                'numero' => 'PD-' . rand(100, 999),
                'ubicacion' => 'Sala ' . rand(1, 5),
                'estado' => 'Activo'
            ];
            break;
            
        case 'puntos_regulares':
            $datos = [
                'sede_id' => $sede_id,
                'numero' => 'PR-' . rand(100, 999),
                'ubicacion' => 'Sala ' . rand(1, 5),
                'estado' => 'Activo'
            ];
            break;
            
        case 'puntos_ap':
            $datos = [
                'sede_id' => $sede_id,
                'numero' => 'AP-' . rand(100, 999),
                'ubicacion' => 'Sala ' . rand(1, 5),
                'estado' => 'Activo'
            ];
            break;
            
        case 'aires_acondicionados':
            $datos = [
                'sede_id' => $sede_id,
                'numero' => 'AC-' . rand(100, 999),
                'ubicacion' => 'Sala ' . rand(1, 5),
                'estado' => 'Activo'
            ];
            break;
    }
    
    if (insertarDatosPrueba($conn, $tabla, $sede_id, $datos)) {
        echo "<div class='test-section success'>";
        echo "<h3>✅ Datos insertados correctamente en $tabla</h3>";
        echo "<p>ID insertado: " . $conn->insert_id . "</p>";
        echo "</div>";
    } else {
        echo "<div class='test-section error'>";
        echo "<h3>❌ Error al insertar datos en $tabla</h3>";
        echo "<p>Error: " . $conn->error . "</p>";
        echo "</div>";
    }
}

echo "<h2>🔗 Enlaces de Prueba</h2>";
echo "<div class='test-section info'>";
echo "<h3>Prueba las secciones:</h3>";
echo "<a href='admin/index.php?page=puntos_datos&sede_id=$sede_id' class='btn btn-primary'>Puntos de Datos</a>";
echo "<a href='admin/index.php?page=puntos_regulares&sede_id=$sede_id' class='btn btn-primary'>Puntos Regulares</a>";
echo "<a href='admin/index.php?page=puntos_ap&sede_id=$sede_id' class='btn btn-primary'>Puntos AP</a>";
echo "<a href='admin/index.php?page=aires_acondicionados&sede_id=$sede_id' class='btn btn-primary'>Aires Acondicionados</a>";
echo "</div>";

$conn->close();
?>
