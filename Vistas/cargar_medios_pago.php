<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$stmt = $conn->query("SELECT id_medio_pago, desc_medio_pago FROM tbl_medio_pago");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>