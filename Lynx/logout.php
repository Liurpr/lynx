<?php
// iniciar sesion
session_start();

// destruir todas las variables de sesion
$_SESSION = array();

// destruir la sesion
session_destroy();

// redirigir al index
header("Location: index.php");
exit();
?>