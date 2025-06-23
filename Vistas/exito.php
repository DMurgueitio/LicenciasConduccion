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
    <title>Éxito - CRC</title>
    <style>
        body {
            background: linear-gradient(to right, #0072BC, #1c2b8c);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }

        .alert-success {
            background-color: #2c2c2c;
            border-left: 6px solid #5FDAF5;
            padding: 20px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            animation: fadeInUp 0.5s ease-in-out;
        }

        .alert-success i {
            font-size: 48px;
            color: #5FDAF5;
            margin-bottom: 10px;
            display: block;
        }

        .alert-success h2 {
            font-size: 22px;
            color: #5FDAF5;
            margin-bottom: 10px;
        }

        .alert-success p {
            font-size: 15px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .btn-regresar {
            display: inline-block;
            background-color: #735FF5;
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-regresar:hover {
            background-color: #B1D3F5;
            color: black;
            transform: scale(1.03);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i>
        <h2>✅ ¡Éxito!</h2>
        <p><?= $mensaje ?></p>
        <a href="registrar_cartera.php" class="btn-regresar"><i class="fas fa-money-check-alt"></i> Registrar otro pago</a>
    </div>
</body>
</html>