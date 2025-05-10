<?php
// configuracion de la conexion a la base de datos
class DatabaseConnection {
    // propiedades privadas para almacenar la informacion de conexion
    private $host = "localhost";     // nombre del servidor donde esta la bd
    private $usuario = "root";      // usuario para acceder a mysql (por defecto es root)
    private $contrasena = "";  // contraseña vacia para entornos locales de desarrollo
    private $nombreBaseDatos = "usuarios-lynx";  // nombre de nuestra base de datos

    //metodo para establecer la conexion
    public function conectar() {
        // crear conexion utilizando mysqli (objeto para interactuar con mysql)
        $conexion = new mysqli($this->host, $this->usuario, $this->contrasena, $this->nombreBaseDatos);
        
        // verificar si hay errores en la conexion
        if ($conexion->connect_error) {
            // detener el script y mostrar el error de conexion (para debug)
            die("error de conexion: " . $conexion->connect_error);
        }
        
        // configurar el conjunto de caracteres a utf-8 para manejar caracteres especiales
        $conexion->set_charset("utf8mb4"); // utf8mb4 soporta emojis y caracteres especiales
        
        // devolver el objeto de conexion para que otros scripts lo usen
        return $conexion;
    }
}
?>