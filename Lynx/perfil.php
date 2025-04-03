<?php
// iniciar sesion para verificar si el usuario esta logueado
session_start();

// incluir archivo de conexion a la base de datos
require_once 'conexion.php';

// variables para el perfil
$nombre_usuario = "invitado";
$foto_perfil = "img/profile/default_profile.jpg";
$descripcion = "este es un perfil de invitado, registrate para personalizar tu perfil";
$es_invitado = true;

// verificar si el usuario esta logueado
if (isset($_SESSION['usuario_id'])) {
    // crear instancia de conexion a la base de datos
    $db = new DatabaseConnection();
    $conexion = $db->conectar();
    
    // obtener id del usuario
    $usuario_id = $_SESSION['usuario_id'];
    
    // preparar consulta para buscar datos del usuario
    $consulta = $conexion->prepare("SELECT nombre_usuario, foto_perfil, descripcion FROM usuarios WHERE id = ?");
    $consulta->bind_param("i", $usuario_id);
    $consulta->execute();
    $resultado = $consulta->get_result();
    
    // si se encuentra el usuario, cargar sus datos
    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $nombre_usuario = $usuario['nombre_usuario'];
        $foto_perfil = !empty($usuario['foto_perfil']) ? "img/profile/".$usuario['foto_perfil'] : "img/profile/default_profile.jpg";
        $descripcion = !empty($usuario['descripcion']) ? $usuario['descripcion'] : "no hay descripcion disponible";
        $es_invitado = false;
    }
    
    // si se ha enviado el formulario para actualizar perfil
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // procesar la descripcion
        if (isset($_POST['descripcion'])) {
            $nueva_descripcion = $conexion->real_escape_string($_POST['descripcion']);
            
            // procesar la imagen si se ha subido una nueva
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
                $archivo = $_FILES['foto_perfil'];
                $nombre_archivo = $usuario_id . '_' . time() . '.jpg';
                $ruta_destino = 'img/profile/' . $nombre_archivo;
                
                // verificar que sea una imagen
                $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($archivo['type'], $tipos_permitidos)) {
                    // mover el archivo subido a la carpeta de destino
                    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                        // actualizar la ruta en la base de datos
                        $actualizar = $conexion->prepare("UPDATE usuarios SET foto_perfil = ?, descripcion = ? WHERE id = ?");
                        $actualizar->bind_param("ssi", $nombre_archivo, $nueva_descripcion, $usuario_id);
                    }
                }
            } else {
                // actualizar solo la descripcion
                $actualizar = $conexion->prepare("UPDATE usuarios SET descripcion = ? WHERE id = ?");
                $actualizar->bind_param("si", $nueva_descripcion, $usuario_id);
            }
            
            // ejecutar la actualizacion
            if (isset($actualizar)) {
                $actualizar->execute();
                // recargar la pagina para mostrar los cambios
                header("Location: perfil.php");
                exit();
            }
        }
    }
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
    <style>
        
    </style>
</head>
<body>
    <!-- incluir el header -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-perfil">
            <div class="contenedor-perfil">
                <?php if ($es_invitado): ?>
                <div class="mensaje-invitado">
                    <h2>aun no te has registrado :c</h2>
                    <p>registrate para crear y personalizar tu perfil</p>
                    <div class="botones-accion">
                        <a href="registro.php" class="boton-primario">registrarse</a>
                        <a href="iniciosesion.php" class="boton-secundario">iniciar sesion</a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="cabecera-perfil">
                    <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil" class="foto-perfil">
                    <h1 class="nombre-usuario"><?php echo $nombre_usuario; ?></h1>
                    <span class="etiqueta-usuario"><?php echo $es_invitado ? 'invitado' : 'estudiante'; ?></span>
                </div>
                
                <div class="descripcion-perfil">
                    <h2 class="titulo-seccion-perfil">sobre mi</h2>
                    <p class="texto-descripcion"><?php echo $descripcion; ?></p>
                </div>
                
                <?php if (!$es_invitado): ?>
                <form class="formulario-perfil" method="post" enctype="multipart/form-data">
                    <h2 class="titulo-seccion-perfil">editar perfil</h2>
                    
                    <div class="grupo-formulario">
                        <label for="descripcion" class="label-formulario">descripcion</label>
                        <textarea id="descripcion" name="descripcion" class="input-formulario" rows="4"><?php echo $descripcion; ?></textarea>
                    </div>
                    
                    <div class="grupo-formulario">
                        <div class="input-file-container">
                            <label for="foto_perfil" class="input-file-trigger">
                                <i class="fa-solid fa-upload"></i> cambiar foto de perfil
                            </label>
                            <input type="file" id="foto_perfil" name="foto_perfil" class="input-file" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="botones-accion">
                        <button type="submit" class="boton-primario">guardar cambios</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- script para funcionalidad de formulario -->
    <script src="js/script.js"></script>
</body>
</html>