<?php
session_start();
header('Content-Type: application/json');

require_once "../modelos/conexion.php";

// Para debugging: guarda en un archivo los datos recibidos
file_put_contents('debug_guardar_cartera.txt', print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    // Conectar a la base de datos
    $conn = Conexion::conectar();

    // Recibir campos
    $cedula_cliente = trim($_POST['cedula_cliente'] ?? '');
    $nro_orden = trim($_POST['NroOrden'] ?? '');
    $fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d');
    $valor_pago = floatval(str_replace(['$', ','], '', $_POST['valor_pago'] ?? 0));
    $id_medio_pago = intval($_POST['id_medio_pago'] ?? 0);
    $id_banco = !empty($_POST['id_Banco']) ? intval($_POST['id_Banco']) : null;
    $comision = floatval(str_replace(['$', ','], '', $_POST['comision'] ?? 0));
    $observaciones = trim($_POST['observaciones'] ?? '');

    // Validación de campos obligatorios
    if (empty($cedula_cliente) || empty($nro_orden) || empty($fecha_pago)) {
        throw new Exception("Faltan campos obligatorios.");
    }

    // Verificar si el cliente existe
    $stmt_check = $conn->prepare("SELECT cedula_cliente FROM tbl_cliente WHERE cedula_cliente = ?");
    $stmt_check->execute([$cedula_cliente]);

    if (!$stmt_check->fetch()) {
        throw new Exception("El cliente no está registrado.");
    }

    // Insertar cartera
    $stmt = $conn->prepare("INSERT INTO tbl_cartera 
        (cedula_cliente, NroOrden, fecha_pago, valor_pago, id_medio_pago, id_Banco, comision, observaciones) 
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
    error_log("Error SQL: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}