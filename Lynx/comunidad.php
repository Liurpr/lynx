<?php
// iniciar sesion para verificar si el usuario esta logueado
session_start();

// incluir archivo de conexion a la base de datos
require_once 'conexion.php';

// variables para la comunidad
$nombre_usuario = "invitado"; // nombre por defecto para usuarios no logueados
$foto_perfil = "img/profile/default_profile.jpg"; // foto de perfil por defecto
$es_invitado = true; // por defecto se asume que es invitado
$mensaje = ""; // para mensajes de respuesta del sistema
$tipo_mensaje = ""; // para definir el tipo de mensaje (error, exito, etc)
$comunidad_actual = "general"; // comunidad por defecto a mostrar
$edad_usuario = 0; // para guardar la edad calculada del usuario
$puede_publicar = false; // por defecto no puede publicar
$es_desarrollador = false; // para verificar si tiene permisos de dev

// verificar si el usuario esta logueado
if (isset($_SESSION['usuario_id'])) {
    // crear instancia de conexion a la base de datos
    $db = new DatabaseConnection();
    $conexion = $db->conectar();
    
    // obtener id del usuario de la sesion actual
    $usuario_id = $_SESSION['usuario_id'];
    
    // preparar consulta para buscar datos del usuario incluyendo edad y rol
    $consulta = $conexion->prepare("SELECT nombre_usuario, foto_perfil, fecha_nacimiento, rol FROM usuarios WHERE id = ?");
    $consulta->bind_param("i", $usuario_id); // vincular el parametro como entero
    $consulta->execute(); // ejecutar la consulta
    $resultado = $consulta->get_result(); // obtener resultado
    
    // si se encuentra el usuario, cargar sus datos
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc(); // convertir resultado en array asociativo
        $nombre_usuario = $usuario['nombre_usuario']; // guardar nombre de usuario
        $foto_perfil = !empty($usuario['foto_perfil']) ? "img/profile/".$usuario['foto_perfil'] : "img/profile/default_profile.jpg"; // ruta a la foto del perfil
        $es_invitado = false; // ya no es invitado
        
        // verificar si el usuario es desarrollador segun su rol
        $es_desarrollador = ($usuario['rol'] == 'desarrollador');
        
        // calcular edad a partir de fecha de nacimiento
        if (!empty($usuario['fecha_nacimiento'])) {
            $fecha_nacimiento = new DateTime($usuario['fecha_nacimiento']); // convertir a objeto datetime
            $hoy = new DateTime(); // fecha actual
            $edad_usuario = $hoy->diff($fecha_nacimiento)->y; // calcular diferencia en años
        }
        
        // determinar a qué comunidad pertenece basado en la edad
        // los desarrolladores pueden acceder a cualquier comunidad
        if ($es_desarrollador) {
            // si es desarrollador y solicita una comunidad específica
            if (isset($_GET['comunidad']) && ($_GET['comunidad'] == 'adultos' || $_GET['comunidad'] == 'jovenes')) {
                $comunidad_actual = $_GET['comunidad']; // asignar comunidad solicitada
            } else {
                $comunidad_actual = "adultos"; // por defecto para desarrolladores
            }
        } else {
            // usuarios normales se asignan por edad
            if ($edad_usuario >= 18) {
                $comunidad_actual = "adultos"; // si es mayor de edad
            } else {
                $comunidad_actual = "jovenes"; // si es menor de edad
            }
        }
        
        $puede_publicar = true; // permitir publicar mensajes
    }
    
    // si se ha enviado un mensaje para la comunidad
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mensaje']) && !$es_invitado) {
        $nuevo_mensaje = trim($_POST['mensaje']); // quitar espacios extras
        
        // validar mensaje
        if (empty($nuevo_mensaje)) {
            $mensaje = "el mensaje no puede estar vacío"; // mensaje de error
            $tipo_mensaje = "error"; // tipo de alerta a mostrar
        } elseif (strlen($nuevo_mensaje) > 500) {
            $mensaje = "el mensaje no puede exceder los 500 caracteres"; // mensaje de error
            $tipo_mensaje = "error"; // tipo de alerta a mostrar
        } else {
            // escapar el mensaje para evitar inyección SQL
            $nuevo_mensaje = $conexion->real_escape_string($nuevo_mensaje);
            
            // insertar mensaje en la base de datos
            $consulta = $conexion->prepare("INSERT INTO comunidad (usuario_id, mensaje, tipo_comunidad, fecha_creacion) VALUES (?, ?, ?, NOW())");
            $consulta->bind_param("iss", $usuario_id, $nuevo_mensaje, $comunidad_actual); // i=entero, s=string
            
            if ($consulta->execute()) {
                $mensaje = "mensaje publicado correctamente"; // mensaje de exito
                $tipo_mensaje = "exito"; // tipo de alerta a mostrar
                // recargar la página para mostrar el nuevo mensaje
                header("Location: comunidad.php?msg=publicado"); // redirigir con mensaje en la url
                exit(); // terminar ejecucion
            } else {
                $mensaje = "error al publicar el mensaje"; // mensaje de error
                $tipo_mensaje = "error"; // tipo de alerta a mostrar
            }
        }
    }
    
    // procesar la eliminación de mensajes (solo para desarrolladores)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'borrar_mensaje' && $es_desarrollador) {
        if (isset($_POST['mensaje_id'])) {
            $mensaje_id = intval($_POST['mensaje_id']); // convertir a entero
            
            // borrar el mensaje
            $consulta_borrar = $conexion->prepare("DELETE FROM comunidad WHERE id = ?");
            $consulta_borrar->bind_param("i", $mensaje_id); // i=entero
            
            if ($consulta_borrar->execute()) {
                // tambien borrar comentarios y reacciones asociadas
                $borrar_comentarios = $conexion->prepare("DELETE FROM comentarios_comunidad WHERE mensaje_id = ?");
                $borrar_comentarios->bind_param("i", $mensaje_id); // i=entero
                $borrar_comentarios->execute(); // ejecutar borrado de comentarios
                
                $borrar_reacciones = $conexion->prepare("DELETE FROM reacciones_comunidad WHERE mensaje_id = ?");
                $borrar_reacciones->bind_param("i", $mensaje_id); // i=entero
                $borrar_reacciones->execute(); // ejecutar borrado de reacciones
                
                $mensaje = "mensaje eliminado correctamente"; // mensaje de exito
                $tipo_mensaje = "exito"; // tipo de alerta a mostrar
                header("Location: comunidad.php?msg=eliminado&comunidad=$comunidad_actual"); // redirigir con mensaje
                exit(); // terminar ejecucion
            } else {
                $mensaje = "error al eliminar el mensaje"; // mensaje de error
                $tipo_mensaje = "error"; // tipo de alerta a mostrar
            }
        }
    }
}

