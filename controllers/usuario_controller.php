<!-- /controllers/usuario_controller.php -->
<?php
require_once '../includes/conexion.php';

class UsuarioController {

    private $conexion;

    public function __construct() {
        $conexion = new Conexion();
        $this->conexion = $conexion->conectar();
    }

    // Registrar un nuevo usuario
    public function registrarUsuario($nombre, $contrasena, $id_rol) {
        $stmt = $this->conexion->prepare("INSERT INTO tbl_usuario (nombre_usuario, contrasena, id_rol) VALUES (?, ?, ?)");
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt->bind_param("ssi", $nombre, $contrasena_hash, $id_rol);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Iniciar sesión
    public function loginUsuario($nombre, $contrasena) {
        $stmt = $this->conexion->prepare("SELECT id_usuario, nombre_usuario, contrasena, id_rol FROM tbl_usuario WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $nombre_usuario, $hash_contrasena, $id_rol);

        if ($stmt->fetch() && password_verify($contrasena, $hash_contrasena)) {
            return [
                'id_usuario' => $id,
                'nombre_usuario' => $nombre_usuario,
                'id_rol' => $id_rol
            ];
        } else {
            return false;
        }
    }

    // Listar todos los usuarios (opcional)
    public function listarUsuarios() {
        $result = $this->conexion->query("SELECT u.id_usuario, u.nombre_usuario, r.nivel_rol 
                                          FROM tbl_usuario u
                                          JOIN dbo_rol r ON u.id_rol = r.id_nivel_rol");

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    // Cerrar conexión
    public function __destruct() {
        mysqli_close($this->conexion);
    }
}
?>