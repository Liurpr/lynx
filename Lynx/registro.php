<?php
// le damos play a la sesion pa guardar los errores y mensajitos
session_start();

// conectamos con la base de datos
require_once 'conexion.php';

// este array guarda los errores q vayan saliendo
$errores = [];
$campo_con_error = [];

// guardamos los datos q ya puso el user pa no tener q volverlos a escribir si hay error
$datos_formulario = [
    'usuario' => '',
    'correo' => '',
    'confirmar-correo' => '',
    'fecha_nacimiento' => '',
    'terminos' => false
];

// checamos si le dio al boton de enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // creamos la conexion a la bd
    $db = new DatabaseConnection();
    $conexion = $db->conectar();

    // limpiamos los datos pa q no nos metan codigo malicioso
    $usuario = $conexion->real_escape_string($_POST['usuario']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $confirmar_correo = $conexion->real_escape_string($_POST['confirmar-correo']);
    $contrasena = $_POST['contrasena'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $terminos = isset($_POST['terminos']) ? true : false;
    
    // guardamos los datos q puso pa no perderlos si hay error
    $datos_formulario = [
        'usuario' => $usuario,
        'correo' => $correo,
        'confirmar-correo' => $confirmar_correo,
        'fecha_nacimiento' => $fecha_nacimiento,
        'terminos' => $terminos
    ];
    
    // calculamos la edad segun la fecha q metio
    $hoy = new DateTime();
    $fecha_nac = new DateTime($fecha_nacimiento);
    $edad = $hoy->diff($fecha_nac)->y;

    // empezamos a validar q todo este bien
    if (empty($usuario)) {
        $errores[] = "El nombre de usuario es obligatorio.";
        $campo_con_error['usuario'] = true;
    } elseif (!preg_match("/^[a-zA-Z0-9]{4,20}$/", $usuario)) {
        $errores[] = "Nombre de usuario inválido. Debe tener mínimo 4 caracteres (letras o números).";
        $campo_con_error['usuario'] = true;
    }

    if (empty($correo)) {
        $errores[] = "El correo electrónico es obligatorio.";
        $campo_con_error['correo'] = true;
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico es inválido.";
        $campo_con_error['correo'] = true;
    }

    if ($correo !== $confirmar_correo) {
        $errores[] = "Los correos electrónicos no coinciden.";
        $campo_con_error['confirmar-correo'] = true;
    }

    if (empty($fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento es obligatoria.";
        $campo_con_error['fecha_nacimiento'] = true;
    } elseif ($edad < 5 || $edad > 100) {
        $errores[] = "Edad inválida. Debes tener entre 5 y 100 años.";
        $campo_con_error['fecha_nacimiento'] = true;
    }
    
    // checamos q la contraseña sea buena
    if (empty($contrasena)) {
        $errores[] = "La contraseña es obligatoria.";
        $campo_con_error['contrasena'] = true;
    } elseif (strlen($contrasena) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        $campo_con_error['contrasena'] = true;
    } elseif (!preg_match("/[A-Za-z]/", $contrasena) || !preg_match("/[0-9]/", $contrasena)) {
        $errores[] = "La contraseña debe contener letras y números.";
        $campo_con_error['contrasena'] = true;
    }
    
    // verificamos que haya aceptado los términos
    if (!$terminos) {
        $errores[] = "Debes aceptar los términos y condiciones para continuar.";
        $campo_con_error['terminos'] = true;
    }

    // nos aseguramos q no exista ya el user o el correo en la bd
    if (empty($errores) || (!isset($campo_con_error['usuario']) && !isset($campo_con_error['correo']))) {
        $consulta_existencia = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? OR correo = ?");
        $consulta_existencia->bind_param("ss", $usuario, $correo);
        $consulta_existencia->execute();
        $resultado = $consulta_existencia->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if ($fila['nombre_usuario'] === $usuario) {
                $errores[] = "El nombre de usuario ya está registrado.";
                $campo_con_error['usuario'] = true;
            }
            if ($fila['correo'] === $correo) {
                $errores[] = "El correo electrónico ya está registrado.";
                $campo_con_error['correo'] = true;
            }
        }
    }

    // si todo chido procedemos a registrar al user
    if (empty($errores)) {
        // encriptamos la contraseña pa q no se guarde en texto plano
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        // armamos la query pa insertar los datos
        $consulta = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo, edad, contrasena, fecha_nacimiento, acepto_terminos) VALUES (?, ?, ?, ?, ?, ?)");
        $consulta->bind_param("ssissi", $usuario, $correo, $edad, $contrasena_hash, $fecha_nacimiento, $terminos);

        // mandamos a hacer el registro
        if ($consulta->execute()) {
            // si se registro bien lo mandamos pa q inicie sesion
            $_SESSION['registro_exitoso'] = "Cuenta creada exitosamente. Inicia sesión.";
            header("Location: iniciosesion.php");
            exit();
        } else {
            $errores[] = "Error al registrar el usuario: " . $conexion->error;
        }
    }

    // guardamos los errores en la sesion si hay errores
    if (!empty($errores)) {
        $_SESSION['errores_registro'] = $errores;
        $_SESSION['campo_con_error'] = $campo_con_error;
        $_SESSION['datos_formulario'] = $datos_formulario;
    }
} else {
    // Si no es un POST, limpiamos las variables de sesión para evitar mostrar errores antiguos
    unset($_SESSION['errores_registro']);
    unset($_SESSION['campo_con_error']);
    unset($_SESSION['datos_formulario']);
}

