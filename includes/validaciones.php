<?php
/**
 * Sistema de validaciones para evitar duplicados
 * Incluye validaciones para dispositivos, puntos switch y otros puntos
 */

require_once '../login/conexion.php';

class ValidadorDuplicados {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Validar duplicados en dispositivos (validación global en todo el sistema)
     */
    public function validarDispositivo($datos, $excluir_id = null) {
        $errores = [];
        
        // Campos a validar en dispositivos
        $campos_validar = [
            'placa' => 'Placa del Equipo',
            'placa_teclado' => 'Placa Teclado',
            'placa_monitor' => 'Placa Monitor',
            'serial_teclado' => 'Serial Teclado',
            'serial_monitor' => 'Serial Monitor',
            'placa_mouse' => 'Placa Mouse',
            'placa_cpu' => 'Placa CPU',
            'serial_mouse' => 'Serial Mouse'
        ];
        
        foreach ($campos_validar as $campo => $nombre_campo) {
            if (!empty($datos[$campo])) {
                $sql = "SELECT d.id, d.ubicacion, s.nombre as sede FROM dispositivos d 
                        INNER JOIN sedes s ON d.sede_id = s.id 
                        WHERE d.$campo = ?";
                if ($excluir_id) {
                    $sql .= " AND d.id != ?";
                }
                
                $stmt = $this->conn->prepare($sql);
                if ($excluir_id) {
                    $stmt->bind_param("si", $datos[$campo], $excluir_id);
                } else {
                    $stmt->bind_param("s", $datos[$campo]);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $errores[] = "El campo '$nombre_campo' con valor '$datos[$campo]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
                }
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en puntos switch (validación global en todo el sistema)
     */
    public function validarPuntoSwitch($datos, $excluir_id = null) {
        $errores = [];
        
        // Campos a validar en puntos switch
        $campos_validar = [
            'modelo' => 'Modelo',
            'placa' => 'Placa',
            'serial' => 'Serial'
        ];
        
        foreach ($campos_validar as $campo => $nombre_campo) {
            if (!empty($datos[$campo])) {
                $sql = "SELECT ps.id, ps.ubicacion, s.nombre as sede FROM puntos_switch ps 
                        INNER JOIN sedes s ON ps.sede_id = s.id 
                        WHERE ps.$campo = ?";
                if ($excluir_id) {
                    $sql .= " AND ps.id != ?";
                }
                
                $stmt = $this->conn->prepare($sql);
                if ($excluir_id) {
                    $stmt->bind_param("si", $datos[$campo], $excluir_id);
                } else {
                    $stmt->bind_param("s", $datos[$campo]);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $errores[] = "El campo '$nombre_campo' con valor '$datos[$campo]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
                }
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en puntos regulares (validación global en todo el sistema)
     */
    public function validarPuntoRegular($datos, $excluir_id = null) {
        $errores = [];
        
        // Validar número en puntos regulares
        if (!empty($datos['numero'])) {
            $sql = "SELECT pr.id, pr.ubicacion, s.nombre as sede FROM puntos_regulares pr 
                    INNER JOIN sedes s ON pr.sede_id = s.id 
                    WHERE pr.numero = ?";
            if ($excluir_id) {
                $sql .= " AND pr.id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excluir_id) {
                $stmt->bind_param("si", $datos['numero'], $excluir_id);
            } else {
                $stmt->bind_param("s", $datos['numero']);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $errores[] = "El número '$datos[numero]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en puntos de datos (validación global en todo el sistema)
     */
    public function validarPuntoDatos($datos, $excluir_id = null) {
        $errores = [];
        
        // Validar número en puntos de datos
        if (!empty($datos['numero'])) {
            $sql = "SELECT pd.id, pd.ubicacion, s.nombre as sede FROM puntos_datos pd 
                    INNER JOIN sedes s ON pd.sede_id = s.id 
                    WHERE pd.numero = ?";
            if ($excluir_id) {
                $sql .= " AND pd.id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excluir_id) {
                $stmt->bind_param("si", $datos['numero'], $excluir_id);
            } else {
                $stmt->bind_param("s", $datos['numero']);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $errores[] = "El número '$datos[numero]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en puntos AP (validación global en todo el sistema)
     */
    public function validarPuntoAP($datos, $excluir_id = null) {
        $errores = [];
        
        // Validar número en puntos AP
        if (!empty($datos['numero'])) {
            $sql = "SELECT pa.id, pa.ubicacion, s.nombre as sede FROM puntos_ap pa 
                    INNER JOIN sedes s ON pa.sede_id = s.id 
                    WHERE pa.numero = ?";
            if ($excluir_id) {
                $sql .= " AND pa.id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excluir_id) {
                $stmt->bind_param("si", $datos['numero'], $excluir_id);
            } else {
                $stmt->bind_param("s", $datos['numero']);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $errores[] = "El número '$datos[numero]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en puntos comerciales (validación global en todo el sistema)
     */
    public function validarPuntoComercial($datos, $excluir_id = null) {
        $errores = [];
        
        // Validar número en puntos comerciales
        if (!empty($datos['numero'])) {
            $sql = "SELECT pc.id, pc.ubicacion, s.nombre as sede FROM puntos_comerciales pc 
                    INNER JOIN sedes s ON pc.sede_id = s.id 
                    WHERE pc.numero = ?";
            if ($excluir_id) {
                $sql .= " AND pc.id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excluir_id) {
                $stmt->bind_param("si", $datos['numero'], $excluir_id);
            } else {
                $stmt->bind_param("s", $datos['numero']);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $errores[] = "El número '$datos[numero]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar duplicados en aires acondicionados (validación global en todo el sistema)
     */
    public function validarAireAcondicionado($datos, $excluir_id = null) {
        $errores = [];
        
        // Validar número en aires acondicionados
        if (!empty($datos['numero'])) {
            $sql = "SELECT aa.id, aa.ubicacion, s.nombre as sede FROM aires_acondicionados aa 
                    INNER JOIN sedes s ON aa.sede_id = s.id 
                    WHERE aa.numero = ?";
            if ($excluir_id) {
                $sql .= " AND aa.id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excluir_id) {
                $stmt->bind_param("si", $datos['numero'], $excluir_id);
            } else {
                $stmt->bind_param("s", $datos['numero']);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $errores[] = "El número '$datos[numero]' ya existe en la ubicación '{$row['ubicacion']}' de la sede '{$row['sede']}'";
            }
        }
        
        return $errores;
    }
    
    /**
     * Función auxiliar para mostrar errores en formato HTML
     */
    public function mostrarErrores($errores) {
        if (empty($errores)) {
            return '';
        }
        
        $html = '<div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                    <h5><i class="fas fa-exclamation-triangle"></i> Se encontraron duplicados:</h5>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">';
        
        foreach ($errores as $error) {
            $html .= "<li>$error</li>";
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }
}

// Función helper para crear instancia del validador
function crearValidador() {
    global $conn;
    return new ValidadorDuplicados($conn);
}
?>
