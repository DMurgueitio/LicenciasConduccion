<?php
session_start();
header('Content-Type: application/json');

require_once "../modelos/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    $conn = Conexion::conectar();

    $cedula_cliente = trim($_POST['cedula_cliente']);
    $nro_orden = trim($_POST['nro_orden']);
    $fecha_pago = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d');
    $valor_pago = floatval($_POST['valor_pago']);
    $id_medio_pago = intval($_POST['id_medio_pago']);
    $id_banco = isset($_POST['id_banco']) && $_POST['id_banco'] !== '' ? intval($_POST['id_banco']) : null;
    $comision = floatval($_POST['comision']);
    $observaciones = trim($_POST['observaciones']);

    if (empty($cedula_cliente) || empty($nro_orden) || empty($fecha_pago)) {
        throw new Exception("Faltan campos obligatorios.");
    }

    $stmt = $conn->prepare("INSERT INTO tbl_cartera 
        (cedula_cliente, nro_orden, fecha_pago, valor_pago, id_medio_pago, id_banco, comision, observaciones) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $cedula_cliente,
        $nro_orden,
        $fecha_pago,
        $valor_pago,
        $id_medio_pago,
        $id_banco,
        $comision,
        $observaciones
    ]);

    echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>