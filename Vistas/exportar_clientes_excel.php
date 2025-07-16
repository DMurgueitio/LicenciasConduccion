<?php
require_once "../modelos/conexion.php";
$conn = Conexion::conectar();

$today = date('Y-m-d');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=clientes_del_dia_' . $today . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, [
    'Nro Orden',
    'Fecha Pago',
    'Cédula',
    'Nombre',
    'Teléfono',
    'Medio de Pago',
    'Valor Pago',
    'Comisión',
    'Observaciones',
    'Categoría',
    'Tipo Trámite'
]);

try {
    $stmt = $conn->prepare("
        SELECT 
            car.NroOrden,
            car.Fecha_Pago,
            cli.Cedula_Cliente,
            cli.apellidos_nombre,
            cli.Telefono,
            mp.desc_Medio_Pago AS medio_pago_desc,
            car.Valor_Pago,
            car.Comision,
            car.Observaciones,
            cat.descripcion AS categoria,
            tt.Tramite AS tipo_tramite
        FROM tbl_cartera car
        JOIN tbl_cliente cli ON car.Cedula_Cliente = cli.Cedula_Cliente
        LEFT JOIN tbl_cliente_categoria cc ON cli.Cedula_Cliente = cc.Cedula_Cliente
        LEFT JOIN tbl_categoria cat ON cc.Id_Categoria = cat.Id_Categoria
        LEFT JOIN tbl_tipotramite tt ON cc.TipoTramite = tt.Id_TipoTramite
        LEFT JOIN tbl_medio_pago mp ON car.Id_Medio_Pago = mp.id_Medio_Pago
        WHERE DATE(car.Fecha_Pago) = ?
        ORDER BY car.Fecha_Pago DESC
    ");
    
    $stmt->execute([$today]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($registros as $row) {
        fputcsv($output, [
            $row['NroOrden'] ?: '',
            $row['Fecha_Pago'],
            $row['Cedula_Cliente'],
            $row['apellidos_nombre'],
            $row['Telefono'] ?: 'Sin teléfono',
            $row['medio_pago_desc'] ?: 'Sin medio de pago',
            number_format($row['Valor_Pago'], 2, ',', '.'), // Formato moneda
            number_format($row['Comision'], 2, ',', '.'),   // Formato moneda
            $row['Observaciones'] ?: '',
            $row['categoria'] ?: 'Sin categoría',
            $row['tipo_tramite'] ?: 'Sin tipo de trámite'
        ]);
    }

    fclose($output);
} catch (PDOException $e) {
    die("Error al exportar: " . $e->getMessage());
}
?>