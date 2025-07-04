<?php
session_start();
require_once "../modelos/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = Conexion::conectar();

        // Recibir campos del formulario
        $cedula     = trim($_POST['cedula_cliente']);
        $nombre     = trim($_POST['apellidos_nombre']);
        $telefono   = trim($_POST['telefono']);
        $escuela    = intval($_POST['id_escuela']);
        $categorias = isset($_POST['categorias']) ? array_map('intval', $_POST['categorias']) : [];
        $tramites   = isset($_POST['TipoTramite']) ? array_map('intval', $_POST['TipoTramite']) : [];

        // Validación básica
        if (empty($cedula) || empty($nombre)) {
            throw new Exception("Cédula y nombre son obligatorios.");
        }

        // Verificar si ya existe el cliente
        $stmt_check = $conn->prepare("SELECT cedula_cliente FROM tbl_cliente WHERE cedula_cliente = ?");
        $stmt_check->execute([$cedula]);
        $cliente_existe = $stmt_check->fetch();

        if ($cliente_existe) {
            // ACTUALIZAR datos del cliente
            $update_cliente = $conn->prepare("UPDATE tbl_cliente SET Telefono = ?, id_escuela = ? WHERE cedula_cliente = ?");
            $update_cliente->execute([$telefono, $escuela, $cedula]);

            // ELIMINAR relaciones anteriores
            $delete_relaciones = $conn->prepare("DELETE FROM tbl_cliente_categoria WHERE cedula_cliente = ?");
            $delete_relaciones->execute([$cedula]);
        } else {
            // INSERTAR cliente nuevo
            $insert_cliente = $conn->prepare("INSERT INTO tbl_cliente 
                (cedula_cliente, apellidos_nombre, Telefono, id_escuela, FechaRegistro) 
                VALUES (?, ?, ?, ?, CURRENT_DATE)");
            $insert_cliente->execute([$cedula, $nombre, $telefono, $escuela]);
        }

        // Insertar nuevas categorías y trámites
        if (!empty($categorias) && !empty($tramites)) {
            $stmt_insert = $conn->prepare("INSERT INTO tbl_cliente_categoria 
                (cedula_cliente, id_categoria, FechaAsignacion, tipotramite) 
                VALUES (?, ?, CURRENT_DATE, ?)");
            foreach ($categorias as $categoria_id) {
                foreach ($tramites as $tramite_id) {
                    $stmt_insert->execute([$cedula, $categoria_id, $tramite_id]);
                }
            }
        }

        // Mensaje de éxito
        $_SESSION['mensaje'] = $cliente_existe ? "Cliente actualizado correctamente." : "Cliente registrado correctamente.";

        // Redirección por POST con formulario oculto
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Redirigiendo...</title>
        </head>
        <body>
            <form id="redirigir" action="../vistas/registro_cartera.php" method="post">
                <input type="hidden" name="cedula_cliente" value="' . htmlspecialchars($cedula) . '">
            </form>
            <script>
                document.getElementById("redirigir").submit();
            </script>
        </body>
        </html>';
        exit();

    } catch (PDOException | Exception $e) {
        $_SESSION['mensaje'] = "Error al procesar cliente: " . $e->getMessage();
        header("Location: ../vistas/error.php");
        exit();
    }
}
?>
