<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Donantes - Organización Sin Fines de Lucro</title>
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
                    <li><a href="formulario_donantes.php" class="active">Donantes</a></li>
                    <li><a href="formulario_donaciones.php">Donaciones</a></li>
                    <li><a href="consultas_avanzadas.php">Reportes</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="formulario-seccion">
                <h2>Registro de Nuevos Donantes</h2>
                
                <?php
                // Mostrar mensajes de éxito o error
                if (isset($_GET['mensaje'])) {
                    if ($_GET['mensaje'] == 'exito') {
                        echo '<div class="mensaje exito">Donante registrado correctamente</div>';
                    } elseif ($_GET['mensaje'] == 'error') {
                        echo '<div class="mensaje error">Error al registrar el donante</div>';
                    } elseif ($_GET['mensaje'] == 'duplicado') {
                        echo '<div class="mensaje error">El email ya está registrado</div>';
                    }
                }
                ?>

                <form id="formDonante" action="procesar_donante.php" method="POST" class="formulario">
                    <div class="campo">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" required maxlength="100">
                        <span class="error-mensaje" id="error-nombre"></span>
                    </div>

                    <div class="campo">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" id="email" name="email" required maxlength="100">
                        <span class="error-mensaje" id="error-email"></span>
                    </div>

                    <div class="campo">
                        <label for="direccion">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" maxlength="200">
                        <span class="error-mensaje" id="error-direccion"></span>
                    </div>

                    <div class="campo">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" maxlength="20" placeholder="+56912345678">
                        <span class="error-mensaje" id="error-telefono"></span>
                    </div>

                    <button type="submit" class="btn-submit">Registrar Donante</button>
                </form>
            </section>

            <section class="lista-seccion">
                <h2>Donantes Registrados</h2>
                <div class="tabla-container">
                    <?php
                    require_once 'conexion.php';
                    
                    try {
                        // Consulta para obtener todos los donantes
                        $sql = "SELECT * FROM DONANTE ORDER BY nombre ASC";
                        $stmt = ejecutarConsulta($pdo, $sql);
                        $donantes = $stmt->fetchAll();
                        
                        if (count($donantes) > 0) {
                            echo '<table class="tabla-datos">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>ID</th>';
                            echo '<th>Nombre</th>';
                            echo '<th>Email</th>';
                            echo '<th>Dirección</th>';
                            echo '<th>Teléfono</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';
                            
                            foreach ($donantes as $donante) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($donante['id_donante']) . '</td>';
                                echo '<td>' . htmlspecialchars($donante['nombre']) . '</td>';
                                echo '<td>' . htmlspecialchars($donante['email']) . '</td>';
                                echo '<td>' . htmlspecialchars($donante['direccion']) . '</td>';
                                echo '<td>' . htmlspecialchars($donante['telefono']) . '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p class="sin-datos">No hay donantes registrados aún.</p>';
                        }
                    } catch (Exception $e) {
                        echo '<p class="error">Error al cargar los donantes: ' . $e->getMessage() . '</p>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <script src="validaciones.js"></script>
    <script>
        // Validaciones específicas para el formulario de donantes
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formDonante');
            const emailInput = document.getElementById('email');
            const telefonoInput = document.getElementById('telefono');
            
            // Validación de email en tiempo real
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !validarEmail(email)) {
                    mostrarError('error-email', 'Ingrese un email válido');
                } else {
                    limpiarError('error-email');
                }
            });
            
            // Validación de teléfono en tiempo real
            telefonoInput.addEventListener('blur', function() {
                const telefono = this.value.trim();
                if (telefono && !validarTelefono(telefono)) {
                    mostrarError('error-telefono', 'Ingrese un teléfono válido (ej: +56912345678)');
                } else {
                    limpiarError('error-telefono');
                }
            });
        });
        
        // Función para validar formato de email
        function validarEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }
        
        // Función para validar formato de teléfono chileno
        function validarTelefono(telefono) {
            const regex = /^\+56[0-9]{9}$/;
            return regex.test(telefono);
        }
    </script>
</body>
</html>