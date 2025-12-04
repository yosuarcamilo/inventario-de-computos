<?php
require_once 'login/conexion.php';

echo "<h2>Columnas que se muestran en la tabla de dispositivos:</h2>";

// Obtener una muestra de datos para ver la estructura
$sql = "SELECT * FROM dispositivos LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h3>Columnas disponibles:</h3>";
    echo "<ul>";
    foreach ($row as $column => $value) {
        echo "<li><strong>$column</strong>: " . ($value ? $value : '(vacío)') . "</li>";
    }
    echo "</ul>";
} else {
    echo "No hay datos en la tabla dispositivos";
}

$conn->close();
?>
