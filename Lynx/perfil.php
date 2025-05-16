
<?php
// arranca la movida de la sesion pa ver si el usuario esta logueado ya
session_start();

// metemos el archivo q conecta con la bd
require_once 'conexion.php';

// estas variables son pa guardar los datos del perfil del usuario
$nombre_usuario = "invitado";
$foto_perfil = "img/profile/default_profile.jpg";
$descripcion = "este es un perfil de invitado, registrate para personalizar tu perfil";
$es_invitado = true;
$rol = "invitado"; // esto guarda q tipo de usuario es
$mensaje = "";
$tipo_mensaje = "";

// checamos si hay sesion activa del usuario
if (isset($_SESSION['usuario_id'])) {
    // creamos la conexion a la bd
    $db = new DatabaseConnection();
    $conexion = $db->conectar();
    
    // sacamos el id del usuario de la sesion
    $usuario_id = $_SESSION['usuario_id'];
    
    // armamos la consulta pa sacar los datos del usuario
    $consulta = $conexion->prepare("SELECT nombre_usuario, foto_perfil, descripcion, rol FROM usuarios WHERE id = ?");
    $consulta->bind_param("i", $usuario_id);
    $consulta->execute();
    $resultado = $consulta->get_result();
    
    // si encontramos al usuario, jalamos sus datos
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $nombre_usuario = $usuario['nombre_usuario'];
        $foto_perfil = !empty($usuario['foto_perfil']) ? "img/profile/".$usuario['foto_perfil'] : "img/profile/default_profile.jpg";
        $descripcion = !empty($usuario['descripcion']) ? $usuario['descripcion'] : "no hay descripcion disponible";
        $rol = !empty($usuario['rol']) ? $usuario['rol'] : "estudiante"; // si no hay rol le ponemos estudiante por default
        $es_invitado = false;
    }
    
    // procesamos el form si el usuario mando datos
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // vemos q accion quiere hacer - actualizar perfil o borrar foto
        if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_foto') {
            // este es pa borrar la foto de perfil
            if ($usuario['foto_perfil'] != '') {
                // borramos el archivo si existe y no es el default
                $ruta_archivo = 'img/profile/' . $usuario['foto_perfil'];
                if (file_exists($ruta_archivo) && $usuario['foto_perfil'] != 'default_profile.jpg') {
                    unlink($ruta_archivo);
                }
                
                // actualizamos la bd pa quitar la foto
                $actualizar = $conexion->prepare("UPDATE usuarios SET foto_perfil = '' WHERE id = ?");
                $actualizar->bind_param("i", $usuario_id);
                
                if ($actualizar->execute()) {
                    $mensaje = "foto de perfil eliminada correctamente";
                    $tipo_mensaje = "exito";
                    // actualizamos la variable local
                    $foto_perfil = "img/profile/default_profile.jpg";
                    // recargamos la pagina pa q se vean los cambios
                    header("Location: perfil.php?msg=foto_eliminada");
                    exit();
                }
            }
        } elseif (isset($_POST['accion']) && $_POST['accion'] == 'cambiar_rol' && $rol == 'desarrollador') {
            // solo los devs pueden cambiar roles
            $tipo_busqueda = isset($_POST['tipo_busqueda']) ? $_POST['tipo_busqueda'] : 'id';
            $nuevo_rol = $conexion->real_escape_string($_POST['nuevo_rol']);
            $usuario_encontrado = false;
            
            // checamos q el rol sea valido
            $roles_validos = ['estudiante', 'profesor', 'desarrollador'];
            
            if (in_array($nuevo_rol, $roles_validos)) {
                if ($tipo_busqueda == 'id' && isset($_POST['usuario_id']) && !empty($_POST['usuario_id'])) {
                    // buscar por id
                    $usuario_id_cambio = intval($_POST['usuario_id']);
                    
                    // vemos si existe el usuario
                    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE id = ?");
                    $verificar->bind_param("i", $usuario_id_cambio);
                    $verificar->execute();
                    $resultado_verificacion = $verificar->get_result();
                    
                    if ($resultado_verificacion->num_rows == 1) {
                        $usuario_encontrado = true;
                        $actualizar_rol = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                        $actualizar_rol->bind_param("si", $nuevo_rol, $usuario_id_cambio);
                    } else {
                        $mensaje = "no se encontró ningún usuario con ese ID";
                        $tipo_mensaje = "error";
                    }
                    
                } elseif ($tipo_busqueda == 'nombre' && isset($_POST['nombre_usuario']) && !empty($_POST['nombre_usuario'])) {
                    // buscar por nombre
                    $nombre_usuario_busqueda = $conexion->real_escape_string($_POST['nombre_usuario']);
                    
                    // verificamos si el user existe
                    $verificar = $conexion->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
                    $verificar->bind_param("s", $nombre_usuario_busqueda);
                    $verificar->execute();
                    $resultado_verificacion = $verificar->get_result();
                    
                    if ($resultado_verificacion->num_rows == 1) {
                        $usuario_encontrado = true;
                        $actualizar_rol = $conexion->prepare("UPDATE usuarios SET rol = ? WHERE nombre_usuario = ?");
                        $actualizar_rol->bind_param("ss", $nuevo_rol, $nombre_usuario_busqueda);
                    } else {
                        $mensaje = "no se encontró ningún usuario con ese nombre";
                        $tipo_mensaje = "error";
                    }
                } else {
                    $mensaje = "debes proporcionar un ID o un nombre de usuario válido";
                    $tipo_mensaje = "error";
                }
                
                // si encontramos al usuario, actualizamos su rol
                if ($usuario_encontrado && isset($actualizar_rol)) {
                    if ($actualizar_rol->execute()) {
                        $mensaje = "rol actualizado correctamente";
                        $tipo_mensaje = "exito";
                    } else {
                        $mensaje = "error al actualizar el rol: " . $conexion->error;
                        $tipo_mensaje = "error";
                    }
                }
            } else {
                $mensaje = "rol no válido";
                $tipo_mensaje = "error";
            }
        } else {
            // aqui procesamos la actualizacion normal del perfil
            $actualizar_descripcion = false;
            $actualizar_foto = false;
            $error_encontrado = false;
            
            // procesamos la descripcion
            if (isset($_POST['descripcion'])) {
                // checamos q la descripcion no sea muy larga
                $nueva_descripcion = trim($_POST['descripcion']);
                
                if (strlen($nueva_descripcion) > 500) {
                    $mensaje = "la descripcion no puede exceder los 500 caracteres";
                    $tipo_mensaje = "error";
                    $error_encontrado = true;
                } else {
                    $nueva_descripcion = $conexion->real_escape_string($nueva_descripcion);
                    $actualizar_descripcion = true;
                }
            }
            
            // procesamos la imagen si se subio una nueva
            $nombre_archivo = '';
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0 && $_FILES['foto_perfil']['size'] > 0) {
                $archivo = $_FILES['foto_perfil'];
                $nombre_archivo = $usuario_id . '_' . time() . '.jpg';
                $ruta_destino = 'img/profile/' . $nombre_archivo;
                
                // checamos q sea una imagen valida
                $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
                
                if (in_array($archivo['type'], $tipos_permitidos)) {
                    // checamos q no sea muy grande (max 2MB)
                    if ($archivo['size'] <= 2097152) { // 2MB en bytes
                        // checamos las dimensiones
                        $info_imagen = getimagesize($archivo['tmp_name']);
                        if ($info_imagen !== false) {
                            // la imagen parece buena
                            $actualizar_foto = true;
                        } else {
                            $mensaje = "el archivo no es una imagen valida";
                            $tipo_mensaje = "error";
                            $error_encontrado = true;
                        }
                    } else {
                        $mensaje = "la imagen no debe superar los 2MB";
                        $tipo_mensaje = "error";
                        $error_encontrado = true;
                    }
                } else {
                    $mensaje = "solo se permiten imagenes jpg, png y gif";
                    $tipo_mensaje = "error";
                    $error_encontrado = true;
                }
            }
            
            // si todo bien, actualizamos
            if (!$error_encontrado) {
                // armamos la query segun lo q vamos a actualizar
                if ($actualizar_foto && $actualizar_descripcion) {
                    // actualizamos foto y descripcion
                    $actualizar = $conexion->prepare("UPDATE usuarios SET foto_perfil = ?, descripcion = ? WHERE id = ?");
                    $actualizar->bind_param("ssi", $nombre_archivo, $nueva_descripcion, $usuario_id);
                } elseif ($actualizar_foto) {
                    // actualizamos solo la foto
                    $actualizar = $conexion->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
                    $actualizar->bind_param("si", $nombre_archivo, $usuario_id);
                } elseif ($actualizar_descripcion) {
                    // actualizamos solo la descripcion
                    $actualizar = $conexion->prepare("UPDATE usuarios SET descripcion = ? WHERE id = ?");
                    $actualizar->bind_param("si", $nueva_descripcion, $usuario_id);
                }
                
                // ejecutamos la query si hay una
                if (isset($actualizar)) {
                    if ($actualizar->execute()) {
                        // si actualizamos la foto, movemos el archivo
                        if ($actualizar_foto) {
                            // borramos la foto anterior si existe
                            if (!empty($usuario['foto_perfil']) && $usuario['foto_perfil'] != 'default_profile.jpg') {
                                $ruta_anterior = 'img/profile/' . $usuario['foto_perfil'];
                                if (file_exists($ruta_anterior)) {
                                    unlink($ruta_anterior);
                                }
                            }
                            
                            // movemos el archivo nuevo a donde va
                            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                                // actualizamos la variable local
                                $foto_perfil = "img/profile/" . $nombre_archivo;
                            } else {
                                $mensaje = "error al subir la imagen";
                                $tipo_mensaje = "error";
                                $error_encontrado = true;
                            }
                        }
                        
                        // actualizamos la descripcion en la variable
                        if ($actualizar_descripcion) {
                            $descripcion = $nueva_descripcion;
                        }
                        
                        if (!$error_encontrado) {
                            $mensaje = "perfil actualizado correctamente";
                            $tipo_mensaje = "exito";
                            // recargamos la pagina pa ver los cambios
                            header("Location: perfil.php?msg=actualizado");
                            exit();
                        }
                    } else {
                        $mensaje = "error al actualizar el perfil: " . $conexion->error;
                        $tipo_mensaje = "error";
                    }
                }
            }
        }
    }
    
    // procesamos mensajes q vienen por url
    if (isset($_GET['msg'])) {
        switch ($_GET['msg']) {
            case 'actualizado':
                $mensaje = "perfil actualizado correctamente";
                $tipo_mensaje = "exito";
                break;
            case 'foto_eliminada':
                $mensaje = "foto de perfil eliminada correctamente";
                $tipo_mensaje = "exito";
                break;
        }
    }
}

