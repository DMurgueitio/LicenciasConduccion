<?php
require 'includes/auth.php';
if ($_SESSION['rol'] !== 'Administrador') {
    echo "Acceso denegado.";
    exit;
}
require 'includes/db.php';

$resultado = $conn->query("SELECT a.id, u.nombre, a.accion, a.fecha 
                           FROM auditoria a 
                           JOIN usuarios u ON a.usuario_id = u.id 
                           ORDER BY a.fecha DESC");

echo "<h2>Auditoría del sistema</h2><table border='1'><tr><th>Usuario</th><th>Acción</th><th>Fecha</th></tr>";
while ($row = $resultado->fetch_assoc()) {
    echo "<tr><td>{$row['nombre']}</td><td>{$row['accion']}</td><td>{$row['fecha']}</td></tr>";
}
echo "</table>";

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$resultado = $conn->query("SELECT * FROM auditoria ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Auditoría del Sistema</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Registro de Auditoría</h2>
        <table border="1" cellpadding="5" width="100%">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Fecha y Hora</th>
            </tr>
            <?php while ($log = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td><?= $log['usuario'] ?></td>
                    <td><?= $log['accion'] ?></td>
                    <td><?= $log['fecha'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p><a href="dashboard.php">Volver al menú</a></p>
    </div>
</body>
</html>
