<?php
session_start();
// Aquí puedes validar sesión si lo deseas
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bienvenido - CRC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../Css/Bienvenida.css">
</head>

<body>

    <div class="hero">
        <div class="logo">
            <a href="#" class="logo-container">
                <img src="../Css/image/iconos/logo.png" alt="ConduExam Logo" class="logo-animado">
            </a>
        </div>
        <h1>¡Bienvenido !</h1>
        <p>Tu plataforma integral para la gestión de usuarios, trámites y reportes en el Centro de Reconocimiento de Conductores.</p>
        <button class="btn-welcome" onclick="window.location.href='dashboard.php'">
            <i class="fas fa-arrow-right"></i> Ir al sistema
        </button>
    </div>

</body>

</html>