// funcion pa saber si el user actual es dev
function es_desarrollador() {
    return isset($GLOBALS['rol']) && $GLOBALS['rol'] == 'desarrollador';
}
?>

<!DOCTYPE html>
<html lang="es" data-tema="oscuro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perfil de usuario en Lynx: Plataforma interactiva para aprender Lenguaje de Señas Mexicano (LSM)">
    <title>Perfil | Lynx - Aprende Lenguaje de Señas Mexicano</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- incluir el header -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-perfil">
            <div class="contenedor-perfil">
                <?php if (!empty($mensaje)): ?>
                <!-- mostramos mensajes de error o exito si hay -->
                <div class="mensaje-alerta <?php echo $tipo_mensaje; ?>">
                    <p><?php echo $mensaje; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($es_invitado): ?>
                <!-- mostramos mensaje pa los invitados -->
                <div class="mensaje-invitado">
                    <h2>aun no te has registrado</h2>
                    <p>registrate para crear y personalizar tu perfil</p>
                    <div class="botones-accion">
                        <a href="registro.php" class="boton-primario">registrarse</a>
                        <a href="iniciosesion.php" class="boton-secundario">iniciar sesion</a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="cabecera-perfil">
                    <div class="contenedor-foto-perfil">
                        <!-- mostramos la foto de perfil -->
                        <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil" class="foto-perfil">
                        <?php if (!$es_invitado): ?>
                        <!-- opciones pa cambiar o borrar foto -->
                        <div class="overlay-foto">
                            <form method="post" class="form-eliminar-foto">
                                <input type="hidden" name="accion" value="eliminar_foto">
                                <button type="submit" class="boton-eliminar-foto" title="eliminar foto">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            <label for="foto_perfil" class="boton-cambiar-foto" title="cambiar foto">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="info-usuario">
                        <!-- mostramos nombre y tipo de usuario -->
                        <h1 class="nombre-usuario"><?php echo $nombre_usuario; ?></h1>
                        <span class="etiqueta-usuario <?php echo $rol; ?>"><?php echo $rol; ?></span>
                    </div>
                </div>
                
                <div class="descripcion-perfil">
                    <h2 class="titulo-seccion-perfil">sobre mi</h2>
                    <!-- mostramos la descripcion del perfil -->
                    <p class="texto-descripcion"><?php echo $descripcion; ?></p>
                </div>
                
                <?php if (!$es_invitado): ?>
                <!-- formulario pa editar el perfil -->
                <form class="formulario-perfil" method="post" enctype="multipart/form-data" id="form-perfil">
                    <h2 class="titulo-seccion-perfil">editar perfil</h2>
                    
                    <div class="grupo-formulario">
                        <label for="descripcion" class="label-formulario">descripcion <span class="contador-caracteres"><span id="contador">0</span>/500</span></label>
                        <textarea id="descripcion" name="descripcion" class="input-formulario" rows="4" maxlength="500"><?php echo $descripcion; ?></textarea>
                    </div>
                    
                    <div class="grupo-formulario">
                        <div class="input-file-container">
                            <input type="file" id="foto_perfil" name="foto_perfil" class="input-file" accept="image/jpeg, image/png, image/gif">
                            <label for="foto_perfil" class="input-file-trigger">
                                <i class="fa-solid fa-upload"></i> cambiar foto de perfil
                            </label>
                            <span class="archivo-seleccionado" id="archivo-seleccionado">ningún archivo seleccionado</span>
                            <p class="restricciones-archivo">formatos: jpg, png, gif | tamaño máximo: 2MB</p>
                        </div>
                    </div>
                    
                    <div class="botones-accion">
                        <button type="submit" class="boton-primario">guardar cambios</button>
                        <button type="reset" class="boton-secundario">cancelar</button>
                    </div>
                </form>
                <?php endif; ?>
                
                <!-- panel de admin solo pa desarrolladores -->
                <?php if (!$es_invitado && $rol == 'desarrollador'): ?>
                <div class="panel-admin">
                    <h2 class="titulo-seccion-perfil">panel de administración</h2>
                    
                    <form class="formulario-admin" method="post">
                        <input type="hidden" name="accion" value="cambiar_rol">
                        
                        <div class="grupo-formulario">
                            <label class="label-formulario">buscar usuario por:</label>
                            <div class="opciones-busqueda">
                                <label>
                                    <input type="radio" name="tipo_busqueda" value="id" checked> ID
                                </label>
                                <label>
                                    <input type="radio" name="tipo_busqueda" value="nombre"> nombre de usuario
                                </label>
                            </div>
                        </div>
                        
                        <div class="grupo-formulario campo-id">
                            <label for="usuario_id" class="label-formulario">ID del usuario</label>
                            <input type="number" id="usuario_id" name="usuario_id" class="input-formulario">
                        </div>
                        
                        <div class="grupo-formulario campo-nombre" style="display: none;">
                            <label for="nombre_usuario" class="label-formulario">nombre de usuario</label>
                            <input type="text" id="nombre_usuario" name="nombre_usuario" class="input-formulario">
                        </div>
                        
                        <div class="grupo-formulario">
                            <label for="nuevo_rol" class="label-formulario">nuevo rol</label>
                            <select id="nuevo_rol" name="nuevo_rol" class="input-formulario" required>
                                <option value="estudiante">estudiante</option>
                                <option value="profesor">profesor</option>
                                <option value="desarrollador">desarrollador</option>
                            </select>
                        </div>
                        
                        <div class="botones-accion">
                            <button type="submit" class="boton-primario">cambiar rol</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- script para funcionalidad de formulario -->
    <script src="js/script.js"></script>
    
</body>
</html>