<?php
session_start();

// Si el usuario ya está autenticado, redirige al panel principal
if (isset($_SESSION['ID_Usuario'])) {
    header("Location: Vistas/dashboard.php");
    exit();
}

// Si no ha iniciado sesión, redirige al login
header("Location: vistas/bienvenida.php");
exit();
?>

