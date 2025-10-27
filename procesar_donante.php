<?php
/**
 * Script para procesar el registro de nuevos donantes
 * Semana 6 - Programación Web 2
 */

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario_donantes.php');
    exit();
}

// Incluir archivo de conexión
require_once 'conexion.php';

try {
    // Obtener y validar los datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    
    // Validaciones básicas del lado del servidor
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    }
    
    if (empty($email)) {
        $errores[] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no tiene un formato válido";
    }
    
    // Validar formato de teléfono chileno (opcional)
    if (!empty($telefono) && !preg_match('/^\+56[0-9]{9}$/', $telefono)) {
        $errores[] = "El teléfono debe tener el formato +56912345678";
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
        echo '<a href="formulario_donantes.php">Volver al formulario</a>';
        echo '</div>';
        exit();
    }
    
    // Verificar si el email ya existe
    $sqlVerificar = "SELECT COUNT(*) FROM DONANTE WHERE email = :email";
    $stmtVerificar = ejecutarConsulta($pdo, $sqlVerificar, [':email' => $email]);
    
    if ($stmtVerificar->fetchColumn() > 0) {
        header('Location: formulario_donantes.php?mensaje=duplicado');
        exit();
    }
    
    // Preparar y ejecutar la consulta de inserción
    $sql = "INSERT INTO DONANTE (nombre, email, direccion, telefono) 
            VALUES (:nombre, :email, :direccion, :telefono)";
    
    $parametros = [
        ':nombre' => $nombre,
        ':email' => $email,
        ':direccion' => $direccion,
        ':telefono' => $telefono
    ];
    
    $stmt = ejecutarConsulta($pdo, $sql, $parametros);
    
    // Verificar si la inserción fue exitosa
    if ($stmt->rowCount() > 0) {
        // Redirigir con mensaje de éxito
        header('Location: formulario_donantes.php?mensaje=exito');
    } else {
        // Redirigir con mensaje de error
        header('Location: formulario_donantes.php?mensaje=error');
    }
    
} catch (Exception $e) {
    // En caso de error, mostrar mensaje y redirigir
    error_log("Error al procesar donante: " . $e->getMessage());
    header('Location: formulario_donantes.php?mensaje=error');
}

// Cerrar conexión
cerrarConexion($pdo);
exit();
?>