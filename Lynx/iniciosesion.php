<?php
// iniciar sesion para manejar mensajes y errores
session_start();

// incluir archivo de conexion a la base de datos
require_once 'conexion.php';

// variable para almacenar errores
$error_login = '';

// verificar si hay un mensaje de registro exitoso
if (isset($_SESSION['registro_exitoso'])) {
    $registro_exitoso = $_SESSION['registro_exitoso'];
    unset($_SESSION['registro_exitoso']);
}

// verificar si se ha enviado el formulario de inicio de sesion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // crear instancia de conexion a la base de datos
    $db = new DatabaseConnection();
    $conexion = $db->conectar();

    // limpiar datos del formulario
    $usuario_correo = $conexion->real_escape_string($_POST['usuario-correo']);
    $contrasena = $_POST['contrasena'];

    // preparar consulta para buscar usuario
    $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
    $consulta->bind_param("ss", $usuario_correo, $usuario_correo);
    $consulta->execute();
    $resultado = $consulta->get_result();

    // verificar si se encontro el usuario
    if ($resultado->num_rows == 1) {
        // obtener datos del usuario
        $usuario = $resultado->fetch_assoc();

        // verificar contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // iniciar sesion
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];

            // redirigir al dashboard
            header("Location: index.php");
            exit();
        } else {
            // contraseña incorrecta
            $error_login = "contraseña incorrecta";
        }
    } else {
        // usuario no encontrado
        $error_login = "usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-tema="claro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en Lynx: Plataforma interactiva para aprender Lenguaje de Señas Mexicano (LSM)">
    <title>Iniciar Sesión | Lynx - Aprende Lenguaje de Señas Mexicano</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- header del sitio (menu y botones adicionales, para optimizar el trabajo) -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-registro">
            <div class="contenedor-formulario">
                <h1 class="titulo-formulario">Iniciar sesión</h1>
                
                <form id="formulario-login" action="iniciosesion.php" method="post">
                    <!-- campo de usuario o correo electronico -->
                    <div class="grupo-formulario">
                        <label for="usuario-correo" class="label-formulario requerido">Usuario o correo electrónico</label>
                        <input type="text" id="usuario-correo" name="usuario-correo" class="input-formulario" placeholder="Tu nombre de usuario o correo" required>
                    </div>
                    
                    <!-- campo de contraseña -->
                    <div class="grupo-formulario">
                        <label for="contrasena" class="label-formulario requerido">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="input-formulario" placeholder="Tu contraseña" required>
                        <i class="fa-solid fa-eye-slash icono-input" id="mostrar-contrasena"></i>
                    </div>
                    
                    <!-- opciones adicionales -->
                    <div class="grupo-formulario opciones-login">
                        <div class="recordar">
                            <input type="checkbox" id="recordar-sesion" name="recordar-sesion">
                            <label for="recordar-sesion">Recordar mi sesión</label>
                        </div>
                        <a href="#" class="recuperar-contrasena">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <!-- Mensaje de error -->
                    <?php if (!empty($error_login)): ?>
                        <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin: 10px 0; display: flex; align-items: center; font-size: 14px;">
                            <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i>
                            <?php echo $error_login === "contraseña incorrecta" ? "Contraseña incorrecta" : "Usuario no encontrado"; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- boton de inicio de sesion -->
                    <button type="submit" class="boton-formulario">Iniciar sesión</button>
                    
                    <!-- enlace para crear cuenta -->
                    <p class="link-login">¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
                </form>
            </div>
        </section>
    </main>

    
    <!-- pie de página (footer) -->
    <?php include("zfooter.php"); ?>

    <!-- script para funcionalidad de formulario -->
    <script src="js/script.js"></script>
</body>
</html>