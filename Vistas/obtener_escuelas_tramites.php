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

    // Consulta para obtener el número de trámites por escuela
    // Ajusta las tablas y columnas según tu esquema real si es diferente.
    // Asumo que un "trámite" es una entrada en tbl_cartera relacionada con un cliente y su escuela.
    $stmt = $conn->prepare("
        SELECT
            esc.descri_escuela AS escuela_nombre,
            COUNT(car.Id_Cartera) AS total_tramites
        FROM tbl_cartera car
        JOIN tbl_cliente cli ON car.Cedula_Cliente = cli.Cedula_Cliente
        JOIN tbl_escuela esc ON cli.id_Escuela = esc.id_escuela
        GROUP BY esc.descri_escuela
        ORDER BY total_tramites DESC
        LIMIT 5 -- Limita a las top 5 escuelas
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $tramites = [];

    foreach ($data as $row) {
        $labels[] = $row['escuela_nombre'];
        $tramites[] = (int)$row['total_tramites'];
    }

    $response = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Total Trámites',
                'data' => $tramites,
                'backgroundColor' => ['#4BC0C0', '#FFCD56', '#FF6384', '#9966FF', '#C9CBCF'], // Colores para las barras
                'borderColor' => ['#4BC0C0', '#FFCD56', '#FF6384', '#9966FF', '#C9CBCF'],
                'borderWidth' => 1
            ]
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error in obtener_escuelas_tramites.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener datos de escuelas.', 'details' => $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error in obtener_escuelas_tramites.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error inesperado al obtener datos de escuelas.', 'details' => $e->getMessage()]);
}
?>