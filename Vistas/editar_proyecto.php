<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelUsuario'] !== 'Administrador') {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['ID_PPP'])) {
    echo "No se recibió el ID del proyecto.";
    exit();
}

$idPPP = $_POST['ID_PPP'];

require_once "../modelos/conexion.php";

try {
    $pdo = Conexion::conectar();

    // Consulta básica para obtener datos del proyecto
    $stmt = $pdo->prepare("SELECT * FROM proceso_proyecto_producto WHERE ID_PPP = ?");
    $stmt->execute([$idPPP]);
    $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {
        echo "Proyecto no encontrado.";
        exit();
    }

} catch (PDOException $e) {
    die("Error al cargar los detalles del proyecto: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="container">
    <h2>Editar Proyecto</h2>

    <form action="guardar_edicion.php" method="POST">
        <input type="hidden" name="ID_PPP" value="<?= htmlspecialchars($proyecto['ID_PPP']) ?>">

        <label for="Nombre_PPP">Nombre del Proyecto:</label>
        <input type="text" name="Nombre_PPP" value="<?= htmlspecialchars($proyecto['Nombre_PPP']) ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion"><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>

        <label for="fecha_Inicio">Fecha Inicio:</label>
        <input type="date" name="fecha_Inicio" value="<?= htmlspecialchars($proyecto['fecha_Inicio']) ?>">

        <label for="fecha_Fin">Fecha Fin:</label>
        <input type="date" name="fecha_Fin" value="<?= htmlspecialchars($proyecto['fecha_Fin']) ?>">

        <div class="form-group">
    <label for="DirectorProyecto">Director del Proyecto</label>
    <div class="input-group">
        <select id="DirectoProyecto" class="form-control form-control-sm"
                onchange="autocompletarCampos('DirectorProyecto', 'cargo_director', 'unidad_director')">
            <option value="">Seleccione...</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id_usuario'] ?>"
                    data-cargo="<?= htmlspecialchars($u['cargo']) ?>"
                    data-unidad="<?= htmlspecialchars($u['Unidad']) ?>">
                    <?= htmlspecialchars($u['Nombres_apellidos']) ?> - <?= htmlspecialchars($u['NivelUsuario']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <span class="input-group-text input-group-label">Cargo</span>
        <input type="text" id="cargo_director" class="form-control form-control-sm" readonly>

        <span class="input-group-text input-group-label">Unidad</span>
        <input type="text" id="unidad_director" class="form-control form-control-sm" readonly>
    </div>
</div>
 <!-- Investigador principal -->
            <div class="form-row-horizontal">
                <div class="campo-horizontal">
                    <label for="ID_Usuario">Investigador Principal:</label>
                    <select name="ID_Usuario" id="ID_Usuario" required>
                        <option value="">Seleccione un investigador</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['ID_Usuario'] ?>">
                                <?= htmlspecialchars($usuario['Nombres_apellidos']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo-horizontal">
                    <label>Cargo:</label>
                    <input type="text" id="cargo_autocomplete" name="cargo_autocomplete" readonly>
                </div>

                <div class="campo-horizontal">
                    <label>Unidad:</label>
                    <input type="text" id="unidad_autocomplete" name="unidad_autocomplete" readonly>
                </div>
            </div>

        <!-- Más campos según tus necesidades -->

        <button type="submit" class="btn">Guardar Cambios</button>
    </form>

    <br>
    <a href="modificar_proyecto.php" class="btn">Volver</a>
</div>
<script>
 function obtenerDatoDirector(idDirector, campo) {
            return directores.find(d => d.DirectorProyecto === idDirector)?.[campo] || '';
        }


        function obtenerDatoUsuario(idUsuario, campo) {
            return usuarios.find(u => u.ID_Usuario === idUsuario)?.[campo] || '';
        }

        function obtenerDatoUnidad(idUsuario) {
            const usuario = usuarios.find(u => u.ID_Usuario === idUsuario);
            return usuario ? usuario.unidad : '';
        }
        document.getElementById("DirectorProyecto").addEventListener("change", function() {
            const idDirector = this.value;
            if (!idDirector) return;

            const cargo = obtenerDatoDirector(idDirector, 'cargo');
            const unidad = obtenerDatoDirector(idDirector, 'Unidad');

            document.getElementById('ACargo').value = cargo;
            document.getElementById('UnidadDir').value = unidad;
        });


        document.getElementById("ID_Usuario").addEventListener("change", function() {
            const idUsuario = this.value;
            if (!idUsuario) return;

            const cargo = obtenerDatoUsuario(idUsuario, 'cargo');
            const unidad = obtenerDatoUnidad(idUsuario);

            document.querySelector('#cargo_autocomplete').value = cargo;
            document.querySelector('#unidad_autocomplete').value = unidad;
        });


</script>
       

</body>
</html>