<?php
ob_start();
session_start();

require_once "../modelos/conexion.php";

$conn = Conexion::conectar();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = trim($_POST['ID_Usuario']);
    $contrasena = trim($_POST['contrasena']);

    $stmt = $conn->prepare("SELECT u.ID_Usuario, u.Nombre_usuario, u.email, u.contrasena, u.NivelRol, r.id_NivelRol 
                            FROM tbl_usuario u
                            JOIN dbo_Rol r ON u.NivelRol = r.id_NivelRol
                            WHERE u.ID_Usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['ID_Usuario'] = $usuario['ID_Usuario'];
        $_SESSION['NivelRol'] = $usuario['NivelRol'];
        $_SESSION['NombreUsuario'] = $usuario['Nombre_usuario'];
        $_SESSION['EmailUsuario'] = $usuario['email'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Cédula o contraseña incorrecta.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - CRC</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <img src="../Css/image/iconos/logo.png" alt="ConduExam Logo" class="logo-animado">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <!--img src="../Css/image/iconos/logo.png" alt="Logo Conduexam" -->
                    <h2>Conduexam S.A.S</h2>
                    <p>Inicia sesión con tus credenciales</p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="login-form">
                <div class="input-group">
                    <i class="fas fa-id-badge input-icon"></i>
                    <input type="text" id="ID_Usuario" name="ID_Usuario" placeholder="Cédula de identidad" required>
                </div>

                <div class="input-group position-relative">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <div class="options-row">
                    <a href="recuperar.php" class="forgot-password">
                        <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
                    </a>
                    <a href="registrar_usuario.php" class="btn-register">
                        <i class="fas fa-user-plus"></i> Registrar Usuario
                    </a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("contrasena");
            const icon = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
<?php ob_end_flush(); ?>