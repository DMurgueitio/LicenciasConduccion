<?php
session_start();
require_once "../modelos/conexion.php";

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['ID_Usuario'])) {
    header("Location: login.php");
    exit();
}

$conn = Conexion::conectar();
$idUsuario = $_SESSION['ID_Usuario'];

// Consultar si ya tiene pregunta y respuesta registradas
$stmt = $conn->prepare("SELECT pregunta_seguridad, respuesta_seguridad FROM usuario_diligencia WHERE ID_Usuario = ?");
$stmt->execute([$idUsuario]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Seguridad</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="form-container">
        <form method="POST" action="guardar_seguridad.php">
            <h2>Configurar pregunta de seguridad</h2>

            <label for="pregunta">Pregunta de seguridad:</label>
            <select name="pregunta" required>
                <option value="">Selecciona una pregunta</option>
                <option value="¿Cuál es el nombre de tu primera mascota?" <?= ($datos && $datos['pregunta_seguridad'] === '¿Cuál es el nombre de tu primera mascota?') ? 'selected' : '' ?>>¿Cuál es el nombre de tu primera mascota?</option>
                <option value="¿En qué ciudad naciste?" <?= ($datos && $datos['pregunta_seguridad'] === '¿En qué ciudad naciste?') ? 'selected' : '' ?>>¿En qué ciudad naciste?</option>
                <option value="¿Cuál es tu comida favorita?" <?= ($datos && $datos['pregunta_seguridad'] === '¿Cuál es tu comida favorita?') ? 'selected' : '' ?>>¿Cuál es tu comida favorita?</option>
            </select>

            <label for="respuesta">Respuesta:</label>
            <input type="text" name="respuesta" id="respuesta" value="" required>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>
