<!-- registrar_pago.php -->
<form method="post" action="guardar_pago.php">
    <input type="text" name="cedula_cliente" placeholder="Cédula cliente" required>
    <input type="text" name="nro_orden" placeholder="Número de orden">
    <input type="date" name="fecha_pago" required>
    <input type="number" step="0.01" name="valor_pago" placeholder="Valor pago" required>

    <select name="medio_pago">
        <?php
        $pagos = $conexion->query("SELECT * FROM tbl_medio_pago");
        while ($row = $pagos->fetch_assoc()) {
            echo "<option value='{$row['id_medio_pago']}'>{$row['desc_medio_pago']}</option>";
        }
        ?>
    </select>

    <select name="banco">
        <option value="">Seleccionar Banco (si aplica)</option>
        <?php
        $bancos = $conexion->query("SELECT * FROM tbl_banco");
        while ($row = $bancos->fetch_assoc()) {
            echo "<option value='{$row['id_banco']}'>{$row['desc_banco']}</option>";
        }
        ?>
    </select>

    <input type="number" step="0.01" name="comision" placeholder="Comisión (opcional)">
    <textarea name="observaciones"></textarea>
    <button type="submit">Registrar Pago</button>
</form>