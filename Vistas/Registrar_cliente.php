<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Cliente - CRC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/registrar_cliente.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="form-container">
        <div class="form-card">
            <div class="card-header">
                <i class="fas fa-user-plus icon-header"></i>
                <h2>Registro de Cliente</h2>
                <p>Crea una nueva cuenta de acceso al sistema</p>
            </div>

            <form method="post" action="guardar_cliente.php">
                <div class="form-grid">
                    <div class="input-group">
                        <label for="cedula_cliente">Cédula Cliente</label>
                        <i class="fas fa-id-badge input-icon"></i>
                        <input type="text" id="cedula_cliente" name="cedula_cliente" placeholder="Ej: 1010101010" required>
                    </div>

                    <div class="input-group">
                        <label for="apellidos_nombre">Nombres y Apellidos</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="apellidos_nombre" name="apellidos_nombre" placeholder="Ej: Juan Pérez" required onblur="this.value = this.value.toUpperCase();">
                    </div>


                    <div class="input-group">
                        <label for="telefono">Teléfono</label>
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" id="telefono" name="telefono" placeholder="Ej: 3101234567">
                    </div>

                    <div class="input-group">
                        <label for="id_escuela">Escuela de Conducción</label>
                        <i class="fas fa-school input-icon"></i>
                        <select id="id_escuela" name="id_escuela" required>
                            <option value="">Selecciona escuela</option>
                            <?php
                            require_once "../modelos/conexion.php";
                            $conn = Conexion::conectar();
                            $stmt = $conn->query("SELECT id_escuela, descri_escuela FROM tbl_escuela");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id_escuela']}'>{$row['descri_escuela']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Categorías de Licencia</label>
                        <i class="fas fa-car input-icon"></i>
                        <div class="chip-container" id="categorias-chip">
                            <?php
                            $stmt = $conn->query("SELECT id_categoria, descripcion FROM tbl_categoria ORDER BY descripcion ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "
                                <div class='chip' data-value='{$row['id_categoria']}' onclick=\"toggleCategoria(this)\">
                                    {$row['descripcion']}       
                                    <input type='checkbox' name='categorias[]' value='{$row['id_categoria']}' class='chip-input'>
                                </div>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Tipo de Tramite</label>
                        <i class="fas fa-car input-icon"></i>
                        <div class="chip-container" id="tipotramite-chip"> <?php
                            $stmt = $conn->query("SELECT Id_TipoTramite, Tramite FROM tbl_TipoTramite ORDER BY Tramite ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "
                                <div class='chip' data-value='{$row['Id_TipoTramite']}' onclick=\"toggleTramite(this)\">
                                    {$row['Tramite']}       
                                    <input type='checkbox' name='TipoTramite[]' value='{$row['Id_TipoTramite']}' class='chip-input'>
                                </div>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn-register">
                            <i class="fas fa-save"></i> Registrar Cliente
                        </button>

                        <?php if (isset($_SESSION['mensaje'])): ?>
                            <div class="toast toast-success">
                                <i class="fas fa-check-circle"></i>
                                <?= htmlspecialchars($_SESSION['mensaje']) ?>
                            </div>
                            <?php unset($_SESSION['mensaje']); ?>
                        <?php endif; ?>
                    </div>
                </div> </form>
        </div>
    </div>
    <script>
        function toggleCategoria(element) {
            element.classList.toggle("selected");
            const checkbox = element.querySelector(".chip-input");
            checkbox.checked = element.classList.contains("selected");
        }

        function toggleTramite(element) {
            element.classList.toggle("selected");
            const checkbox = element.querySelector(".chip-input");
            checkbox.checked = element.classList.contains("selected");
        }
    </script>
</body>

</html>