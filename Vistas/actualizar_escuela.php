<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelRol'] != 1) {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos.']);
    exit();
}

require_once '../modelos/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id_escuela']);
    $nombre = trim($_POST['descri_escuela']);

    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio.']);
        exit();
    }

    try {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("UPDATE tbl_escuela SET descri_escuela = ? WHERE id_escuela = ?");
        $stmt->execute([$nombre, $id]);

        echo json_encode(['success' => true, 'message' => 'Escuela actualizada.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
    }
}
?>