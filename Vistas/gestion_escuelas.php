<?php
session_start();

// Verificar sesión activa
if (!isset($_SESSION['ID_Usuario'])) {
    header("Location: ../login.php");
    exit("No tienes sesión activa.");
}

// Verificar que NivelRol exista
if (!isset($_SESSION['NivelRol'])) {
    header("Location: ../login.php");
    exit("Tu rol no está definido.");
}

// Solo permite acceso a administradores (NivelRol = 1)
if ($_SESSION['NivelRol'] != 1) {
    header("Location: ../login.php");
    exit("No tienes permisos suficientes.");
}

// Conectar a la base de datos
require_once '../modelos/conexion.php';
$conn = Conexion::conectar();

try {
    $stmt = $conn->query("SELECT * FROM tbl_escuela");
    $escuelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar escuelas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Escuelas - CRC</title>
    <link rel="stylesheet" href="../css/registrar_cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">       
</head>
<body>

<div class="form-container">
    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-school"></i>
            <h2>Gestión de Escuelas de Conducción</h2>
            <p>Crea, edita o elimina escuelas del sistema</p>
        </div>

        <!-- Formulario para nueva escuela -->
        <form id="form-registro" method="POST" action="guardar_escuela.php">
            <div class="input-group">
                <label for="descri_escuela">Nombre de la Escuela</label>
                <input type="text" name="descri_escuela" id="descri_escuela" required>
            </div>
            <button type="submit" class="btn-register">
                <i class="fas fa-plus-circle"></i> Registrar Escuela
            </button>
        </form>

        <!-- Mensaje Toast -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="toast toast-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['mensaje']) ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <!-- Tabla de escuelas -->
        <div class="table-section">
            <h3>Listado de Escuelas</h3>
            <table class="data-table" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-escuelas">
                    <!-- Las escuelas se cargan aquí con JS -->
                    <?php foreach ($escuelas as $escuela): ?>
                        <tr>
                            <td><?= htmlspecialchars($escuela['id_escuela']) ?></td>
                            <td><?= htmlspecialchars($escuela['descri_escuela']) ?></td>
                            <td>
                                <a href="editar_escuela.php?id=<?= $escuela['id_escuela'] ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</a>
                                <a href="eliminar_escuela.php?id=<?= $escuela['id_escuela'] ?>" onclick="return confirm('¿Eliminar esta escuela?')" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-footer">
            <a href="../dashboard.php" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>

<script>
document.getElementById("form-registro").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("guardar_escuela.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '✅ Registrado',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Limpiar formulario
            document.getElementById("descri_escuela").value = "";

            // Añadir fila nueva a la tabla
            const tabla = document.getElementById("tabla-escuelas");
            const nuevaFila = document.createElement("tr");
            nuevaFila.innerHTML = `
                <td>${data.id_escuela}</td>
                <td>${formData.get("descri_escuela")}</td>
                <td>
                    <a href="editar_escuela.php?id=${data.id_escuela}" class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</a>
                    <a href="eliminar_escuela.php?id=${data.id_escuela}" onclick="return confirm('¿Eliminar esta escuela?')" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i> Eliminar</a>
                </td>
            `;
            tabla.appendChild(nuevaFila);

        } else {
            Swal.fire({
                icon: 'error',
                title: '❌ Error',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(err => {
        console.error(err);
        alert("Hubo un error al enviar los datos.");
    });
});
</script>

</body>
</html>