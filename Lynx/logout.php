<?php
// arrancamos la sesion para poder manipularla
session_start();

// borramos todo lo que hay en la sesion, vaciando el array
$_SESSION = array();

// matamos la sesion por completo
session_destroy();

// mandamos al usuario a la pagina principal
header("Location: index.php");
// detenemos la ejecucion para que no siga el script
exit();
?>