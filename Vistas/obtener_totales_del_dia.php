<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$today = (new DateTime())->format('Y-m-d');
$currentYear = date('Y');
$lastYear = $currentYear - 1;

try {
    // Total clientes del día
    $stmt_clientes = $conn->prepare("
        SELECT COUNT(DISTINCT cedula_cliente) AS total_clientes 
        FROM tbl_cartera 
        WHERE DATE(fecha_pago) = :fecha
    ");
    $stmt_clientes->bindValue(':fecha', $today);
    $stmt_clientes->execute();
    $result_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC);
    $total_clientes = (int)($result_clientes['total_clientes'] ?? 0);

    // Ingreso total del día
    $stmt_ingresos = $conn->prepare("
        SELECT SUM(valor_pago) AS total_pagos 
        FROM tbl_cartera 
        WHERE DATE(fecha_pago) = :fecha
    ");
    $stmt_ingresos->bindValue(':fecha', $today);
    $stmt_ingresos->execute();
    $result_pagos = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);
    $total_pagos = (float)($result_pagos['total_pagos'] ?? 0);

    // Ingresos por medio de pago
    $stmt_medios_pago = $conn->prepare("
        SELECT mp.id_medio_pago, mp.desc_medio_pago, SUM(c.valor_pago) AS total
        FROM tbl_cartera c
        LEFT JOIN tbl_medio_pago mp ON c.id_medio_pago = mp.id_medio_pago
        WHERE DATE(c.fecha_pago) = :fecha
        GROUP BY mp.id_medio_pago, mp.desc_medio_pago
    ");
    $stmt_medios_pago->bindValue(':fecha', $today);
    $stmt_medios_pago->execute();
    $medios_pago = $stmt_medios_pago->fetchAll(PDO::FETCH_ASSOC);

    // Acumulado año actual
    $stmt_year_current = $conn->prepare("
        SELECT SUM(valor_pago) AS total_anual 
        FROM tbl_cartera 
        WHERE YEAR(fecha_pago) = :anio
    ");
    $stmt_year_current->bindValue(':anio', $currentYear, PDO::PARAM_INT);
    $stmt_year_current->execute();
    $total_anual_actual = (float)($stmt_year_current->fetch(PDO::FETCH_ASSOC)['total_anual'] ?? 0);

    // Acumulado año anterior
    $stmt_year_last = $conn->prepare("
        SELECT SUM(valor_pago) AS total_anual 
        FROM tbl_cartera 
        WHERE YEAR(fecha_pago) = :anio
    ");
    $stmt_year_last->bindValue(':anio', $lastYear, PDO::PARAM_INT);
    $stmt_year_last->execute();
    $total_anual_anterior = (float)($stmt_year_last->fetch(PDO::FETCH_ASSOC)['total_anual'] ?? 0);

    // Armar respuesta
    $response = [
        'total_clientes' => $total_clientes,
        'total_pagos' => round($total_pagos, 2),
        'efectivo' => 0.0,
        'credito' => 0.0,
        'transferencias' => 0.0,
        'otros' => 0.0,
        'anual_actual' => round($total_anual_actual, 2),
        'anual_anterior' => round($total_anual_anterior, 2),
        'currentYear' => $currentYear,
        'lastYear' => $lastYear
    ];

    foreach ($medios_pago as $fila) {
        $desc = strtolower($fila['desc_medio_pago'] ?? '');
        $total = round((float)$fila['total'], 2);

        switch ($desc) {
            case 'efectivo':
                $response['efectivo'] = $total;
                break;
            case 'credito':
            case 'crédito':
                $response['credito'] = $total;
                break;
            case 'transferencia':
            case 'transferencias':
                $response['transferencias'] = $total;
                break;
            default:
                $response['otros'] += $total;
                break;
        }
    }

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
