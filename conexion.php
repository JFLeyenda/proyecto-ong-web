<?php
/**
 * Archivo de conexión a la base de datos MySQL
 * Semana 6 - Programación Web 2
 * 
 * Este archivo establece una conexión segura con la base de datos ORGANIZACION
 * utilizando PDO para mayor seguridad y mejor manejo de errores
 */

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'ORGANIZACION';
$username = 'root';  // Usuario por defecto de XAMPP
$password = '';      // Contraseña vacía por defecto en XAMPP

try {
    // Crear conexión PDO con opciones de seguridad
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Mensaje de confirmación (solo para desarrollo)
    // echo "Conexión exitosa a la base de datos ORGANIZACION";
    
} catch (PDOException $e) {
    // En caso de error en la conexión, mostrar información detallada
    echo "<div style='background: #ffebee; border: 1px solid #f44336; padding: 15px; margin: 10px; border-radius: 5px;'>";
    echo "<h3 style='color: #d32f2f; margin: 0 0 10px 0;'>❌ Error de Conexión MySQL</h3>";
    echo "<p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<hr style='border: none; border-top: 1px solid #f44336;'>";
    echo "<p><strong>🔧 Posibles soluciones:</strong></p>";
    echo "<ul>";
    echo "<li>Verifica que XAMPP esté instalado e iniciado</li>";
    echo "<li>Asegúrate de que MySQL esté corriendo en XAMPP Control Panel</li>";
    echo "<li>Verifica que la base de datos 'ORGANIZACION' exista</li>";
    echo "<li>Ejecuta el script crear_base_datos_mysql.sql en phpMyAdmin</li>";
    echo "</ul>";
    echo "<p><strong>Para diagnosticar:</strong> <a href='test_conexion.php' style='color: #1976d2;'>Ejecutar test de conexión</a></p>";
    echo "</div>";
    die();
}

/**
 * Función para ejecutar consultas preparadas de forma segura
 * 
 * @param PDO $pdo - Objeto de conexión a la base de datos
 * @param string $sql - Consulta SQL a ejecutar
 * @param array $params - Parámetros para la consulta preparada
 * @return PDOStatement - Resultado de la consulta
 */
function ejecutarConsulta($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        throw new Exception("Error en la consulta: " . $e->getMessage());
    }
}

/**
 * Función para cerrar la conexión (opcional con PDO)
 */
function cerrarConexion(&$pdo) {
    $pdo = null;
}
?>