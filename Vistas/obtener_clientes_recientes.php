<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();

$stmt = $conn->query("SELECT c.cedula_cliente, c.apellidos_nombre, c.telefono, cat.descripcion 
                      FROM tbl_cliente c
                      LEFT JOIN tbl_categoria cat ON c.id_categoria = cat.id_categoria
                      ORDER BY c.fecha_registro DESC LIMIT 5");

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($clientes);
?>