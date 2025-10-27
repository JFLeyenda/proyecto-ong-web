<?php
/**
 * Script para procesar el registro de nuevos proyectos
 * Semana 6 - Programación Web 2
 */

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_proyectos.php');
    exit();
}

// Incluir archivo de conexión
require_once 'conexion.php';

try {
    // Obtener y validar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $presupuesto = floatval($_POST['presupuesto'] ?? 0);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    
    // Validaciones básicas del lado del servidor
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = "El nombre del proyecto es obligatorio";
    }
    
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria";
    }
    
    if ($presupuesto <= 0) {
        $errores[] = "El presupuesto debe ser mayor a cero";
    }
    
    if (empty($fecha_inicio)) {
        $errores[] = "La fecha de inicio es obligatoria";
    }
    
    if (empty($fecha_fin)) {
        $errores[] = "La fecha de finalización es obligatoria";
    }
    
    // Validar que la fecha de fin sea posterior a la de inicio
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
            $errores[] = "La fecha de finalización debe ser posterior a la fecha de inicio";
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
        echo '<a href="formulario_proyectos.php">Volver al formulario</a>';
        echo '</div>';
        exit();
    }
    
    // Preparar y ejecutar la consulta de inserción
    $sql = "INSERT INTO PROYECTO (nombre, descripcion, presupuesto, fecha_inicio, fecha_fin) 
            VALUES (:nombre, :descripcion, :presupuesto, :fecha_inicio, :fecha_fin)";
    
    $parametros = [
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':presupuesto' => $presupuesto,
        ':fecha_inicio' => $fecha_inicio,
        ':fecha_fin' => $fecha_fin
    ];
    
    $stmt = ejecutarConsulta($pdo, $sql, $parametros);
    
    // Verificar si la inserción fue exitosa
    if ($stmt->rowCount() > 0) {
        // Redirigir con mensaje de éxito
        header('Location: formulario_proyectos.php?mensaje=exito');
    } else {
        // Redirigir con mensaje de error
        header('Location: formulario_proyectos.php?mensaje=error');
    }
    
} catch (Exception $e) {
    // En caso de error, mostrar mensaje y redirigir
    error_log("Error al procesar proyecto: " . $e->getMessage());
    header('Location: formulario_proyectos.php?mensaje=error');
}

// Cerrar conexión
cerrarConexion($pdo);
exit();
?>