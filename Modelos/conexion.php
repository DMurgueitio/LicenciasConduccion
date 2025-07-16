<?php
// Este archivo se guarda en la carpeta modelo

class Conexion {
    static public function conectar() {
        try {
            
            $conexion = new PDO("mysql:host=localhost;dbname=Conductores;charset=utf8mb4", "root", "");

           
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            
            return $conexion;
        } catch (PDOException $e) {
            // En caso de error, muestra el mensaje de error y termina la ejecución
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }
}
?>