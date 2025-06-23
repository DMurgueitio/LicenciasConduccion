<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

require_once "../modelos/conexion.php";

try {
    $cedula = trim($_POST['id_usuario']);
    $nombre = trim($_POST['Nombre_usuario']);
    $email = trim($_POST['Email']);
    $contrasena = trim($_POST['contrasena']);
    $confirmar = trim($_POST['confirmar_contrasena']);
    $rol = 2;
    

    if ($contrasena !== $confirmar) {
        throw new Exception("Las contraseñas no coinciden.");
    }

    $conexion = Conexion::conectar();

    // Verificar si ya existe el usuario
    $stmt_check = $conexion->prepare("SELECT ID_Usuario FROM tbl_Usuario WHERE ID_Usuario = ?");
    $stmt_check->execute([$cedula]);
    if ($stmt_check->fetch()) {
        throw new Exception("Ya existe un usuario con esta cédula.");
    }

    // Insertar nuevo usuario
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt_insert = $conexion->prepare("INSERT INTO tbl_Usuario 
        (ID_Usuario, nombre_usuario, EMAIL, contrasena, NivelRol) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->execute([$cedula, $nombre, $email, $contrasena_hash, $rol]);

    echo json_encode([
        'success' => true,
        'message' => 'Registro exitoso. Redirigiendo...'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>