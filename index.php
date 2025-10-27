<?php
// Incluir archivo de conexión
require_once 'conexion.php';

// Procesar donación si se envía el formulario
$resultado_donacion = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'donar') {
    try {
        // Validar datos básicos
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $monto = floatval($_POST['monto'] ?? 0);
        $id_proyecto = intval($_POST['proyecto'] ?? 0);
        $mensaje = trim($_POST['mensaje'] ?? '');
        
        if (empty($nombre) || empty($email) || $monto <= 0 || $id_proyecto <= 0) {
            throw new Exception('Todos los campos son obligatorios');
        }
        
        // Verificar si el donante existe, si no, crearlo
        $sql_donante = "SELECT id_donante FROM DONANTE WHERE email = :email";
        $stmt = ejecutarConsulta($pdo, $sql_donante, [':email' => $email]);
        $donante = $stmt->fetch();
        
        if (!$donante) {
            // Crear nuevo donante
            $sql_crear = "INSERT INTO DONANTE (nombre, email) VALUES (:nombre, :email)";
            $stmt = ejecutarConsulta($pdo, $sql_crear, [':nombre' => $nombre, ':email' => $email]);
            $id_donante = $pdo->lastInsertId();
        } else {
            $id_donante = $donante['id_donante'];
        }
        
        // Crear donación
        $sql_donacion = "INSERT INTO DONACION (monto, fecha, id_proyecto, id_donante) VALUES (:monto, CURDATE(), :proyecto, :donante)";
        $parametros = [
            ':monto' => $monto,
            ':proyecto' => $id_proyecto,
            ':donante' => $id_donante
        ];
        
        $stmt = ejecutarConsulta($pdo, $sql_donacion, $parametros);
        
        if ($stmt->rowCount() > 0) {
            $resultado_donacion = [
                'success' => true,
                'mensaje' => '¡Gracias por tu generosa donación! Tu apoyo hace la diferencia.',
                'monto' => $monto
            ];
        } else {
            throw new Exception('Error al procesar la donación');
        }
        
    } catch (Exception $e) {
        $resultado_donacion = [
            'success' => false,
            'mensaje' => 'Error: ' . $e->getMessage()
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ayudando Juntos - Organización Sin Fines de Lucro</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos adicionales específicos para el diseño de la Semana 5 */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: none;
            margin: 0;
            padding: 0;
        }
        
        /* Header estilo Semana 5 */
        .encabezado-principal {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 0;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .encabezado-principal h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .encabezado-principal p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        /* Navegación estilo Semana 5 */
        .navegacion-principal {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .boton-navegacion {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .boton-navegacion:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            color: white;
        }
        
        .boton-navegacion.active {
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        /* Ocultar contenido por defecto */
        .seccion-contenido {
            display: none;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .seccion-contenido.active {
            display: block;
        }
        
        /* Panel de notificaciones */
        .panel-notificaciones {
            margin: 20px 0;
        }
        
        .panel-notificaciones h3 {
            color: white;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        
        .notificacion {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 20px;
            border-radius: 10px;
            border-left: 5px solid #007bff;
            backdrop-filter: blur(10px);
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }
        
        .notificacion:hover {
            transform: translateX(5px);
        }
        
        .notificacion.exito {
            border-left-color: #28a745;
        }
        
        .notificacion.informacion {
            border-left-color: #17a2b8;
        }
    </style>
</head>
<body>
    <!-- Header de la página -->
    <header class="encabezado-principal">
        <h1>🌟 Ayudando Juntos</h1>
        <p>Transformando vidas, construyendo esperanza</p>
        <nav class="navegacion-principal">
            <button onclick="showSection('proyectos')" class="boton-navegacion active">Proyectos</button>
            <button onclick="showSection('eventos')" class="boton-navegacion">Eventos</button>
            <button onclick="showSection('donaciones')" class="boton-navegacion">Donaciones</button>
            <button onclick="showSection('admin')" class="boton-navegacion">Administrar</button>
        </nav>
    </header>

    <!-- Sección de Proyectos -->
    <section id="seccion-proyectos" class="seccion-contenido active">
        <!-- Panel de noticias -->
        <div class="panel-notificaciones">
            <h3>📢 Últimas Noticias</h3>
            <div id="contenedor-notificaciones">
                <div class="notificacion exito">
                    <p>¡Nuevo! Sistema de gestión de proyectos con base de datos MySQL implementado</p>
                </div>
                <div class="notificacion informacion">
                    <p>Sistema de donaciones integrado: Conecta donantes con proyectos de manera segura</p>
                </div>
                <div class="notificacion exito">
                    <p>Reportes avanzados disponibles: Consulta estadísticas detalladas de impacto</p>
                </div>
            </div>
        </div>

        <h2 style="color: white; margin-bottom: 20px;">📋 Nuestros Proyectos</h2>
        <div class="cuadricula-proyectos">
            <?php
            try {
                // Obtener todos los proyectos con información de donaciones
                $sql = "SELECT 
                            p.*,
                            COUNT(d.id_donacion) as total_donaciones,
                            COALESCE(SUM(d.monto), 0) as monto_recaudado,
                            ROUND((COALESCE(SUM(d.monto), 0) / p.presupuesto) * 100, 1) as porcentaje_progreso
                        FROM PROYECTO p
                        LEFT JOIN DONACION d ON p.id_proyecto = d.id_proyecto
                        GROUP BY p.id_proyecto
                        ORDER BY p.fecha_inicio DESC";
                
                $stmt = ejecutarConsulta($pdo, $sql);
                $proyectos = $stmt->fetchAll();
                
                if (count($proyectos) > 0) {
                    foreach ($proyectos as $proyecto) {
                        $categoria_class = 'educacion';
                        if (stripos($proyecto['nombre'], 'salud') !== false) $categoria_class = 'salud';
                        if (stripos($proyecto['nombre'], 'ambiente') !== false) $categoria_class = 'ambiente';
                        if (stripos($proyecto['nombre'], 'social') !== false || stripos($proyecto['nombre'], 'desarrollo') !== false) $categoria_class = 'social';
                        
                        echo '<div class="tarjeta-proyecto ' . $categoria_class . '">';
                        echo '<div class="cabecera-proyecto">';
                        echo '<h3>' . htmlspecialchars($proyecto['nombre']) . '</h3>';
                        echo '<span class="estado-proyecto activo">Activo</span>';
                        echo '</div>';
                        
                        echo '<div class="contenido-proyecto">';
                        echo '<p class="descripcion">' . htmlspecialchars(substr($proyecto['descripcion'], 0, 100)) . '...</p>';
                        
                        echo '<div class="progreso-proyecto">';
                        echo '<div class="barra-progreso">';
                        echo '<div class="relleno-progreso" style="width: ' . $proyecto['porcentaje_progreso'] . '%"></div>';
                        echo '</div>';
                        echo '<p class="texto-progreso">$' . number_format($proyecto['monto_recaudado'], 0, ',', '.') . ' de $' . number_format($proyecto['presupuesto'], 0, ',', '.') . ' (' . $proyecto['porcentaje_progreso'] . '%)</p>';
                        echo '</div>';
                        
                        echo '<div class="detalles-proyecto">';
                        echo '<p><strong>📅 Finaliza:</strong> ' . date('d/m/Y', strtotime($proyecto['fecha_fin'])) . '</p>';
                        echo '<p><strong>💝 Donaciones:</strong> ' . $proyecto['total_donaciones'] . '</p>';
                        echo '</div>';
                        
                        echo '<div class="acciones-proyecto">';
                        echo '<button class="boton-donar" onclick="donarAProyecto(' . $proyecto['id_proyecto'] . ', \'' . htmlspecialchars($proyecto['nombre']) . '\')">Donar</button>';
                        echo '<button class="boton-ver-mas">Ver más</button>';
                        echo '</div>';
                        
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="sin-proyectos" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; text-align: center;">';
                    echo '<p>No hay proyectos activos en este momento.</p>';
                    echo '<p><a href="formulario_proyectos.php" style="color: #007bff;">¿Quieres registrar un proyecto?</a></p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error-proyectos" style="background: rgba(248, 215, 218, 0.9); padding: 2rem; border-radius: 10px; text-align: center;">';
                echo '<p>Error al cargar los proyectos: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </section>

    <!-- Sección de Eventos (estática para mantener funcionalidad) -->
    <section id="seccion-eventos" class="seccion-contenido">
        <h2 style="color: white; margin-bottom: 20px;">📅 Próximos Eventos</h2>
        <div class="cuadricula-eventos">
            <div class="tarjeta-evento">
                <div class="cabecera-evento">
                    <h3>Jornada de Capacitación MySQL</h3>
                    <span class="tipo-evento">Taller</span>
                </div>
                <div class="detalles-evento">
                    <p><strong>📍 Lugar:</strong> Campus IACC</p>
                    <p><strong>📅 Fecha:</strong> 20/10/2025</p>
                    <p><strong>🕐 Hora:</strong> 14:00</p>
                </div>
                <div class="acciones-evento">
                    <button class="boton-participar">Participar</button>
                    <button class="boton-compartir">Compartir</button>
                </div>
            </div>
            
            <div class="tarjeta-evento">
                <div class="cabecera-evento">
                    <h3>Charla sobre Programación Web</h3>
                    <span class="tipo-evento">Charla</span>
                </div>
                <div class="detalles-evento">
                    <p><strong>📍 Lugar:</strong> Aula Virtual</p>
                    <p><strong>📅 Fecha:</strong> 25/10/2025</p>
                    <p><strong>🕐 Hora:</strong> 16:00</p>
                </div>
                <div class="acciones-evento">
                    <button class="boton-participar">Participar</button>
                    <button class="boton-compartir">Compartir</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Donaciones -->
    <section id="seccion-donaciones" class="seccion-contenido">
        <h2 style="color: white; margin-bottom: 20px;">💝 Realizar Donación</h2>
        
        <!-- Progreso dinámico de campañas -->
        <div class="progreso-donaciones" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
            <h3>Progreso de Campañas Activas</h3>
            <div id="progreso-campanas">
                <?php
                try {
                    // Obtener progreso de todos los proyectos
                    $sql = "SELECT 
                                p.id_proyecto,
                                p.nombre,
                                p.presupuesto,
                                COALESCE(SUM(d.monto), 0) as monto_recaudado,
                                ROUND((COALESCE(SUM(d.monto), 0) / p.presupuesto) * 100, 1) as porcentaje
                            FROM PROYECTO p
                            LEFT JOIN DONACION d ON p.id_proyecto = d.id_proyecto
                            GROUP BY p.id_proyecto
                            ORDER BY porcentaje DESC
                            LIMIT 3";
                    
                    $stmt = ejecutarConsulta($pdo, $sql);
                    $proyectos_progreso = $stmt->fetchAll();
                    
                    foreach ($proyectos_progreso as $proyecto) {
                        echo '<div class="progreso-campana" style="margin-bottom: 1rem;">';
                        echo '<h4 style="margin-bottom: 0.5rem;">' . htmlspecialchars($proyecto['nombre']) . '</h4>';
                        echo '<div class="barra-progreso" style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden;">';
                        echo '<div class="relleno-progreso" style="background: linear-gradient(135deg, #28a745, #20c997); height: 100%; width: ' . $proyecto['porcentaje'] . '%; transition: width 0.3s ease;"></div>';
                        echo '</div>';
                        echo '<p style="margin-top: 0.5rem; color: #666;">$' . number_format($proyecto['monto_recaudado'], 0, ',', '.') . ' de $' . number_format($proyecto['presupuesto'], 0, ',', '.') . ' (' . $proyecto['porcentaje'] . '%)</p>';
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    echo '<p>Error al cargar el progreso de campañas.</p>';
                }
                ?>
            </div>
        </div>
        
        <!-- Formulario de donación -->
        <div class="formulario-donacion-integrado" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px;">
            <h3>💝 Realizar Donación</h3>
            
            <?php if (isset($resultado_donacion)): ?>
                <div class="mensaje-resultado <?php echo $resultado_donacion['success'] ? 'exito' : 'error'; ?>" 
                     style="padding: 15px; margin-bottom: 20px; border-radius: 5px; 
                            background: <?php echo $resultado_donacion['success'] ? '#d4edda' : '#f8d7da'; ?>; 
                            color: <?php echo $resultado_donacion['success'] ? '#155724' : '#721c24'; ?>; 
                            border: 1px solid <?php echo $resultado_donacion['success'] ? '#c3e6cb' : '#f5c6cb'; ?>;">
                    <?php echo htmlspecialchars($resultado_donacion['mensaje']); ?>
                    <?php if ($resultado_donacion['success']): ?>
                        <div style="margin-top: 10px;">
                            <strong>Monto donado:</strong> $<?php echo number_format($resultado_donacion['monto'], 0, ',', '.'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="info-donacion" style="margin-bottom: 2rem;">
                <h4>¿Cómo ayuda tu donación?</h4>
                <div class="impacto-donaciones" style="display: grid; gap: 10px; margin-top: 1rem;">
                    <div class="impacto-item" style="display: flex; align-items: center; gap: 10px;">
                        <span class="icono" style="font-size: 1.5rem;">📚</span>
                        <div><strong>$10.000:</strong> Materiales escolares para un niño</div>
                    </div>
                    <div class="impacto-item" style="display: flex; align-items: center; gap: 10px;">
                        <span class="icono" style="font-size: 1.5rem;">🍲</span>
                        <div><strong>$25.000:</strong> Kit de alimentos para una familia</div>
                    </div>
                    <div class="impacto-item" style="display: flex; align-items: center; gap: 10px;">
                        <span class="icono" style="font-size: 1.5rem;">🏥</span>
                        <div><strong>$50.000:</strong> Medicamentos básicos</div>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="" class="form-donacion-rapida">
                <input type="hidden" name="accion" value="donar">
                
                <div class="grupo-campos" style="display: grid; gap: 1rem; grid-template-columns: 1fr 1fr; margin-bottom: 1rem;">
                    <div class="campo-donacion">
                        <label for="nombre_donacion" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre completo *</label>
                        <input type="text" id="nombre_donacion" name="nombre" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div class="campo-donacion">
                        <label for="email_donacion" style="display: block; margin-bottom: 5px; font-weight: 600;">Correo electrónico *</label>
                        <input type="email" id="email_donacion" name="email" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </div>
                
                <div class="campo-donacion" style="margin-bottom: 1rem;">
                    <label for="proyecto_donacion" style="display: block; margin-bottom: 5px; font-weight: 600;">Proyecto a apoyar *</label>
                    <select id="proyecto_donacion" name="proyecto" required 
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Selecciona un proyecto</option>
                        <?php
                        try {
                            $sql = "SELECT id_proyecto, nombre, presupuesto FROM PROYECTO ORDER BY nombre";
                            $stmt = ejecutarConsulta($pdo, $sql);
                            $proyectos_select = $stmt->fetchAll();
                            
                            foreach ($proyectos_select as $proyecto) {
                                echo '<option value="' . $proyecto['id_proyecto'] . '">';
                                echo htmlspecialchars($proyecto['nombre']) . ' (Meta: $' . number_format($proyecto['presupuesto'], 0, ',', '.') . ')';
                                echo '</option>';
                            }
                        } catch (Exception $e) {
                            echo '<option value="">Error al cargar proyectos</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="campo-donacion" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Selecciona un monto:</label>
                    <div class="botones-monto" style="display: flex; gap: 10px; margin: 10px 0; flex-wrap: wrap;">
                        <button type="button" class="btn-monto" onclick="seleccionarMonto(10000)" 
                                style="padding: 8px 15px; border: 2px solid #007bff; background: white; color: #007bff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">$10.000</button>
                        <button type="button" class="btn-monto" onclick="seleccionarMonto(25000)" 
                                style="padding: 8px 15px; border: 2px solid #007bff; background: white; color: #007bff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">$25.000</button>
                        <button type="button" class="btn-monto" onclick="seleccionarMonto(50000)" 
                                style="padding: 8px 15px; border: 2px solid #007bff; background: white; color: #007bff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">$50.000</button>
                        <button type="button" class="btn-monto" onclick="seleccionarMonto(100000)" 
                                style="padding: 8px 15px; border: 2px solid #007bff; background: white; color: #007bff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">$100.000</button>
                        <button type="button" class="btn-monto" onclick="habilitarMontoPersonalizado()" 
                                style="padding: 8px 15px; border: 2px solid #007bff; background: white; color: #007bff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">Otro</button>
                    </div>
                </div>
                
                <div class="campo-donacion" style="margin-bottom: 1rem;">
                    <label for="monto_donacion" style="display: block; margin-bottom: 5px; font-weight: 600;">Monto a donar ($) *</label>
                    <input type="number" id="monto_donacion" name="monto" min="1000" step="1000" required readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f8f9fa;">
                    <div id="mensaje-impacto" class="mensaje-impacto" style="margin-top: 5px; font-size: 0.9rem;"></div>
                </div>
                
                <div class="campo-donacion" style="margin-bottom: 1rem;">
                    <label for="mensaje_donacion" style="display: block; margin-bottom: 5px; font-weight: 600;">Mensaje (opcional)</label>
                    <textarea id="mensaje_donacion" name="mensaje" rows="3" 
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"
                              placeholder="Comparte por qué quieres apoyar nuestra causa..."></textarea>
                </div>
                
                <button type="submit" class="btn-donar-principal" 
                        style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 30px; border: none; border-radius: 25px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; width: 100%;">
                    💝 Donar Ahora
                </button>
                
                <div class="nota-seguridad" style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9rem;">
                    🔒 Sistema seguro con validaciones PHP y MySQL
                </div>
            </form>
        </div>
    </section>

    <!-- Sección de Administración -->
    <section id="seccion-admin" class="seccion-contenido">
        <h2 style="color: white; margin-bottom: 20px;">⚙️ Administración</h2>
        <div class="cuadricula-admin" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="tarjeta-admin" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; text-align: center; transition: transform 0.3s ease;">
                <h3>📝 Gestionar Proyectos</h3>
                <p>Registrar y administrar proyectos de la organización</p>
                <a href="formulario_proyectos.php" class="boton-admin" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin-top: 1rem; font-weight: 600; transition: all 0.3s ease;">Gestionar</a>
            </div>
            
            <div class="tarjeta-admin" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; text-align: center; transition: transform 0.3s ease;">
                <h3>👥 Gestionar Donantes</h3>
                <p>Administrar información de donantes registrados</p>
                <a href="formulario_donantes.php" class="boton-admin" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin-top: 1rem; font-weight: 600; transition: all 0.3s ease;">Gestionar</a>
            </div>
            
            <div class="tarjeta-admin" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; text-align: center; transition: transform 0.3s ease;">
                <h3>💝 Procesar Donaciones</h3>
                <p>Registrar y vincular donaciones con proyectos</p>
                <a href="formulario_donaciones.php" class="boton-admin" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin-top: 1rem; font-weight: 600; transition: all 0.3s ease;">Procesar</a>
            </div>
            
            <div class="tarjeta-admin" style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; text-align: center; transition: transform 0.3s ease;">
                <h3>📊 Reportes Avanzados</h3>
                <p>Consultas complejas y estadísticas detalladas</p>
                <a href="consultas_avanzadas.php" class="boton-admin" 
                   style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin-top: 1rem; font-weight: 600; transition: all 0.3s ease;">Ver Reportes</a>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div style="background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 10px; margin-top: 2rem;">
            <h3>📈 Estado General del Sistema</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <?php
                try {
                    // Estadísticas rápidas
                    $sql = "SELECT COUNT(*) as total FROM PROYECTO";
                    $stmt = ejecutarConsulta($pdo, $sql);
                    $total_proyectos = $stmt->fetch()['total'];
                    
                    $sql = "SELECT COUNT(*) as total FROM DONANTE";
                    $stmt = ejecutarConsulta($pdo, $sql);
                    $total_donantes = $stmt->fetch()['total'];
                    
                    $sql = "SELECT COUNT(*) as total, COALESCE(SUM(monto), 0) as monto FROM DONACION";
                    $stmt = ejecutarConsulta($pdo, $sql);
                    $datos_donaciones = $stmt->fetch();
                    
                    echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
                    echo '<div style="font-size: 2rem; font-weight: bold; color: #28a745;">' . $total_proyectos . '</div>';
                    echo '<div style="color: #666;">Proyectos</div>';
                    echo '</div>';
                    
                    echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
                    echo '<div style="font-size: 2rem; font-weight: bold; color: #17a2b8;">' . $total_donantes . '</div>';
                    echo '<div style="color: #666;">Donantes</div>';
                    echo '</div>';
                    
                    echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
                    echo '<div style="font-size: 2rem; font-weight: bold; color: #fd7e14;">' . $datos_donaciones['total'] . '</div>';
                    echo '<div style="color: #666;">Donaciones</div>';
                    echo '</div>';
                    
                    echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
                    echo '<div style="font-size: 1.5rem; font-weight: bold; color: #6f42c1;">$' . number_format($datos_donaciones['monto'], 0, ',', '.') . '</div>';
                    echo '<div style="color: #666;">Total Recaudado</div>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div style="text-align: center; padding: 1rem; background: #f8d7da; border-radius: 8px; color: #721c24;">';
                    echo 'Error al cargar estadísticas';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <script>
        // Funciones de navegación
        function showSection(sectionName) {
            // Ocultar todas las secciones
            document.querySelectorAll('.seccion-contenido').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remover clase active de todos los botones
            document.querySelectorAll('.boton-navegacion').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById('seccion-' + sectionName).classList.add('active');
            
            // Activar el botón correspondiente
            event.target.classList.add('active');
        }

        // Funciones para donaciones
        function seleccionarMonto(monto) {
            document.querySelectorAll('.btn-monto').forEach(btn => {
                btn.style.background = 'white';
                btn.style.color = '#007bff';
            });
            
            event.target.style.background = '#007bff';
            event.target.style.color = 'white';
            
            document.getElementById('monto_donacion').value = monto;
            document.getElementById('monto_donacion').readOnly = true;
            document.getElementById('monto_donacion').style.background = '#fff';
            mostrarImpacto(monto);
        }
        
        function habilitarMontoPersonalizado() {
            document.querySelectorAll('.btn-monto').forEach(btn => {
                btn.style.background = 'white';
                btn.style.color = '#007bff';
            });
            
            event.target.style.background = '#007bff';
            event.target.style.color = 'white';
            
            const input = document.getElementById('monto_donacion');
            input.readOnly = false;
            input.style.background = '#fff';
            input.value = '';
            input.focus();
            document.getElementById('mensaje-impacto').innerHTML = '';
        }
        
        function mostrarImpacto(monto) {
            let mensaje = '';
            if (monto >= 100000) {
                mensaje = '<span style="color: #28a745;">💝 ¡Increíble! Apoyo integral completo.</span>';
            } else if (monto >= 50000) {
                mensaje = '<span style="color: #17a2b8;">🏥 ¡Genial! Medicamentos básicos cubiertos.</span>';
            } else if (monto >= 25000) {
                mensaje = '<span style="color: #fd7e14;">🍲 ¡Fantástico! Alimentarás a una familia.</span>';
            } else if (monto >= 10000) {
                mensaje = '<span style="color: #6f42c1;">📚 ¡Perfecto! Materiales escolares completos.</span>';
            }
            document.getElementById('mensaje-impacto').innerHTML = mensaje;
        }
        
        function donarAProyecto(idProyecto, nombreProyecto) {
            // Cambiar a la sección de donaciones
            showSection('donaciones');
            document.querySelector('[onclick="showSection(\'donaciones\')"]').classList.add('active');
            
            // Seleccionar el proyecto en el select
            document.getElementById('proyecto_donacion').value = idProyecto;
            
            // Mostrar mensaje
            const mensaje = document.createElement('div');
            mensaje.style.cssText = 'background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #bee5eb;';
            mensaje.textContent = 'Proyecto seleccionado: ' + nombreProyecto;
            
            const form = document.querySelector('.form-donacion-rapida');
            form.insertBefore(mensaje, form.firstChild);
            
            setTimeout(() => mensaje.remove(), 5000);
        }
        
        // Agregar efectos hover a las tarjetas admin
        document.addEventListener('DOMContentLoaded', function() {
            const tarjetasAdmin = document.querySelectorAll('.tarjeta-admin');
            tarjetasAdmin.forEach(tarjeta => {
                tarjeta.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                });
                
                tarjeta.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
            
            // Agregar efectos hover a botones de monto
            const botonesMonto = document.querySelectorAll('.btn-monto');
            botonesMonto.forEach(boton => {
                boton.addEventListener('mouseenter', function() {
                    if (this.style.background !== 'rgb(0, 123, 255)') {
                        this.style.background = '#f8f9fa';
                    }
                });
                
                boton.addEventListener('mouseleave', function() {
                    if (this.style.background !== 'rgb(0, 123, 255)') {
                        this.style.background = 'white';
                    }
                });
            });
        });
        
        // Auto-mostrar donaciones si hay resultado
        <?php if (isset($resultado_donacion)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showSection('donaciones');
                document.querySelector('[onclick="showSection(\'donaciones\')"]').classList.add('active');
            });
        <?php endif; ?>
    </script>
</body>
</html>