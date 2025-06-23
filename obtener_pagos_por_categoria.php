<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();

$stmt = $conn->query("
    SELECT cat.descripcion AS categoria, SUM(car.valor_pago) AS total 
    FROM tbl_cartera car
    JOIN tbl_cliente cli ON car.cedula_cliente = cli.cedula_cliente
    JOIN tbl_categoria cat ON cli.id_categoria = cat.id_categoria
    GROUP BY cat.descripcion
    ORDER BY total DESC
");

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($result, 'categoria');
$values = array_column($result, 'total');

echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
?>