<?php
session_start();

if ($_SESSION['NivelUsuario'] !== 'Administrador') {
    header("Location: ../login.php");
    exit();
}

require_once "../modelos/conexion.php";

$id = intval($_GET['id']);

try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("DELETE FROM tbl_usuario WHERE ID_Usuario = ?");
    $stmt->execute([$id]);

    $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
    $_SESSION['tipo'] = "success";
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error al eliminar usuario: " . $e->getMessage();
    $_SESSION['tipo'] = "danger";
}

header("Location: usuarios.php");
exit();