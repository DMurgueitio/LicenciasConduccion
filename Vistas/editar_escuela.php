<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelRol'] != 1) {
    header("Location: ../login.php");
    exit();
}

require_once '../modelos/conexion.php';
$conn = Conexion::conectar();

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM tbl_escuela WHERE id_escuela = ?");
$stmt->execute([$id]);
$escuela = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$escuela) {
    $_SESSION['mensaje'] = "Escuela no encontrada.";
    header("Location: gestion_escuelas.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Escuela - CRC</title>
    <link rel="stylesheet" href="../css/registrar_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">    
</head>
<body>

<div class="form-container">
    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-edit"></i>
            <h2>Editar Escuela</h2>
            <p>Modifica los datos de una escuela</p>
        </div>

        <form method="POST" action="actualizar_escuela.php">
            <input type="hidden" name="id_escuela" value="<?= $escuela['id_escuela'] ?>">
            <div class="input-group">
                <label for="descri_escuela">Nombre de la Escuela</label>
                <input type="text" name="descri_escuela" id="descri_escuela" value="<?= htmlspecialchars($escuela['descri_escuela']) ?>" required>
            </div>
            <button type="submit" class="btn-register">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>
</div>

</body>
</html>