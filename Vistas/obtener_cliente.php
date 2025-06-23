<?php
require_once "../modelos/conexion.php";
$cedula = $_GET['cedula'] ?? '';
$conn = Conexion::conectar();
$stmt = $conn->prepare("SELECT c.*, cat.descripcion 
                         FROM tbl_cliente c
                         JOIN tbl_categoria cat ON c.id_categoria = cat.id_categoria
                         WHERE c.cedula_cliente = ?");
$stmt->execute([$cedula]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
?>