<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Donaciones - Organización Sin Fines de Lucro</title>
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
                    <li><a href="formulario_donaciones.php" class="active">Donaciones</a></li>
                    <li><a href="consultas_avanzadas.php">Reportes</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="formulario-seccion">
                <h2>Registro de Donaciones</h2>
                
                <?php
                // Mostrar mensajes de éxito o error
                if (isset($_GET['mensaje'])) {
                    if ($_GET['mensaje'] == 'exito') {
                        echo '<div class="mensaje exito">Donación registrada correctamente</div>';
                    } elseif ($_GET['mensaje'] == 'error') {
                        echo '<div class="mensaje error">Error al registrar la donación</div>';
                    }
                }
                ?>

                <form id="formDonacion" action="procesar_donacion.php" method="POST" class="formulario">
                    <div class="campo">
                        <label for="monto">Monto de la Donación (CLP):</label>
                        <input type="number" id="monto" name="monto" required min="1000" step="0.01">
                        <span class="error-mensaje" id="error-monto"></span>
                    </div>

                    <div class="campo">
                        <label for="fecha">Fecha de la Donación:</label>
                        <input type="date" id="fecha" name="fecha" required>
                        <span class="error-mensaje" id="error-fecha"></span>
                    </div>

                    <div class="campo">
                        <label for="id_proyecto">Proyecto a Donar:</label>
                        <select id="id_proyecto" name="id_proyecto" required>
                            <option value="">Seleccione un proyecto</option>
                            <?php
                            require_once 'conexion.php';
                            
                            try {
                                // Obtener proyectos activos (fecha de fin mayor a hoy)
                                $sql = "SELECT id_proyecto, nombre, presupuesto FROM PROYECTO 
                                       WHERE fecha_fin >= CURDATE() 
                                       ORDER BY nombre";
                                $stmt = ejecutarConsulta($pdo, $sql);
                                $proyectos = $stmt->fetchAll();
                                
                                foreach ($proyectos as $proyecto) {
                                    echo '<option value="' . $proyecto['id_proyecto'] . '">';
                                    echo htmlspecialchars($proyecto['nombre']) . ' (Meta: $' . 
                                         number_format($proyecto['presupuesto'], 0, ',', '.') . ')';
                                    echo '</option>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="">Error al cargar proyectos</option>';
                            }
                            ?>
                        </select>
                        <span class="error-mensaje" id="error-proyecto"></span>
                    </div>

                    <div class="campo">
                        <label for="id_donante">Donante:</label>
                        <select id="id_donante" name="id_donante" required>
                            <option value="">Seleccione un donante</option>
                            <?php
                            try {
                                // Obtener todos los donantes
                                $sql = "SELECT id_donante, nombre, email FROM DONANTE ORDER BY nombre";
                                $stmt = ejecutarConsulta($pdo, $sql);
                                $donantes = $stmt->fetchAll();
                                
                                foreach ($donantes as $donante) {
                                    echo '<option value="' . $donante['id_donante'] . '">';
                                    echo htmlspecialchars($donante['nombre']) . ' (' . 
                                         htmlspecialchars($donante['email']) . ')';
                                    echo '</option>';
                                }
                            } catch (Exception $e) {
                                echo '<option value="">Error al cargar donantes</option>';
                            }
                            ?>
                        </select>
                        <span class="error-mensaje" id="error-donante"></span>
                    </div>

                    <button type="submit" class="btn-submit">Registrar Donación</button>
                </form>
            </section>

            <section class="lista-seccion">
                <h2>Donaciones Registradas</h2>
                <div class="tabla-container">
                    <?php
                    try {
                        // Consulta para obtener todas las donaciones con información relacionada
                        $sql = "SELECT d.id_donacion, d.monto, d.fecha, 
                                       p.nombre as proyecto_nombre, 
                                       don.nombre as donante_nombre
                                FROM DONACION d
                                INNER JOIN PROYECTO p ON d.id_proyecto = p.id_proyecto
                                INNER JOIN DONANTE don ON d.id_donante = don.id_donante
                                ORDER BY d.fecha DESC";
                        
                        $stmt = ejecutarConsulta($pdo, $sql);
                        $donaciones = $stmt->fetchAll();
                        
                        if (count($donaciones) > 0) {
                            echo '<table class="tabla-datos">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>ID</th>';
                            echo '<th>Monto</th>';
                            echo '<th>Fecha</th>';
                            echo '<th>Proyecto</th>';
                            echo '<th>Donante</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            
                            foreach ($donaciones as $donacion) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($donacion['id_donacion']) . '</td>';
                                echo '<td>$' . number_format($donacion['monto'], 0, ',', '.') . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($donacion['fecha'])) . '</td>';
                                echo '<td>' . htmlspecialchars($donacion['proyecto_nombre']) . '</td>';
                                echo '<td>' . htmlspecialchars($donacion['donante_nombre']) . '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p class="sin-datos">No hay donaciones registradas aún.</p>';
                        }
                    } catch (Exception $e) {
                        echo '<p class="error">Error al cargar las donaciones: ' . $e->getMessage() . '</p>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <script src="validaciones.js"></script>
    <script>
        // Validaciones específicas para el formulario de donaciones
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formDonacion');
            const fechaInput = document.getElementById('fecha');
            
            // Establecer la fecha máxima como hoy
            const hoy = new Date().toISOString().split('T')[0];
            fechaInput.max = hoy;
            fechaInput.value = hoy; // Establecer fecha por defecto
            
            // Validar que la fecha no sea futura
            fechaInput.addEventListener('change', function() {
                if (this.value > hoy) {
                    mostrarError('error-fecha', 'La fecha no puede ser futura');
                    this.value = hoy;
                } else {
                    limpiarError('error-fecha');
                }
            });
        });
    </script>
</body>
</html>