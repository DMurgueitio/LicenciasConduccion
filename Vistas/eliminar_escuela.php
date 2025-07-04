<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelRol'] != 1) {
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar escuelas.']);
    exit();
}

require_once '../modelos/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("DELETE FROM tbl_escuela WHERE id_escuela = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Escuela eliminada correctamente.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar escuela: ' . $e->getMessage()]);
    }
}
?>