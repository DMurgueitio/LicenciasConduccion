<?php
session_start();
if (!isset($_SESSION['mensaje'])) {
    header("Location: ../dashboard.php");
    exit();
}
$mensaje = htmlspecialchars($_SESSION['mensaje']);
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error - CRC</title>
    <style>
        body {
            background: linear-gradient(to right, #0f0f0f, #1c1c1c);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        .alert-error {
            background-color: #2c2c2c;
            border-left: 6px solid #dc3545;
            padding: 20px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            animation: fadeInUp 0.5s ease-in-out;
        }

        .alert-error i {
            font-size: 48px;
            color: #ff4d4d;
            margin-bottom: 10px;
            display: block;
        }

        .alert-error h2 {
            font-size: 22px;
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        .alert-error p {
            font-size: 15px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .btn-volver {
            display: inline-block;
            background-color: #5FDAF5;
            color: black;
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-volver:hover {
            background-color: #735FF5;
            color: white;
            transform: scale(1.02);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <h2>❌ Error en Registro</h2>
        <p><?= $mensaje ?></p>
        <a href="javascript:history.back()" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</body>
</html>