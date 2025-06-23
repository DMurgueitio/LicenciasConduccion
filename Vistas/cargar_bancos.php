<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$stmt = $conn->query("SELECT id_banco, desc_banco FROM tbl_banco");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>