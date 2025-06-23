<?php
session_start();

if (!isset($_SESSION['ID_Usuario']) || $_SESSION['NivelUsuario'] !== 'Administrador') {
    header("Location: ../login.php");
    exit();
}

require_once "../modelos/conexion.php";

try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->query("SELECT u.ID_Usuario, u.nombre_usuario, r.NivelUsuario 
                              FROM tbl_usuario u
                              JOIN dbo_rol r ON u.id_rol = r.id_nivel_rol
                              ORDER BY u.ID_Usuario DESC");

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener usuarios: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - CRC</title>
    <link rel="stylesheet" href="../css/usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo">CRC Admin</div>
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="usuarios.php" class="active"><i class="fas fa-users-cog"></i> Gestión de Usuarios</a></li>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1><i class="fas fa-users-cog"></i> Gestión de Usuarios</h1>
            <a href="#" id="btnAgregar" class="btn btn-primary"><i class="fas fa-user-plus"></i> Agregar Usuario</a>
        </header>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['ID_Usuario']) ?></td>
                    <td><?= htmlspecialchars($user['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($user['NivelUsuario']) ?></td>
                    <td>
                        <button class="btn btn-view" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-edit" data-id="<?= $user['ID_Usuario'] ?>" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-delete" data-id="<?= $user['ID_Usuario'] ?>" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Usuario</h3>
                <span class="close" id="cerrarModal">&times;</span>
            </div>
            <form action="editar_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" id="editId">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nombre de usuario</label>
                    <input type="text" name="nombre_usuario" id="editNombre" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-briefcase"></i> Rol</label>
                    <select name="id_rol" id="editRol" required>
                        <option value="1">Administrador</option>
                        <option value="2">Empleado</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../Js/usuarios.js"></script>
</body>
</html>