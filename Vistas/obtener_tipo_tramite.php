<?php
session_start();

if (!isset($_SESSION['ID_Usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado.']);
    exit();
}

require_once "../modelos/conexion.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Conexion::conectar();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener la cantidad de trámites por tipo y por año
    // Asumo que 'tipo de trámite' está relacionado con la 'Categoría' del cliente
    // o con algún tipo de servicio asociado a la 'cartera'.
    // Si 'tbl_cartera' no tiene un campo directo para 'tipo_tramite',
    // deberás ajustar esta consulta a cómo lo modelaste.
    // He asumido que puedes unir tbl_cartera con tbl_cliente_categoria y tbl_categoria.
    $stmt = $conn->prepare("
        SELECT
            cat.descripcion AS tipo_tramite,
            YEAR(car.Fecha_Pago) AS anio,
            COUNT(car.Id_Cartera) AS cantidad
        FROM tbl_cartera car
        JOIN tbl_cliente cli ON car.Cedula_Cliente = cli.Cedula_Cliente
        LEFT JOIN tbl_cliente_categoria cl_cat ON cli.Cedula_Cliente = cl_cat.Cedula_Cliente
        LEFT JOIN tbl_categoria cat ON cl_cat.Id_Categoria = cat.Id_Categoria
        WHERE cat.descripcion IS NOT NULL -- Solo incluye trámites con categoría definida
        GROUP BY cat.descripcion, YEAR(car.Fecha_Pago)
        ORDER BY anio DESC, cantidad DESC
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $datasets = [];
    $dataByYearAndType = [];

    // Organiza los datos por año y tipo de trámite
    foreach ($results as $row) {
        $anio = $row['anio'];
        $tipo = $row['tipo_tramite'];
        $cantidad = (int)$row['cantidad'];

        if (!in_array($tipo, $labels)) {
            $labels[] = $tipo; // Los labels serán los tipos de trámite
        }

        if (!isset($dataByYearAndType[$anio])) {
            $dataByYearAndType[$anio] = [];
        }
        $dataByYearAndType[$anio][$tipo] = $cantidad;
    }

    // Ordena los años para asegurar un orden consistente en los datasets
    krsort($dataByYearAndType);

    $colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6c757d', '#17a2b8', '#fd7e14']; // Colores de ejemplo

    $colorIndex = 0;
    foreach ($dataByYearAndType as $anio => $types) {
        $dataForDataset = [];
        foreach ($labels as $label) {
            $dataForDataset[] = $types[$label] ?? 0; // Asegura que si un tipo no tiene datos para un año, sea 0
        }

        $datasets[] = [
            'label' => 'Año ' . $anio,
            'data' => $dataForDataset,
            'backgroundColor' => $colors[$colorIndex % count($colors)], // Cicla los colores
            'borderColor' => $colors[$colorIndex % count($colors)],
            'borderWidth' => 1,
            'stack' => 'stack' . $anio // Para apilar barras si se desea, o quitar para barras agrupadas
        ];
        $colorIndex++;
    }

    $response = [
        'labels' => $labels,
        'datasets' => $datasets
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in obtener_tipo_tramite.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener tipos de trámite.', 'details' => $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error in obtener_tipo_tramite.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error inesperado al obtener tipos de trámite.', 'details' => $e->getMessage()]);
}
?>