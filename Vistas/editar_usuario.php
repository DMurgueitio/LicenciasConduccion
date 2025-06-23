<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$id = $_GET['id'];
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit();
}

$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar Usuario</h2>
        <form action="procesar_editar_usuario.php" method="POST">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            <input type="text" name="usuario" value="<?= $usuario['usuario'] ?>" required>
            <input type="email" name="correo" value="<?= $usuario['correo'] ?>" required>
            <select name="rol" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="docente" <?= $usuario['rol'] === 'docente' ? 'selected' : '' ?>>Docente</option>
                <option value="estudiante" <?= $usuario['rol'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
            </select>
            <input type="submit" value="Actualizar">
        </form>
        <p><a href="crud_usuarios.php">Volver</a></p>
    </div>
</body>
</html>
