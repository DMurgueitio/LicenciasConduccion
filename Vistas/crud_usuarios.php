<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$resultado = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Usuarios del Sistema</h2>
        <table border="1" cellpadding="5" cellspacing="0" width="100%">
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id'] ?></td>
                    <td><?= $fila['usuario'] ?></td>
                    <td><?= $fila['correo'] ?></td>
                    <td><?= $fila['rol'] ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $fila['id'] ?>">Editar</a> |
                        <a href="eliminar_usuario.php?id=<?= $fila['id'] ?>" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p><a href="dashboard.php">Volver al menú</a></p>
    </div>
</body>
</html>
