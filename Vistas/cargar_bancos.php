<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$stmt = $conn->query("SELECT id_Banco, descBanco FROM tbl_banco");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>