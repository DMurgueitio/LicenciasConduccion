<?php
require_once "../modelos/conexion.php";

$conn = Conexion::conectar();

$fecha_hoy = date('Y-m-d');

// Total de clientes registrados hoy (por ejemplo, basándote en la fecha de registro)
$stmt_clientes = $conn->prepare("SELECT COUNT(*) AS total FROM tbl_cliente WHERE DATE(fecha_registro) = ?");
$stmt_clientes->execute([$fecha_hoy]);
$total_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC)['total'];

// Total de dinero del día (usando tbl_cartera)
$stmt_pagos = $conn->prepare("SELECT 
    SUM(CASE WHEN id_medio_pago = 1 THEN valor_pago ELSE 0 END) AS efectivo,
    SUM(CASE WHEN id_medio_pago = 2 THEN valor_pago ELSE 0 END) AS credito,
    SUM(CASE WHEN id_medio_pago = 3 THEN valor_pago ELSE 0 END) AS transferencias,
    SUM(valor_pago) AS total_pagos
FROM tbl_cartera
WHERE DATE(fecha_pago) = ?");
$stmt_pagos->execute([$fecha_hoy]);

$result = $stmt_pagos->fetch(PDO::FETCH_ASSOC);
$total_pagos = floatval($result['total_pagos'] ?? 0);
$efectivo = floatval($result['efectivo'] ?? 0);
$credito = floatval($result['credito'] ?? 0);
$transferencias = floatval($result['transferencias'] ?? 0);

echo json_encode([
    "total_clientes" => $total_clientes,
    "total_pagos" => $total_pagos,
    "efectivo" => $efectivo,
    "credito" => $credito,
    "transferencias" => $transferencias
]);
?>