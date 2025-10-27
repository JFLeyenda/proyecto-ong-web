<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos - Organización Sin Fines de Lucro</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Organización Sin Fines de Lucro</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="formulario_proyectos.php" class="active">Proyectos</a></li>
                    <li><a href="formulario_donantes.php">Donantes</a></li>
                    <li><a href="formulario_donaciones.php">Donaciones</a></li>
                    <li><a href="consultas_avanzadas.php">Reportes</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <!-- Mensaje de bienvenida con nuevo estilo -->
            <div class="mensaje-bienvenida">
                <h3>🌟 Gestión de Proyectos</h3>
                <p>Aquí puedes registrar nuevos proyectos y administrar los existentes. Todos los datos se almacenan de forma segura en MySQL.</p>
            </div>

            <section class="formulario-seccion">
                <h2>Registro de Nuevos Proyectos</h2>
                
                <?php
                // Mostrar mensajes de éxito o error
                if (isset($_GET['mensaje'])) {
                    if ($_GET['mensaje'] == 'exito') {
                        echo '<div class="mensaje exito">Proyecto registrado correctamente</div>';
                    } elseif ($_GET['mensaje'] == 'error') {
                        echo '<div class="mensaje error">Error al registrar el proyecto</div>';
                    }
                }
                ?>

                <form id="formProyecto" action="procesar_proyecto.php" method="POST" class="formulario">
                    <div class="campo">
                        <label for="nombre">Nombre del Proyecto:</label>
                        <input type="text" id="nombre" name="nombre" required maxlength="100">
                        <span class="error-mensaje" id="error-nombre"></span>
                    </div>

                    <div class="campo">
                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" rows="4" required maxlength="500"></textarea>
                        <span class="error-mensaje" id="error-descripcion"></span>
                    </div>

                    <div class="campo">
                        <label for="presupuesto">Presupuesto (CLP):</label>
                        <input type="number" id="presupuesto" name="presupuesto" required min="1000" step="0.01">
                        <span class="error-mensaje" id="error-presupuesto"></span>
                    </div>

                    <div class="campo">
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                        <span class="error-mensaje" id="error-fecha-inicio"></span>
                    </div>

                    <div class="campo">
                        <label for="fecha_fin">Fecha de Finalización:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" required>
                        <span class="error-mensaje" id="error-fecha-fin"></span>
                    </div>

                    <button type="submit" class="btn-submit">Registrar Proyecto</button>
                </form>
            </section>

            <section class="lista-seccion">
                <h2>Proyectos Registrados</h2>
                <div class="tabla-container">
                    <?php
                    require_once 'conexion.php';
                    
                    try {
                        // Consulta para obtener todos los proyectos
                        $sql = "SELECT * FROM PROYECTO ORDER BY fecha_inicio DESC";
                        $stmt = ejecutarConsulta($pdo, $sql);
                        $proyectos = $stmt->fetchAll();
                        
                        if (count($proyectos) > 0) {
                            echo '<table class="tabla-datos">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>ID</th>';
                            echo '<th>Nombre</th>';
                            echo '<th>Descripción</th>';
                            echo '<th>Presupuesto</th>';
                            echo '<th>Fecha Inicio</th>';
                            echo '<th>Fecha Fin</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            
                            foreach ($proyectos as $proyecto) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($proyecto['id_proyecto']) . '</td>';
                                echo '<td>' . htmlspecialchars($proyecto['nombre']) . '</td>';
                                echo '<td>' . htmlspecialchars(substr($proyecto['descripcion'], 0, 50)) . '...</td>';
                                echo '<td>$' . number_format($proyecto['presupuesto'], 0, ',', '.') . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($proyecto['fecha_inicio'])) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($proyecto['fecha_fin'])) . '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p class="sin-datos">No hay proyectos registrados aún.</p>';
                        }
                    } catch (Exception $e) {
                        echo '<p class="error">Error al cargar los proyectos: ' . $e->getMessage() . '</p>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <script src="validaciones.js"></script>
    <script>
        // Validaciones específicas para el formulario de proyectos
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formProyecto');
            
            // Validar fechas
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');
            
            fechaInicio.addEventListener('change', function() {
                const hoy = new Date().toISOString().split('T')[0];
                if (this.value < hoy) {
                    mostrarError('error-fecha-inicio', 'La fecha de inicio no puede ser anterior a hoy');
                    return false;
                } else {
                    limpiarError('error-fecha-inicio');
                }
                
                // Actualizar fecha mínima del campo fecha fin
                fechaFin.min = this.value;
            });
            
            fechaFin.addEventListener('change', function() {
                if (fechaInicio.value && this.value <= fechaInicio.value) {
                    mostrarError('error-fecha-fin', 'La fecha de fin debe ser posterior a la fecha de inicio');
                    return false;
                } else {
                    limpiarError('error-fecha-fin');
                }
            });
        });
    </script>
</body>
</html>