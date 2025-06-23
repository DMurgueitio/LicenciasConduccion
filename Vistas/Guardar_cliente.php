<?php
session_start();
require_once "../modelos/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = Conexion::conectar();

        // Recibir campos del formulario
        $cedula = trim($_POST['cedula_cliente']);
        $nombre = trim($_POST['apellidos_nombre']);
        $telefono = trim($_POST['telefono']);
        $escuela = intval($_POST['id_escuela']);
        $categorias = isset($_POST['categorias']) ? array_map('intval', $_POST['categorias']) : [];

        // Validación básica
        if (empty($cedula) || empty($nombre)) {
            throw new Exception("Cédula y nombre son obligatorios.");
        }

        // Verificar si ya existe el cliente
        $stmt_check = $conn->prepare("SELECT cedula_cliente FROM tbl_cliente WHERE cedula_cliente = ?");
        $stmt_check->execute([$cedula]);
        if ($stmt_check->fetch()) {
            throw new Exception("Ya existe un cliente con esta cédula.");
        }

        // Insertar cliente con fecha de registro
        $insert = $conn->prepare("INSERT INTO Tbl_Cliente (cedula_cliente, apellidos_nombre, Telefono, id_escuela, FechaRegistro) VALUES (?, ?, ?, ?, CURRENT_DATE)");
        $insert->execute([$cedula, $nombre, $telefono, $escuela]);

        // Insertar categorías seleccionadas en tabla intermedia
        $stmt_categoria = $conn->prepare("INSERT INTO tbl_cliente_categoria (cedula_cliente, id_categoria) VALUES (?, ?)");
        foreach ($categorias as $categoria_id) {
            $stmt_categoria->execute([$cedula, $categoria_id]);
        }

        // Guardar mensaje de éxito
        $_SESSION['mensaje'] = "Cliente registrado correctamente.";

        // Redirigir a registrar_cartera.php
        header("Location: ../vistas/registro_cartera.php?cedula=" . urlencode($cedula));
        exit();

    } catch (PDOException | Exception $e) {
        $_SESSION['mensaje'] = "Error al registrar cliente: " . $e->getMessage();
        header("Location: ../vistas/error.php");
        exit();
    }
}
?>
