// guardar_pago.php
<?php
$conexion = new mysqli("localhost", "root", "", "reconocimiento_infractores");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula_cliente'];
    $orden = $_POST['nro_orden'];
    $fecha = $_POST['fecha_pago'];
    $valor = $_POST['valor_pago'];
    $medio_pago = $_POST['medio_pago'];
    $banco = $_POST['banco'] ?: null;
    $comision = $_POST['comision'] ?: 0;
    $obs = $_POST['observaciones'];

    $stmt = $conexion->prepare("INSERT INTO tbl_cartera 
        (cedula_cliente, nro_orden, fecha_pago, valor_pago, id_medio_pago, id_banco, comision, observaciones) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdiiid", $cedula, $orden, $fecha, $valor, $medio_pago, $banco, $comision, $obs);
    $stmt->execute();
    echo "Pago registrado";
}
?>