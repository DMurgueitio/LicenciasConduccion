<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$stmt = $conn->query("SELECT cedula_cliente, apellidos_nombre FROM tbl_cliente");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>