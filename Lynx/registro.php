<?php
// iniciar sesion para manejar mensajes y errores
session_start();

// incluir archivo de conexion a la base de datos
require_once 'conexion.php';

// variable para almacenar errores
$errores = [];

// verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // crear instancia de conexion a la base de datos
    $db = new DatabaseConnection();
    $conexion = $db->conectar();

    // limpiar y validar datos del formulario
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $confirmar_correo = $conexion->real_escape_string($_POST['confirmar-correo']);
    $edad = intval($_POST['edad']);
    $contrasena = $_POST['contrasena'];

    // validaciones
    if (empty($usuario) || !preg_match("/^[a-zA-Z0-9]{3,20}$/", $usuario)) {
        $errores[] = "nombre de usuario invalido";
    }

    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "correo electronico invalido";
    }

    if ($correo !== $confirmar_correo) {
        $errores[] = "los correos electronicos no coinciden";
    }

    if ($edad < 5 || $edad > 100) {
        $errores[] = "edad invalida";
    }

    // verificar si el usuario o correo ya existen
    $consulta_existencia = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
    $consulta_existencia->bind_param("ss", $usuario, $correo);
    $consulta_existencia->execute();
    $resultado = $consulta_existencia->get_result();

    if ($resultado->num_rows > 0) {
        $errores[] = "el nombre de usuario o correo ya esta registrado";
    }

    // si no hay errores, proceder con el registro
    if (empty($errores)) {
        // hashear contraseña
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // preparar consulta de insercion
        $consulta = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo, edad, contrasena) VALUES (?, ?, ?, ?)");
        $consulta->bind_param("ssis", $usuario, $correo, $edad, $contrasena_hash);

        // ejecutar registro
        if ($consulta->execute()) {
            // redirigir a inicio de sesion con mensaje de exito
            $_SESSION['registro_exitoso'] = "cuenta creada exitosamente. inicia sesion.";
            header("Location: iniciosesion.php");
            exit();
        } else {
            $errores[] = "error al registrar el usuario: " . $conexion->error;
        }
    }

    // almacenar errores en sesion
    $_SESSION['errores_registro'] = $errores;
}
?>
<!DOCTYPE html>
<html lang="es" data-tema="oscuro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Registro a Lynx: Plataforma interactiva para aprender Lenguaje de Señas Mexicano (LSM)">
    <title>Registro | Lynx - Aprende Lenguaje de Señas Mexicano</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- header del sitio (menu y botones adicionales, para optimizar el trabajo) -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-registro">
            <div class="contenedor-formulario">
                <h1 class="titulo-formulario">Crear cuenta</h1>
                
                <form id="formulario-registro" method="post">
                    <!-- campo de usuario -->
                    <div class="grupo-formulario">
                        <label for="usuario" class="label-formulario requerido">Nombre de usuario</label>
                        <input type="text" id="usuario" name="usuario" class="input-formulario" placeholder="Crea un nombre de usuario" required>
                        <p class="ayuda-texto">Este será tu identificador único en Lynx</p>
                    </div>
                    
                    <!-- campo de correo electronico -->
                    <div class="grupo-formulario">
                        <label for="correo" class="label-formulario requerido">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" class="input-formulario" placeholder="tucorreo@ejemplo.com" required>
                        <p class="ayuda-texto">Recibirás un mensaje de confirmación</p>
                    </div>
                    
                    <!-- campo de confirmacion de correo -->
                    <div class="grupo-formulario">
                        <label for="confirmar-correo" class="label-formulario requerido">Confirmar correo electrónico</label>
                        <input type="email" id="confirmar-correo" name="confirmar-correo" class="input-formulario" placeholder="Repite tu correo electrónico" required>
                    </div>
                    
                    <!-- campo de edad -->
                    <div class="grupo-formulario">
                        <label for="edad" class="label-formulario requerido">Edad</label>
                        <div class="grupo-edad">
                            <input type="number" id="edad" name="edad" class="input-formulario input-edad" min="5" max="120" placeholder="Edad" required>
                            <div class="info-edad">
                                <p class="ayuda-texto">Necesitamos verificar tu edad para brindarte la mejor experiencia</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- campo de contraseña (adicional para completar el registro) -->
                    <div class="grupo-formulario">
                        <label for="contrasena" class="label-formulario requerido">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="input-formulario" placeholder="Crea una contraseña segura" required>
                        <i class="fa-solid fa-eye-slash icono-input" id="mostrar-contrasena"></i>
                        <p class="ayuda-texto">Mínimo 8 caracteres con letras y números</p>
                    </div>
                    
                    <!-- boton de registro -->
                    <button type="submit" class="boton-formulario boton-animacion">Crear cuenta</button>
                    
                    <!-- enlace para iniciar sesion -->
                    <p class="link-login">¿Ya tienes una cuenta? <a href="iniciosesion.php">Inicia sesión</a></p>
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