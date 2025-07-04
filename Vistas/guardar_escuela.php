<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelRol'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permiso para registrar escuelas.'
    ]);
    exit();
}

require_once '../modelos/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['descri_escuela']);

    if (empty($nombre)) {
        echo json_encode([
            'success' => false,
            'message' => 'El nombre de la escuela es obligatorio.'
        ]);
        exit();
    }

    try {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO tbl_escuela (descri_escuela) VALUES (?)");
        $stmt->execute([$nombre]);

        $idEscuela = $conn->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Escuela registrada correctamente.',
            'id_escuela' => $idEscuela,
            'nombre' => $nombre
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()
        ]);
    }
}
?>