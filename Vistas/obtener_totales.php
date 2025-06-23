<?php
require_once "../modelos/conexion.php";

$conn = Conexion::conectar();

$stmt_clientes = $conn->query("SELECT COUNT(*) AS total FROM tbl_cliente");
$total_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC)['total'];

$stmt_pagos = $conn->query("SELECT SUM(valor_pago) AS total FROM tbl_cartera");
$total_pagos = floatval($stmt_pagos->fetch(PDO::FETCH_ASSOC)['total']);

echo json_encode([
    "total_clientes" => $total_clientes,
    "total_pagos" => $total_pagos
]);
?>