<?php
// configuracion de la conexion a la base de datos
class DatabaseConnection {
    // propiedades privadas para almacenar la informacion de conexion
    private $host = "localhost";     // servidor de la base de datos
    private $usuario = "root";      // nombre de usuario de mysql
    private $contrasena = "";  // contraseña del usuario de mysql
    private $nombreBaseDatos = "usuarios-lynx";  // nombre de la base de datos

    //metodo para establecer la conexion
    public function conectar() {
        // crear conexion utilizando mysqli
        $conexion = new mysqli($this->host, $this->usuario, $this->contrasena, $this->nombreBaseDatos);
        
        // verificar si hay errores en la conexion
        if ($conexion->connect_error) {
            // detener el script y mostrar el error de conexion
            die("error de conexion: " . $conexion->connect_error);
        }
        
        // configurar el conjunto de caracteres a utf-8 para manejar caracteres especiales
        $conexion->set_charset("utf8mb4");
        
        // devolver el objeto de conexion
        return $conexion;
    }
}
?>