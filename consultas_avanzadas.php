<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas Avanzadas - Organización Sin Fines de Lucro</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Organización Sin Fines de Lucro</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="formulario_proyectos.php">Proyectos</a></li>
                    <li><a href="formulario_donantes.php">Donantes</a></li>
                    <li><a href="formulario_donaciones.php">Donaciones</a></li>
                    <li><a href="consultas_avanzadas.php" class="active">Reportes</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="consultas-seccion">
                <h2>Reportes y Consultas Avanzadas</h2>
                
                <!-- Reporte 1: Proyectos con más de 2 donaciones -->
                <div class="reporte-container">
                    <h3>Proyectos Populares (Con más de 2 donaciones)</h3>
                    <p class="descripcion-reporte">
                        Este reporte muestra los proyectos que han recibido más de dos donaciones, 
                        junto con el número total de donaciones y el monto total recaudado.
                    </p>
                    
                    <?php
                    require_once 'conexion.php';
                    
                    try {
                        // Consulta avanzada: proyectos con más de 2 donaciones y monto total
                        $sql = "SELECT 
                                    p.id_proyecto,
                                    p.nombre as proyecto_nombre,
                                    p.descripcion,
                                    p.presupuesto,
                                    COUNT(d.id_donacion) as total_donaciones,
                                    SUM(d.monto) as monto_recaudado,
                                    ROUND((SUM(d.monto) / p.presupuesto) * 100, 2) as porcentaje_alcanzado
                                FROM PROYECTO p
                                INNER JOIN DONACION d ON p.id_proyecto = d.id_proyecto
                                GROUP BY p.id_proyecto, p.nombre, p.descripcion, p.presupuesto
                                HAVING COUNT(d.id_donacion) > 2
                                ORDER BY total_donaciones DESC, monto_recaudado DESC";
                        
                        $stmt = ejecutarConsulta($pdo, $sql);
                        $proyectos_populares = $stmt->fetchAll();
                        
                        if (count($proyectos_populares) > 0) {
                            echo '<div class="tabla-container">';
                            echo '<table class="tabla-datos tabla-reporte">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>Proyecto</th>';
                            echo '<th>Descripción</th>';
                            echo '<th>Meta</th>';
                            echo '<th>N° Donaciones</th>';
                            echo '<th>Monto Recaudado</th>';
                            echo '<th>% Alcanzado</th>';
                            echo '<th>Estado</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            
                            foreach ($proyectos_populares as $proyecto) {
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($proyecto['proyecto_nombre']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars(substr($proyecto['descripcion'], 0, 80)) . '...</td>';
                                echo '<td>$' . number_format($proyecto['presupuesto'], 0, ',', '.') . '</td>';
                                echo '<td><span class="badge badge-donaciones">' . $proyecto['total_donaciones'] . '</span></td>';
                                echo '<td>$' . number_format($proyecto['monto_recaudado'], 0, ',', '.') . '</td>';
                                echo '<td>' . $proyecto['porcentaje_alcanzado'] . '%</td>';
                                
                                // Determinar estado basado en porcentaje
                                $porcentaje = floatval($proyecto['porcentaje_alcanzado']);
                                if ($porcentaje >= 100) {
                                    echo '<td><span class="badge badge-completado">Completado</span></td>';
                                } elseif ($porcentaje >= 75) {
                                    echo '<td><span class="badge badge-avanzado">Avanzado</span></td>';
                                } elseif ($porcentaje >= 50) {
                                    echo '<td><span class="badge badge-progreso">En Progreso</span></td>';
                                } else {
                                    echo '<td><span class="badge badge-inicial">Inicial</span></td>';
                                }
                                
                                echo '</tr>';
                            }
                            
                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<div class="sin-datos">';
                            echo '<p>No hay proyectos con más de 2 donaciones registradas aún.</p>';
                            echo '<p><em>Registra más donaciones para ver estadísticas aquí.</em></p>';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="error">';
                        echo '<p>Error al cargar el reporte: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- Reporte 2: Estadísticas generales -->
                <div class="reporte-container">
                    <h3>Estadísticas Generales</h3>
                    <div class="estadisticas-grid">
                        <?php
                        try {
                            // Total de proyectos
                            $sql_proyectos = "SELECT COUNT(*) as total FROM PROYECTO";
                            $stmt = ejecutarConsulta($pdo, $sql_proyectos);
                            $total_proyectos = $stmt->fetch()['total'];
                            
                            // Total de donantes
                            $sql_donantes = "SELECT COUNT(*) as total FROM DONANTE";
                            $stmt = ejecutarConsulta($pdo, $sql_donantes);
                            $total_donantes = $stmt->fetch()['total'];
                            
                            // Total de donaciones y monto
                            $sql_donaciones = "SELECT COUNT(*) as total_donaciones, SUM(monto) as monto_total FROM DONACION";
                            $stmt = ejecutarConsulta($pdo, $sql_donaciones);
                            $datos_donaciones = $stmt->fetch();
                            
                            // Proyecto más popular
                            $sql_popular = "SELECT p.nombre, COUNT(d.id_donacion) as donaciones 
                                           FROM PROYECTO p 
                                           INNER JOIN DONACION d ON p.id_proyecto = d.id_proyecto 
                                           GROUP BY p.id_proyecto, p.nombre 
                                           ORDER BY donaciones DESC 
                                           LIMIT 1";
                            $stmt = ejecutarConsulta($pdo, $sql_popular);
                            $proyecto_popular = $stmt->fetch();
                            
                            echo '<div class="estadistica-item">';
                            echo '<h4>Total Proyectos</h4>';
                            echo '<div class="numero-grande">' . $total_proyectos . '</div>';
                            echo '</div>';
                            
                            echo '<div class="estadistica-item">';
                            echo '<h4>Total Donantes</h4>';
                            echo '<div class="numero-grande">' . $total_donantes . '</div>';
                            echo '</div>';
                            
                            echo '<div class="estadistica-item">';
                            echo '<h4>Total Donaciones</h4>';
                            echo '<div class="numero-grande">' . ($datos_donaciones['total_donaciones'] ?? 0) . '</div>';
                            echo '</div>';
                            
                            echo '<div class="estadistica-item">';
                            echo '<h4>Monto Total Recaudado</h4>';
                            echo '<div class="numero-grande">$' . number_format($datos_donaciones['monto_total'] ?? 0, 0, ',', '.') . '</div>';
                            echo '</div>';
                            
                            if ($proyecto_popular) {
                                echo '<div class="estadistica-item proyecto-destacado">';
                                echo '<h4>Proyecto Más Popular</h4>';
                                echo '<div class="proyecto-nombre">' . htmlspecialchars($proyecto_popular['nombre']) . '</div>';
                                echo '<div class="donaciones-count">' . $proyecto_popular['donaciones'] . ' donaciones</div>';
                                echo '</div>';
                            }
                            
                        } catch (Exception $e) {
                            echo '<div class="error">';
                            echo '<p>Error al cargar las estadísticas: ' . htmlspecialchars($e->getMessage()) . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Reporte 3: Donantes más generosos -->
                <div class="reporte-container">
                    <h3>Top Donantes</h3>
                    <p class="descripcion-reporte">
                        Ranking de los donantes que han realizado las mayores contribuciones.
                    </p>
                    
                    <?php
                    try {
                        // Consulta para obtener los top donantes
                        $sql = "SELECT 
                                    don.nombre,
                                    don.email,
                                    COUNT(d.id_donacion) as total_donaciones,
                                    SUM(d.monto) as monto_total,
                                    AVG(d.monto) as promedio_donacion
                                FROM DONANTE don
                                INNER JOIN DONACION d ON don.id_donante = d.id_donante
                                GROUP BY don.id_donante, don.nombre, don.email
                                ORDER BY monto_total DESC
                                LIMIT 10";
                        
                        $stmt = ejecutarConsulta($pdo, $sql);
                        $top_donantes = $stmt->fetchAll();
                        
                        if (count($top_donantes) > 0) {
                            echo '<div class="tabla-container">';
                            echo '<table class="tabla-datos tabla-reporte">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>Posición</th>';
                            echo '<th>Donante</th>';
                            echo '<th>Email</th>';
                            echo '<th>N° Donaciones</th>';
                            echo '<th>Monto Total</th>';
                            echo '<th>Promedio por Donación</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            
                            $posicion = 1;
                            foreach ($top_donantes as $donante) {
                                echo '<tr>';
                                echo '<td><span class="posicion">' . $posicion . '</span></td>';
                                echo '<td><strong>' . htmlspecialchars($donante['nombre']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($donante['email']) . '</td>';
                                echo '<td>' . $donante['total_donaciones'] . '</td>';
                                echo '<td>$' . number_format($donante['monto_total'], 0, ',', '.') . '</td>';
                                echo '<td>$' . number_format($donante['promedio_donacion'], 0, ',', '.') . '</td>';
                                echo '</tr>';
                                $posicion++;
                            }
                            
                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<div class="sin-datos">';
                            echo '<p>No hay datos de donaciones suficientes para generar el ranking.</p>';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="error">';
                        echo '<p>Error al cargar el ranking: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>