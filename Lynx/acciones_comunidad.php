<?php
// archivo para manejar acciones AJAX de la comunidad
session_start(); // iniciamos la sesion para tener acceso a los datos del usuario
header('Content-Type: application/json'); // le decimos al navegador que vamos a enviar json

// incluir archivo de conexión a la base de datos
require_once 'conexion.php'; // traemos el codigo de conexion a la bd

// verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) { // si no hay id de usuario en la sesion es que no esta logueado
    echo json_encode([ // mandamos respuesta json con error
        'exito' => false,
        'mensaje' => 'Debes iniciar sesión para realizar esta acción.',
        'redirigir' => 'iniciosesion.php' // le decimos a donde tiene que ir
    ]);
    exit(); // cortamos la ejecucion para que no siga
}

// obtener ID del usuario
$usuario_id = $_SESSION['usuario_id']; // guardamos el id en una variable para usarlo facil

// crear instancia de conexión a la base de datos
$db = new DatabaseConnection(); // creamos el objeto de conexion
$conexion = $db->conectar(); // llamamos al metodo conectar y guardamos la conexion

// determinar la acción a realizar
$accion = ''; // definimos variable vacia para guardar la accion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) { // revisamos si viene la accion por post
    $accion = $_POST['accion']; // si viene por post la tomamos
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion'])) { // revisamos si viene la accion por get
    $accion = $_GET['accion']; // si viene por get la tomamos
}