// Verificamos si el usuario viene de un POST (envío de formulario)
// Si no es POST, aseguramos que no haya errores mostrados
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Limpiamos todas las variables de error
    $errores = [];
    $campo_con_error = [];
    $datos_formulario = [
        'usuario' => '',
        'correo' => '',
        'confirmar-correo' => '',
        'fecha_nacimiento' => '',
        'terminos' => false
    ];
    
    // Limpiamos cualquier error que pudiera estar en la sesión
    unset($_SESSION['errores_registro']);
    unset($_SESSION['campo_con_error']);
    unset($_SESSION['datos_formulario']);
} else {
    // Solo si venimos de un POST recuperamos los datos del formulario anterior
    if (isset($_SESSION['datos_formulario'])) {
        $datos_formulario = $_SESSION['datos_formulario'];
        unset($_SESSION['datos_formulario']);
    }

    // Solo si venimos de un POST recuperamos los errores
    if (isset($_SESSION['campo_con_error'])) {
        $campo_con_error = $_SESSION['campo_con_error'];
        unset($_SESSION['campo_con_error']);
    }
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
    <style>
        /* Ocultar mensajes de error por defecto */
        .mensaje-error {
            display: none;
        }
        .mensaje-error.mostrar-error {
            display: block;
        }
    </style>
</head>
<body>
    <!-- metemos el header del sitio -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="seccion-registro">
            <div class="contenedor-formulario">
                <h1 class="titulo-formulario">Crear cuenta</h1>
                
                <!-- si hay errores los mostramos aqui (sólo después de enviar el formulario) -->
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['errores_registro']) && !empty($_SESSION['errores_registro'])): ?>
                    <div class="alerta alerta-error">
                        <ul>
                            <?php foreach ($_SESSION['errores_registro'] as $error): ?>
                                <li><i class="fas fa-exclamation-circle icono-error"></i><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errores_registro']); ?>
                <?php endif; ?>
                
                <form id="formulario-registro" method="post" novalidate>
                    <!-- campo pa meter el nombre de usuario -->
                    <div class="grupo-formulario">
                        <label for="usuario" class="label-formulario requerido">Nombre de usuario</label>
                        <input type="text" id="usuario" name="usuario" 
                               class="input-formulario <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['usuario'])) ? 'campo-error' : ''; ?>" 
                               placeholder="Crea un nombre de usuario" 
                               value="<?php echo htmlspecialchars($datos_formulario['usuario']); ?>" 
                               required>
                        <p class="ayuda-texto">Este será tu identificador único en Lynx (mínimo 4 caracteres)</p>
                        <div class="mensaje-error" id="error-usuario">
                            <i class="fas fa-exclamation-circle"></i> El nombre de usuario debe tener mínimo 4 caracteres.
                        </div>
                    </div>
                    
                    <!-- campo pa meter el correo -->
                    <div class="grupo-formulario">
                        <label for="correo" class="label-formulario requerido">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" 
                               class="input-formulario <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['correo'])) ? 'campo-error' : ''; ?>" 
                               placeholder="tucorreo@ejemplo.com" 
                               value="<?php echo htmlspecialchars($datos_formulario['correo']); ?>" 
                               required>
                        <p class="ayuda-texto">Recibirás un mensaje de confirmación</p>
                        <div class="mensaje-error" id="error-correo">
                            <i class="fas fa-exclamation-circle"></i> Ingresa un correo electrónico válido.
                        </div>
                    </div>
                    
                    <!-- campo pa confirmar el correo -->
                    <div class="grupo-formulario">
                        <label for="confirmar-correo" class="label-formulario requerido">Confirmar correo electrónico</label>
                        <input type="email" id="confirmar-correo" name="confirmar-correo" 
                               class="input-formulario <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['confirmar-correo'])) ? 'campo-error' : ''; ?>" 
                               placeholder="Repite tu correo electrónico" 
                               value="<?php echo htmlspecialchars($datos_formulario['confirmar-correo']); ?>" 
                               required>
                        <div class="mensaje-error" id="error-confirmar-correo">
                            <i class="fas fa-exclamation-circle"></i> Los correos electrónicos no coinciden.
                        </div>
                    </div>
                    
                    <!-- campo pa la fecha de nacimiento -->
                    <div class="grupo-formulario grupo-fecha">
                        <div>
                            <label for="fecha_nacimiento" class="label-formulario requerido">Fecha de nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" 
                                   class="input-formulario campo-fecha <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['fecha_nacimiento'])) ? 'campo-error' : ''; ?>" 
                                   value="<?php echo htmlspecialchars($datos_formulario['fecha_nacimiento']); ?>" 
                                   required>
                            <div class="mensaje-error" id="error-fecha_nacimiento">
                                <i class="fas fa-exclamation-circle"></i> La fecha de nacimiento es obligatoria.
                            </div>
                        </div>
                        <p class="info-fecha">Si eres menor de edad deberás tener permiso de tu madre, padre o tutor legal para tener acceso a la comunidad</p>
                    </div>
                    
                    <!-- campo pa la contraseña -->
                    <div class="grupo-formulario">
                        <label for="contrasena" class="label-formulario requerido">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" 
                               class="input-formulario <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['contrasena'])) ? 'campo-error' : ''; ?>" 
                               placeholder="Crea una contraseña segura" 
                               required>
                        <i class="fa-solid fa-eye-slash icono-input" id="mostrar-contrasena"></i>
                        <p class="ayuda-texto">Mínimo 8 caracteres con letras y números</p>
                        <div class="mensaje-error" id="error-contrasena">
                            <i class="fas fa-exclamation-circle"></i> La contraseña debe tener al menos 8 caracteres y contener letras y números.
                        </div>
                    </div>
                    
                    <!-- términos y condiciones -->
                    <div class="checkbox-terminos <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['terminos'])) ? 'campo-error-checkbox' : ''; ?>">
                        <input type="checkbox" id="terminos" name="terminos" <?php echo (isset($datos_formulario['terminos']) && $datos_formulario['terminos']) ? 'checked' : ''; ?>>
                        <label for="terminos">
                            He leído y acepto los <span class="terminos-link" id="mostrar-terminos">términos y condiciones</span> y la <span class="terminos-link" id="mostrar-privacidad">política de privacidad</span>. Entiendo cómo se utilizarán mis datos personales.
                        </label>
                    </div>
                    <div class="mensaje-error" id="error-terminos" <?php echo ($_SERVER["REQUEST_METHOD"] == "POST" && isset($campo_con_error['terminos'])) ? 'style="display: block;"' : ''; ?>>
                        <i class="fas fa-exclamation-circle"></i> Debes aceptar los términos y condiciones para continuar.
                    </div>
                    
                    <!-- boton pa enviar el formulario -->
                    <button type="submit" class="boton-formulario boton-animacion">Crear cuenta</button>
                    
                    <!-- link pa iniciar sesion si ya tiene cuenta -->
                    <p class="link-login">¿Ya tienes una cuenta? <a href="iniciosesion.php">Inicia sesión</a></p>
                </form>
            </div>
        </section>
    </main>

    <!-- Modal para términos y condiciones -->
    <div id="modal-terminos" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="cerrar-terminos">&times;</span>
            <h2 class="modal-titulo">Términos y Condiciones</h2>
            <div>
                <h3>1. Aceptación de los Términos</h3>
                <p>Al registrarte y utilizar Lynx, aceptas todos los términos y condiciones aquí establecidos. Si no estás de acuerdo con alguna parte de estos términos, te pedimos que no utilices nuestro servicio.</p>
                
                <h3>2. Descripción del Servicio</h3>
                <p>Lynx es una plataforma educativa diseñada para el aprendizaje del Lenguaje de Señas Mexicano (LSM). Ofrecemos contenidos interactivos, lecciones, ejercicios y una comunidad de aprendizaje.</p>
                
                <h3>3. Registro y Cuenta</h3>
                <p>Para utilizar Lynx, debes registrarte proporcionando información precisa y mantenerla actualizada. Eres responsable de mantener la confidencialidad de tu contraseña y de todas las actividades que ocurran bajo tu cuenta.</p>
                
                <h3>4. Uso Adecuado</h3>
                <p>Te comprometes a utilizar Lynx de manera responsable, respetuosa y legal. No debes utilizar la plataforma para actividades ilegales, ofensivas o que violen los derechos de otros usuarios.</p>
                
                <h3>5. Contenido del Usuario</h3>
                <p>Al publicar contenido en Lynx, nos otorgas una licencia para usar, modificar y mostrar dicho contenido en relación con el servicio. Eres responsable de todo el contenido que publiques.</p>
                
                <h3>6. Propiedad Intelectual</h3>
                <p>Todo el contenido educativo, diseños, logos, y otros materiales de Lynx están protegidos por derechos de autor y otras leyes de propiedad intelectual. No puedes copiar, modificar o distribuir estos materiales sin autorización.</p>
                
                <h3>7. Limitación de Responsabilidad</h3>
                <p>Lynx se proporciona "tal cual" y no garantizamos que el servicio sea ininterrumpido o libre de errores. No somos responsables por daños indirectos, incidentales o consecuentes.</p>
                
                <h3>8. Modificaciones del Servicio</h3>
                <p>Nos reservamos el derecho de modificar o discontinuar Lynx en cualquier momento, con o sin previo aviso. No seremos responsables ante ti o terceros por ninguna modificación o interrupción del servicio.</p>
                
                <h3>9. Terminación</h3>
                <p>Podemos suspender o terminar tu acceso a Lynx en cualquier momento por violación de estos términos o por cualquier otra razón a nuestra discreción.</p>
                
                <h3>10. Ley Aplicable</h3>
                <p>Estos términos se rigen por las leyes de México y cualquier disputa será resuelta en los tribunales de esta jurisdicción.</p>
                
                <button id="aceptar-terminos" class="boton-formulario">Aceptar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal para política de privacidad -->
    <div id="modal-privacidad" class="modal">
        <div class="modal-contenido">
            <span class="cerrar-modal" id="cerrar-privacidad">&times;</span>
            <h2 class="modal-titulo">Política de Privacidad</h2>
            <div>
                <h3>1. Información que Recopilamos</h3>
                <p>Recopilamos información personal como tu nombre de usuario, correo electrónico y fecha de nacimiento durante el registro. También podemos recopilar información sobre tu uso de la plataforma y progreso de aprendizaje.</p>
                
                <h3>2. Uso de la Información</h3>
                <p>Utilizamos tu información para proporcionar, mantener y mejorar nuestros servicios, personalizar tu experiencia, comunicarnos contigo y garantizar la seguridad de la plataforma.</p>
                
                <h3>3. Compartir Información</h3>
                <p>No vendemos tu información personal. Podemos compartir información con proveedores de servicios que nos ayudan a operar Lynx o cuando sea requerido por ley.</p>
                
                <h3>4. Protección de Datos</h3>
                <p>Implementamos medidas de seguridad para proteger tu información personal contra acceso no autorizado, alteración o divulgación.</p>
                
                <h3>5. Cookies y Tecnologías Similares</h3>
                <p>Utilizamos cookies y tecnologías similares para mejorar tu experiencia, recordar tus preferencias y analizar cómo utilizas nuestra plataforma.</p>
                
                <h3>6. Derechos del Usuario</h3>
                <p>Tienes derecho a acceder, corregir o eliminar tu información personal. Puedes ejercer estos derechos contactándonos a través de nuestro formulario de contacto.</p>
                
                <h3>7. Menores de Edad</h3>
                <p>Lynx puede ser utilizado por menores con el consentimiento de sus padres o tutores legales. Si eres menor de edad, asegúrate de tener este permiso antes de registrarte.</p>
                
                <h3>8. Cambios en la Política de Privacidad</h3>
                <p>Podemos actualizar nuestra política de privacidad ocasionalmente. Te notificaremos sobre cambios significativos mediante un aviso en nuestra plataforma.</p>
                
                <h3>9. Contacto</h3>
                <p>Si tienes preguntas sobre nuestra política de privacidad, puedes contactarnos a través de nuestro formulario de contacto o enviando un correo electrónico a privacidad@lynx.com.</p>
                
                <button id="aceptar-privacidad" class="boton-formulario">Aceptar</button>
            </div>
        </div>
    </div>

    <!-- metemos el footer del sitio -->
    <?php include("zfooter.php"); ?>

    <!-- scripts necesarios -->
    <script src="js/script.js"></script>
    <script src="js/script-registro.js"></script>
    <script src="js/terminos.js"></script>
</body>
</html>