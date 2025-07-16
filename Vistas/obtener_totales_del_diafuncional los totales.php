<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$today = date('Y-m-d');
$currentYear = date('Y');
$lastYear = $currentYear - 1;

try {
    // Total clientes del día
    $stmt_clientes = $conn->prepare("
        SELECT COUNT(DISTINCT cedula_cliente) AS total_clientes 
        FROM tbl_cartera 
        WHERE DATE(fecha_pago) = ?
    ");
    $stmt_clientes->execute([$today]);
    $result_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC);
    $total_clientes = (int)($result_clientes['total_clientes'] ?? 0);

    // Ingreso total del día
    $stmt_ingresos = $conn->prepare("
        SELECT SUM(valor_pago) AS total_pagos 
        FROM tbl_cartera 
        WHERE DATE(fecha_pago) = ?
    ");
    $stmt_ingresos->execute([$today]);
    $result_pagos = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);
    $total_pagos = (float)($result_pagos['total_pagos'] ?? 0);

    // Ingresos por medio de pago
    $stmt_medios_pago = $conn->prepare("
        SELECT mp.id_medio_pago, mp.desc_medio_pago, SUM(c.valor_pago) AS total
        FROM tbl_cartera c
        JOIN tbl_medio_pago mp ON c.id_medio_pago = mp.id_medio_pago
        WHERE DATE(c.fecha_pago) = ?
        GROUP BY mp.id_medio_pago, mp.desc_medio_pago
    ");
    $stmt_medios_pago->execute([$today]);
    $medios_pago = $stmt_medios_pago->fetchAll(PDO::FETCH_ASSOC);

    // Acumulados anuales
    $stmt_year_current = $conn->prepare("SELECT SUM(valor_pago) AS total_anual FROM tbl_cartera WHERE YEAR(fecha_pago) = ?");
    $stmt_year_current->execute([$currentYear]);
    $total_anual_actual = (float)($stmt_year_current->fetch(PDO::FETCH_ASSOC)['total_anual'] ?? 0);

    $stmt_year_last = $conn->prepare("SELECT SUM(valor_pago) AS total_anual FROM tbl_cartera WHERE YEAR(fecha_pago) = ?");
    $stmt_year_last->execute([$lastYear]);
    $total_anual_anterior = (float)($stmt_year_last->fetch(PDO::FETCH_ASSOC)['total_anual'] ?? 0);

    // Formatear respuesta
    $response = [
        'total_clientes' => $total_clientes,
        'total_pagos' => round($total_pagos, 2),
        'efectivo' => 0.0,
        'credito' => 0.0,
        'transferencias' => 0.0,
        'anual_actual' => round($total_anual_actual, 2),
        'anual_anterior' => round($total_anual_anterior, 2),
        'currentYear' => $currentYear,
        'lastYear' => $lastYear
    ];

    foreach ($medios_pago as $fila) {
        switch(strtolower($fila['desc_medio_pago'])) {
            case 'efectivo':
                $response['efectivo'] = round((float)$fila['total'], 2);
                break;
            case 'crédito':
            case 'credito':
                $response['credito'] = round((float)$fila['total'], 2);
                break;
            case 'transferencia':
            case 'transferencias':
                $response['transferencias'] = round((float)$fila['total'], 2);
                break;
        }
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>