switch ($accion) { // segun la accion que nos pidieron hacemos diferentes cosas
    case 'like': // si quieren dar like
        // manejar like
        if (!isset($_POST['mensaje_id'])) { // verificamos que nos digan a que mensaje
            echo json_encode(['exito' => false, 'mensaje' => 'ID de mensaje no proporcionado']); // error si falta
            exit(); // cortamos ejecucion
        }
        
        $mensaje_id = intval($_POST['mensaje_id']); // convertimos el id a numero entero por seguridad
        
        // verificar si el mensaje existe
        $consulta = $conexion->prepare("SELECT id FROM comunidad WHERE id = ?"); // preparamos consulta para buscar el mensaje
        $consulta->bind_param("i", $mensaje_id); // metemos el id en la consulta (i = integer)
        $consulta->execute(); // ejecutamos la consulta
        $resultado = $consulta->get_result(); // obtenemos el resultado
        
        if ($resultado->num_rows === 0) { // si no hay resultados es que no existe el mensaje
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje no existe']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        // verificar si ya existe un like de este usuario en este mensaje
        $consulta = $conexion->prepare("SELECT id FROM reacciones_comunidad WHERE mensaje_id = ? AND usuario_id = ?"); // buscamos si ya dio like
        $consulta->bind_param("ii", $mensaje_id, $usuario_id); // metemos los dos ids (ii = dos integers)
        $consulta->execute(); // ejecutamos la consulta
        $resultado = $consulta->get_result(); // obtenemos resultado
        
        if ($resultado->num_rows > 0) { // si ya hay un resultado es que ya le dio like antes
            echo json_encode(['exito' => false, 'mensaje' => 'Ya has dado like a este mensaje']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        // insertar el like
        $consulta = $conexion->prepare("INSERT INTO reacciones_comunidad (mensaje_id, usuario_id, tipo_reaccion) VALUES (?, ?, 'like')"); // preparamos insert
        $consulta->bind_param("ii", $mensaje_id, $usuario_id); // metemos los ids
        
        if ($consulta->execute()) { // si se ejecuta bien el insert
            echo json_encode(['exito' => true, 'mensaje' => 'Like agregado correctamente']); // mandamos exito
        } else { // si hay error
            echo json_encode(['exito' => false, 'mensaje' => 'Error al agregar like: ' . $conexion->error]); // mandamos error con detalle
        }
        break;
        
    case 'unlike': // si quieren quitar un like
        // manejar unlike (quitar like)
        if (!isset($_POST['mensaje_id'])) { // verificamos que nos digan a que mensaje
            echo json_encode(['exito' => false, 'mensaje' => 'ID de mensaje no proporcionado']); // error si falta
            exit(); // cortamos ejecucion
        }
        
        $mensaje_id = intval($_POST['mensaje_id']); // convertimos a numero entero
        
        // eliminar el like
        $consulta = $conexion->prepare("DELETE FROM reacciones_comunidad WHERE mensaje_id = ? AND usuario_id = ?"); // preparamos delete 
        $consulta->bind_param("ii", $mensaje_id, $usuario_id); // metemos los ids
        
        if ($consulta->execute()) { // si se ejecuta bien
            echo json_encode(['exito' => true, 'mensaje' => 'Like eliminado correctamente']); // mandamos exito
        } else { // si hay error
            echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar like: ' . $conexion->error]); // mandamos error
        }
        break;
    
    case 'dislike': // si quieren dar dislike
        // manejar dislike
        if (!isset($_POST['mensaje_id'])) { // verificamos que nos digan a que mensaje
            echo json_encode(['exito' => false, 'mensaje' => 'ID de mensaje no proporcionado']); // error si falta
            exit(); // cortamos ejecucion
        }
            
        $mensaje_id = intval($_POST['mensaje_id']); // convertimos a numero entero
            
        // verificar si el mensaje existe
        $consulta = $conexion->prepare("SELECT id FROM comunidad WHERE id = ?"); // buscamos el mensaje
        $consulta->bind_param("i", $mensaje_id); // metemos el id
        $consulta->execute(); // ejecutamos
        $resultado = $consulta->get_result(); // obtenemos resultado
            
        if ($resultado->num_rows === 0) { // si no hay resultados no existe
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje no existe']); // mandamos error
            exit(); // cortamos ejecucion
        }
            
        // verificar si ya existe una reacción de este usuario en este mensaje
        $consulta = $conexion->prepare("SELECT id, tipo_reaccion FROM reacciones_comunidad WHERE mensaje_id = ? AND usuario_id = ?"); // buscamos si ya tiene reaccion
        $consulta->bind_param("ii", $mensaje_id, $usuario_id); // metemos los ids
        $consulta->execute(); // ejecutamos
        $resultado = $consulta->get_result(); // obtenemos resultado
            
        if ($resultado->num_rows > 0) { // si ya hay reaccion
            $reaccion = $resultado->fetch_assoc(); // sacamos los datos de la reaccion
            if ($reaccion['tipo_reaccion'] === 'dislike') { // si ya es dislike
                echo json_encode(['exito' => false, 'mensaje' => 'Ya has dado dislike a este mensaje']); // mandamos error
                exit(); // cortamos ejecucion
            } else { // si es otro tipo (like)
                // Si ya hay un like, actualizar a dislike
                $consulta = $conexion->prepare("UPDATE reacciones_comunidad SET tipo_reaccion = 'dislike' WHERE id = ?"); // preparamos update
                $consulta->bind_param("i", $reaccion['id']); // metemos el id de la reaccion
                    
                if ($consulta->execute()) { // si se ejecuta bien
                    echo json_encode(['exito' => true, 'mensaje' => 'Reacción actualizada a dislike']); // mandamos exito
                } else { // si hay error
                    echo json_encode(['exito' => false, 'mensaje' => 'Error al actualizar reacción: ' . $conexion->error]); // mandamos error
                }
                exit(); // cortamos ejecucion
            }
        }
            
        // insertar el dislike
        $consulta = $conexion->prepare("INSERT INTO reacciones_comunidad (mensaje_id, usuario_id, tipo_reaccion) VALUES (?, ?, 'dislike')"); // preparamos insert
        $consulta->bind_param("ii", $mensaje_id, $usuario_id); // metemos los ids
            
        if ($consulta->execute()) { // si se ejecuta bien
            echo json_encode(['exito' => true, 'mensaje' => 'Dislike agregado correctamente']); // mandamos exito
        } else { // si hay error
            echo json_encode(['exito' => false, 'mensaje' => 'Error al agregar dislike: ' . $conexion->error]); // mandamos error
        }
        break;

    case 'comentar': // si quieren comentar
        // manejar agregar comentario
        if (!isset($_POST['mensaje_id']) || !isset($_POST['comentario'])) { // verificamos que nos manden los datos necesarios
            echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos']); // mandamos error si falta
            exit(); // cortamos ejecucion
        }
        
        $mensaje_id = intval($_POST['mensaje_id']); // convertimos a numero entero
        $comentario = trim($_POST['comentario']); // quitamos espacios al inicio y final
        
        // validar comentario
        if (empty($comentario)) { // si el comentario esta vacio
            echo json_encode(['exito' => false, 'mensaje' => 'El comentario no puede estar vacío']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        if (strlen($comentario) > 300) { // si el comentario es muy largo
            echo json_encode(['exito' => false, 'mensaje' => 'El comentario no puede exceder los 300 caracteres']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        // verificar si el mensaje existe
        $consulta = $conexion->prepare("SELECT id FROM comunidad WHERE id = ?"); // buscamos el mensaje
        $consulta->bind_param("i", $mensaje_id); // metemos el id
        $consulta->execute(); // ejecutamos
        $resultado = $consulta->get_result(); // obtenemos resultado
        
        if ($resultado->num_rows === 0) { // si no hay resultados no existe
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje no existe']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        // insertar el comentario
        $consulta = $conexion->prepare("INSERT INTO comentarios_comunidad (mensaje_id, usuario_id, comentario) VALUES (?, ?, ?)"); // preparamos insert
        $consulta->bind_param("iis", $mensaje_id, $usuario_id, $comentario); // metemos los datos (iis = integer, integer, string)
        
        if ($consulta->execute()) { // si se ejecuta bien
            // obtener datos del comentario para devolver
            $comentario_id = $conexion->insert_id; // obtenemos el id del comentario recien insertado
            
            // obtener datos del usuario que comentó
            $consulta = $conexion->prepare("
                SELECT u.nombre_usuario, u.foto_perfil, c.comentario, c.fecha_creacion 
                FROM comentarios_comunidad c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.id = ?
            "); // preparamos consulta para obtener los datos del comentario con el usuario
            $consulta->bind_param("i", $comentario_id); // metemos el id
            $consulta->execute(); // ejecutamos
            $resultado = $consulta->get_result(); // obtenemos resultado
            $datos_comentario = $resultado->fetch_assoc(); // sacamos los datos
            
            // preparar los datos para devolver
            $comentario_info = [
                'nombre_usuario' => $datos_comentario['nombre_usuario'], // nombre de quien comento
                'foto_perfil' => !empty($datos_comentario['foto_perfil']) ? "img/profile/".$datos_comentario['foto_perfil'] : "img/profile/default_profile.jpg", // foto o default
                'comentario' => htmlspecialchars($datos_comentario['comentario']), // comentario escapado para seguridad
                'fecha_formateada' => date('d/m/Y H:i', strtotime($datos_comentario['fecha_creacion'])) // fecha bonita
            ];
            
            echo json_encode(['exito' => true, 'comentario' => $comentario_info]); // mandamos exito con los datos
        } else { // si hay error
            echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar el comentario: ' . $conexion->error]); // mandamos error
        }
        break;
        
    case 'obtener_comentarios': // si quieren ver comentarios
        // obtener comentarios de un mensaje
        if (!isset($_GET['mensaje_id'])) { // verificamos que nos digan de que mensaje
            echo json_encode(['exito' => false, 'mensaje' => 'ID de mensaje no proporcionado']); // mandamos error si falta
            exit(); // cortamos ejecucion
        }
        
        $mensaje_id = intval($_GET['mensaje_id']); // convertimos a numero entero
        
        // verificar si el mensaje existe
        $consulta = $conexion->prepare("SELECT id FROM comunidad WHERE id = ?"); // buscamos el mensaje
        $consulta->bind_param("i", $mensaje_id); // metemos el id
        $consulta->execute(); // ejecutamos
        $resultado = $consulta->get_result(); // obtenemos resultado
        
        if ($resultado->num_rows === 0) { // si no hay resultados no existe
            echo json_encode(['exito' => false, 'mensaje' => 'El mensaje no existe']); // mandamos error
            exit(); // cortamos ejecucion
        }
        
        // obtener comentarios
        $consulta = $conexion->prepare("
            SELECT c.id, c.comentario, c.fecha_creacion, u.nombre_usuario, u.foto_perfil 
            FROM comentarios_comunidad c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.mensaje_id = ?
            ORDER BY c.fecha_creacion DESC
        "); // preparamos consulta para obtener todos los comentarios con sus usuarios
        $consulta->bind_param("i", $mensaje_id); // metemos el id
        $consulta->execute(); // ejecutamos
        $resultado = $consulta->get_result(); // obtenemos resultado
        
        $comentarios = []; // creamos array vacio para guardar los comentarios
        while ($fila = $resultado->fetch_assoc()) { // recorremos todos los resultados
            $comentarios[] = [ // por cada resultado añadimos al array
                'id' => $fila['id'], // id del comentario
                'nombre_usuario' => $fila['nombre_usuario'], // nombre de quien comento
                'foto_perfil' => !empty($fila['foto_perfil']) ? "img/profile/".$fila['foto_perfil'] : "img/profile/default_profile.jpg", // foto o default
                'comentario' => htmlspecialchars($fila['comentario']), // comentario escapado
                'fecha_formateada' => date('d/m/Y H:i', strtotime($fila['fecha_creacion'])) // fecha bonita
            ];
        }
        
        echo json_encode(['exito' => true, 'comentarios' => $comentarios]); // mandamos exito con todos los comentarios
        break;
        
    default: // si la accion no es ninguna de las anteriores
        echo json_encode(['exito' => false, 'mensaje' => 'Acción no válida']); // mandamos error
        break;
}

// cerrar conexión
$conexion->close(); // cerramos la conexion a la bd para liberar recursos
?>