// verificar si se solicita cambiar la comunidad (para desarrolladores o admin)
if (isset($_GET['comunidad']) && ($es_desarrollador || (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'))) {
    $comunidad_solicitada = $_GET['comunidad']; // obtener comunidad de la url
    if ($comunidad_solicitada == 'adultos' || $comunidad_solicitada == 'jovenes') {
        $comunidad_actual = $comunidad_solicitada; // cambiar a la comunidad solicitada
    }
}

// mensaje desde url
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'publicado':
            $mensaje = "mensaje publicado correctamente"; // feedback positivo
            $tipo_mensaje = "exito"; // tipo de alerta
            break;
        case 'eliminado':
            $mensaje = "mensaje eliminado correctamente"; // feedback positivo
            $tipo_mensaje = "exito"; // tipo de alerta
            break;
    }
}

// funcion para verificar si el mensaje pertenece al usuario o si es desarrollador
function puedeEditarMensaje($mensaje_usuario_id, $usuario_actual_id, $es_desarrollador) {
    return ($mensaje_usuario_id == $usuario_actual_id || $es_desarrollador); // true si coincide o es dev
}
?>

<!DOCTYPE html>
<html lang="es" data-tema="oscuro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Comunidad de Lynx: Interactúa con otros estudiantes de Lenguaje de Señas Mexicano (LSM)">
    <title>Comunidad <?php echo ucfirst($comunidad_actual); ?> | Lynx - Aprende Lenguaje de Señas Mexicano</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/comunidad.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- incluir el header -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-comunidad">
            <div class="contenedor-comunidad">
                <div class="cabecera-comunidad">
                    <h1 class="titulo-comunidad">
                        comunidad <?php echo $comunidad_actual; ?> <!-- mostrar nombre de comunidad -->
                    </h1>
                    <div class="info-comunidad">
                        <p class="descripcion-comunidad">
                            <?php if ($comunidad_actual == "adultos"): ?>
                                Comunidad para todos, donde pueden compartir y aprender juntos.
                            <?php else: ?>
                                Comunidad para jóvenes, donde pueden compartir y aprender juntos.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <?php if (!empty($mensaje)): ?> <!-- mostrar mensajes de sistema si existen -->
                <div class="mensaje-alerta <?php echo $tipo_mensaje; ?>">
                    <?php if ($tipo_mensaje == "exito"): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($tipo_mensaje == "error"): ?>
                        <i class="fas fa-exclamation-circle"></i>
                    <?php elseif ($tipo_mensaje == "advertencia"): ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php endif; ?>
                    <p><?php echo $mensaje; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($es_desarrollador): ?> <!-- panel solo visible para desarrolladores -->
                <!-- panel de herramientas para desarrolladores -->
                <div class="panel-desarrollador">
                    <h3>herramientas de desarrollador</h3>
                    <div class="herramientas-desarrollador">
                        <div class="selector-comunidad">
                            <span>cambiar a:</span>
                            <a href="comunidad.php?comunidad=jovenes" class="boton-dev <?php echo ($comunidad_actual == 'jovenes') ? 'activo' : ''; ?>">comunidad jovenes</a>
                            <a href="comunidad.php?comunidad=adultos" class="boton-dev <?php echo ($comunidad_actual == 'adultos') ? 'activo' : ''; ?>">comunidad adultos</a>
                        </div>
                        <div class="contador-mensajes">
                            <?php
                            // contar mensajes en la comunidad actual
                            $consulta_contar = $conexion->prepare("SELECT COUNT(*) as total FROM comunidad WHERE tipo_comunidad = ?");
                            $consulta_contar->bind_param("s", $comunidad_actual); // s=string
                            $consulta_contar->execute(); // ejecutar consulta
                            $total_mensajes = $consulta_contar->get_result()->fetch_assoc()['total']; // numero de mensajes
                            ?>
                            <span class="etiqueta-contador">mensajes totales: <?php echo $total_mensajes; ?></span>
                        </div>
                        <div class="opciones-desarrollador">
                            <button id="toggleBorrar" class="boton-dev">
                                <i class="fas fa-trash-alt"></i> mostrar opciones de borrado
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($es_invitado): ?> <!-- mostrar mensaje para usuarios no logueados -->
                <div class="mensaje-invitado">
                    <h2>inicia sesión para participar</h2>
                    <p>necesitas una cuenta para interactuar en la comunidad</p>
                    <div class="botones-accion">
                        <a href="registro.php" class="boton-primario">registrarse</a>
                        <a href="iniciosesion.php" class="boton-secundario">iniciar sesion</a>
                    </div>
                </div>
                <?php elseif ($puede_publicar): ?> <!-- mostrar form para usuarios que pueden publicar -->
                <!-- Formulario para publicar mensajes -->
                <div class="caja-publicar">
                    <form method="post" class="formulario-mensaje" id="form-mensaje">
                        <div class="cabecera-formulario">
                            <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil" class="mini-foto-perfil">
                            <span class="nombre-usuario-mensaje"><?php echo $nombre_usuario; ?></span>
                        </div>
                        <div class="grupo-formulario">
                            <textarea id="mensaje" name="mensaje" class="input-formulario" rows="3" maxlength="500" placeholder="comparte algo con la comunidad..."></textarea>
                            <span class="contador-caracteres"><span id="contador-mensaje">0</span>/500</span>
                        </div>
                        <div class="botones-accion">
                            <button type="submit" class="boton-primario">publicar</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Lista de mensajes de la comunidad -->
                <div class="lista-mensajes">
                    <?php
                    // obtener mensajes de la comunidad actual
                    if (!$es_invitado) {
                        $consulta_mensajes = $conexion->prepare("
                            SELECT c.id, c.mensaje, c.fecha_creacion, c.usuario_id, u.nombre_usuario, u.foto_perfil 
                            FROM comunidad c 
                            JOIN usuarios u ON c.usuario_id = u.id 
                            WHERE c.tipo_comunidad = ? 
                            ORDER BY c.fecha_creacion DESC 
                            LIMIT 50
                        ");
                        $consulta_mensajes->bind_param("s", $comunidad_actual); // s=string
                        $consulta_mensajes->execute(); // ejecutar la consulta
                        $resultado_mensajes = $consulta_mensajes->get_result(); // obtener resultados
                        
                        if ($resultado_mensajes->num_rows > 0) {
                            while ($mensaje_item = $resultado_mensajes->fetch_assoc()) { // recorrer cada mensaje
                                $foto_mensaje = !empty($mensaje_item['foto_perfil']) ? "img/profile/".$mensaje_item['foto_perfil'] : "img/profile/default_profile.jpg"; // ruta foto
                                ?>
                                <div class="tarjeta-mensaje" data-mensaje-id="<?php echo $mensaje_item['id']; ?>">
                                    <div class="cabecera-mensaje">
                                        <img src="<?php echo $foto_mensaje; ?>" alt="Foto de perfil" class="mini-foto-perfil">
                                        <div class="info-mensaje">
                                            <span class="nombre-usuario-mensaje"><?php echo $mensaje_item['nombre_usuario']; ?></span>
                                            <span class="fecha-mensaje"><?php echo date('d/m/Y H:i', strtotime($mensaje_item['fecha_creacion'])); ?></span>
                                        </div>
                                        
                                        <?php if ($es_desarrollador): ?> <!-- opciones solo para devs -->
                                        <!-- opciones de desarrollador para este mensaje -->
                                        <div class="opciones-desarrollador-mensaje" style="display: none;">
                                            <form method="post" class="form-borrar-mensaje" onsubmit="return confirm('¿estas seguro de que quieres borrar este mensaje?');">
                                                <input type="hidden" name="accion" value="borrar_mensaje">
                                                <input type="hidden" name="mensaje_id" value="<?php echo $mensaje_item['id']; ?>">
                                                <button type="submit" class="boton-borrar-mensaje">
                                                    <i class="fas fa-trash-alt"></i> borrar
                                                </button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="contenido-mensaje">
                                        <p><?php echo nl2br(htmlspecialchars($mensaje_item['mensaje'])); ?></p> <!-- mostrar mensaje con saltos de linea -->
                                    </div>
                                    <div class="acciones-mensaje">
                                        <?php
                                        // Verificar si el usuario ha dado like a este mensaje
                                        $consulta_like = $conexion->prepare("SELECT tipo_reaccion FROM reacciones_comunidad WHERE mensaje_id = ? AND usuario_id = ?");
                                        $consulta_like->bind_param("ii", $mensaje_item['id'], $usuario_id); // i=entero
                                        $consulta_like->execute(); // ejecutar consulta
                                        $resultado_like = $consulta_like->get_result(); // obtener resultado
                                        $tiene_like = false; // por defecto no tiene like
                                        $tiene_dislike = false; // por defecto no tiene dislike
        
                                        if ($resultado_like->num_rows > 0) {
                                            $reaccion = $resultado_like->fetch_assoc(); // obtener tipo de reaccion
                                            $tiene_like = ($reaccion['tipo_reaccion'] === 'like'); // verificar si es like
                                            $tiene_dislike = ($reaccion['tipo_reaccion'] === 'dislike'); // verificar si es dislike
                                        }
        
                                        // Contar likes
                                        $consulta_count_likes = $conexion->prepare("SELECT COUNT(*) as total FROM reacciones_comunidad WHERE mensaje_id = ? AND tipo_reaccion = 'like'");
                                        $consulta_count_likes->bind_param("i", $mensaje_item['id']); // i=entero
                                        $consulta_count_likes->execute(); // ejecutar consulta
                                        $total_likes = $consulta_count_likes->get_result()->fetch_assoc()['total']; // numero de likes
        
                                        // Contar dislikes
                                        $consulta_count_dislikes = $conexion->prepare("SELECT COUNT(*) as total FROM reacciones_comunidad WHERE mensaje_id = ? AND tipo_reaccion = 'dislike'");
                                        $consulta_count_dislikes->bind_param("i", $mensaje_item['id']); // i=entero
                                        $consulta_count_dislikes->execute(); // ejecutar consulta
                                        $total_dislikes = $consulta_count_dislikes->get_result()->fetch_assoc()['total']; // numero de dislikes
        
                                        // Contar comentarios
                                        $consulta_count_comentarios = $conexion->prepare("SELECT COUNT(*) as total FROM comentarios_comunidad WHERE mensaje_id = ?");
                                        $consulta_count_comentarios->bind_param("i", $mensaje_item['id']); // i=entero
                                        $consulta_count_comentarios->execute(); // ejecutar consulta
                                        $total_comentarios = $consulta_count_comentarios->get_result()->fetch_assoc()['total']; // numero de comentarios
                                        ?>

                                        <button class="boton-accion-mensaje me-gusta <?php echo $tiene_like ? 'activo' : ''; ?>" 
                                                data-accion="like" 
                                                data-mensaje-id="<?php echo $mensaje_item['id']; ?>" 
                                                title="me gusta">
                                            <i class="<?php echo $tiene_like ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i> 
                                            <span class="contador-acciones"><?php echo $total_likes; ?></span>
                                        </button>
        
                                        <button class="boton-accion-mensaje no-me-gusta <?php echo $tiene_dislike ? 'activo' : ''; ?>" 
                                                data-accion="dislike" 
                                                data-mensaje-id="<?php echo $mensaje_item['id']; ?>" 
                                                title="no me gusta">
                                            <i class="<?php echo $tiene_dislike ? 'fa-solid' : 'fa-regular'; ?> fa-thumbs-down"></i> 
                                            <span class="contador-acciones"><?php echo $total_dislikes; ?></span>
                                        </button>
        
                                        <button class="boton-accion-mensaje comentar" 
                                                data-accion="mostrar-comentarios" 
                                                data-mensaje-id="<?php echo $mensaje_item['id']; ?>" 
                                                title="comentar">
                                            <i class="fa-regular fa-comment"></i> 
                                            <span class="contador-acciones"><?php echo $total_comentarios; ?></span>
                                        </button>
        
                                        <button class="boton-accion-mensaje compartir" 
                                                data-accion="compartir" 
                                                data-mensaje-id="<?php echo $mensaje_item['id']; ?>" 
                                                title="compartir">
                                            <i class="fa-regular fa-share-from-square"></i>
                                        </button>

                                        <?php if ($es_desarrollador): ?> <!-- info solo para devs -->
                                        <span class="dev-info" title="ID del mensaje">
                                            <i class="fas fa-info-circle"></i> ID: <?php echo $mensaje_item['id']; ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
    
                                    <!-- Contenedor para comentarios (inicialmente oculto) -->
                                    <div class="seccion-comentarios" style="display: none;">
                                        <div class="formulario-comentario">
                                            <textarea class="input-comentario" placeholder="Escribe un comentario..." maxlength="300"></textarea>
                                            <div class="botones-comentario">
                                                <button class="boton-primario boton-enviar-comentario" data-mensaje-id="<?php echo $mensaje_item['id']; ?>">Comentar</button>
                                            </div>
                                        </div>
                                        <div class="lista-comentarios" data-mensaje-id="<?php echo $mensaje_item['id']; ?>">
                                            <!-- Los comentarios se cargarán aquí mediante AJAX -->
                                            <div class="cargando-comentarios">Cargando comentarios...</div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?> <!-- mensaje cuando no hay contenido -->
                            <div class="mensaje-sin-contenido">
                                <p>aún no hay mensajes en esta comunidad. ¡sé el primero en publicar!</p>
                            </div>
                            <?php
                        }
                    } else {
                        ?> <!-- mensaje para invitados -->
                        <div class="mensaje-sin-contenido">
                            <p>inicia sesión para ver los mensajes de la comunidad</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <!-- script para funcionalidad de formularios -->
    <script src="js/script.js"></script>
    <script src="js/script_comunidad.js"></script>
    
    <?php if ($es_desarrollador): ?> <!-- cargar script adicional solo para desarrolladores -->
    <!-- script específico para funcionalidades de desarrollador -->
    <script src="js/script_admin.js"></script>
    <?php endif; ?>

</body>
</html>