<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "root";
$contrasena_db = "";
$base_datos = "investigar";

$conn = new mysqli($host, $usuario, $contrasena_db, $base_datos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener todos los usuarios
$resultado = $conn->query("SELECT ID_Usuario, contrasena FROM usuario_diligencia");

while ($fila = $resultado->fetch_assoc()) {
    $id = $fila['ID_Usuario'];
    $contrasena = $fila['contrasena'];

    // Verifica si la contraseña ya está hasheada (empieza por "$2y$")
    if (strpos($contrasena, '$2y$') !== 0) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario_diligencia SET contrasena = ? WHERE ID_Usuario = ?");
        $stmt->bind_param("ss", $hash, $id);
        $stmt->execute();
        echo "Contraseña actualizada para el usuario $id<br>";
    } else {
        echo "El usuario $id ya tiene contraseña hasheada<br>";
    }
}

$conn->close();
?>
