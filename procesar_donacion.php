<?php
/**
 * Script para procesar el registro de nuevas donaciones
 * Semana 6 - Programación Web 2
 */

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_donaciones.php');
    exit();
}

// Incluir archivo de conexión
require_once 'conexion.php';

try {
    // Obtener y validar los datos del formulario
    $monto = floatval($_POST['monto'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $id_proyecto = intval($_POST['id_proyecto'] ?? 0);
    $id_donante = intval($_POST['id_donante'] ?? 0);
    
    // Validaciones básicas del lado del servidor
    $errores = [];
    
    if ($monto <= 0) {
        $errores[] = "El monto debe ser mayor a cero";
    }
    
    if (empty($fecha)) {
        $errores[] = "La fecha es obligatoria";
    } elseif (strtotime($fecha) > time()) {
        $errores[] = "La fecha no puede ser futura";
    }
    
    if ($id_proyecto <= 0) {
        $errores[] = "Debe seleccionar un proyecto válido";
    }
    
    if ($id_donante <= 0) {
        $errores[] = "Debe seleccionar un donante válido";
    }
    
    // Verificar que el proyecto exista y esté activo
    if ($id_proyecto > 0) {
        $sqlProyecto = "SELECT COUNT(*) FROM PROYECTO WHERE id_proyecto = :id AND fecha_fin >= CURDATE()";
        $stmtProyecto = ejecutarConsulta($pdo, $sqlProyecto, [':id' => $id_proyecto]);
        
        if ($stmtProyecto->fetchColumn() == 0) {
            $errores[] = "El proyecto seleccionado no existe o ya ha finalizado";
        }
    }
    
    // Verificar que el donante exista
    if ($id_donante > 0) {
        $sqlDonante = "SELECT COUNT(*) FROM DONANTE WHERE id_donante = :id";
        $stmtDonante = ejecutarConsulta($pdo, $sqlDonante, [':id' => $id_donante]);
        
        if ($stmtDonante->fetchColumn() == 0) {
            $errores[] = "El donante seleccionado no existe";
        }
    }
    
    // Si hay errores, mostrarlos
    if (!empty($errores)) {
        echo '<div class="error">';
        echo '<h3>Se encontraron los siguientes errores:</h3>';
        echo '<ul>';
        foreach ($errores as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
        echo '<a href="formulario_donaciones.php">Volver al formulario</a>';
        echo '</div>';
        exit();
    }
    
    // Preparar y ejecutar la consulta de inserción
    $sql = "INSERT INTO DONACION (monto, fecha, id_proyecto, id_donante) 
            VALUES (:monto, :fecha, :id_proyecto, :id_donante)";
    
    $parametros = [
        ':monto' => $monto,
        ':fecha' => $fecha,
        ':id_proyecto' => $id_proyecto,
        ':id_donante' => $id_donante
    ];
    
    $stmt = ejecutarConsulta($pdo, $sql, $parametros);
    
    // Verificar si la inserción fue exitosa
    if ($stmt->rowCount() > 0) {
        // Redirigir con mensaje de éxito
        header('Location: formulario_donaciones.php?mensaje=exito');
    } else {
        // Redirigir con mensaje de error
        header('Location: formulario_donaciones.php?mensaje=error');
    }
    
} catch (Exception $e) {
    // En caso de error, mostrar mensaje y redirigir
    error_log("Error al procesar donación: " . $e->getMessage());
    header('Location: formulario_donaciones.php?mensaje=error');
}

// Cerrar conexión
cerrarConexion($pdo);
exit();
?>