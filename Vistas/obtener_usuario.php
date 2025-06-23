<?php
require_once "../modelos/conexion.php";

$id = intval($_GET['id']);

try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT * FROM tbl_usuario WHERE ID_Usuario = ?");
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}