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

    // Asegura que la fecha esté correctamente formateada
    $today = (new DateTime())->format('Y-m-d');

    $stmt = $conn->prepare("
        SELECT
            cli.Cedula_Cliente,
            cli.Apellidos_Nombre,
            cli.Telefono,
            COALESCE(cat.descripcion, 'N/A') AS Categoria,
            COALESCE(esc.descri_escuela, 'N/A') AS Escuela
        FROM tbl_cartera car
        JOIN tbl_cliente cli ON car.Cedula_Cliente = cli.Cedula_Cliente
        LEFT JOIN tbl_cliente_categoria cl_cat ON cli.Cedula_Cliente = cl_cat.Cedula_Cliente
        LEFT JOIN tbl_categoria cat ON cl_cat.Id_Categoria = cat.Id_Categoria
        LEFT JOIN tbl_escuela esc ON cli.id_Escuela = esc.id_escuela
        WHERE DATE(car.Fecha_Pago) = :fecha
        GROUP BY cli.Cedula_Cliente, cli.Apellidos_Nombre, cli.Telefono, Categoria, Escuela
        ORDER BY cli.Apellidos_Nombre
    ");

    $stmt->bindValue(':fecha', $today);
    $stmt->execute();

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clientes);

} catch (PDOException $e) {
    error_log("Database error in obtener_clientes_del_dia.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los clientes del día.',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in obtener_clientes_del_dia.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Ocurrió un error inesperado al obtener clientes.',
        'details' => $e->getMessage()
    ]);
}
?>
