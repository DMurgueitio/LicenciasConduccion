<?php
// Este archivo se guarda en la carpeta modelo

class Conexion {
    static public function conectar() {
        try {
            // Conexión a la base de datos investigar usando PDO
            $conexion = new PDO("mysql:host=localhost;dbname=Conductores", "root", "");
            $conexion->exec("SET NAMES utf8"); // Codificación de caracteres
            return $conexion;
        } catch (PDOException $e) {
            // En caso de error, muestra el mensaje
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }
}
?>
