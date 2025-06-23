<?php
ob_start();
session_start();

require_once "../modelos/conexion.php";

$conn = Conexion::conectar();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = "";
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Registrar Usuario - CRC</title>
  <link rel="stylesheet" href="../css/registrar_usuario.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="register-container">
    <div class="form-card">
      <div class="card-header">
        <i class="fas fa-user-plus icon-header"></i>
        <h2>Registro de Usuario</h2>
        <p>Crea una nueva cuenta de acceso al sistema</p>
      </div>

      <!-- Alerta visible solo si hay error o éxito -->
      <div id="mensaje" class="alert"></div>

      <form id="registroForm" method="POST">
        <div class="input-group">
          <i class="fas fa-id-badge input-icon"></i>
          <input type="text" name="id_usuario" placeholder="Cédula de usuario" required>
        </div>

        <div class="input-group">
          <i class="fas fa-user input-icon"></i>
          <input type="text" name="Nombre_usuario" placeholder="Nombre completo" required>
        </div>

        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" name="Email" placeholder="Correo electrónico" required>
        </div>

        <div class="input-group position-relative">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
          <span class="toggle-password" onclick="togglePassword()">
            <i class="fas fa-eye" id="toggleIcon"></i>
          </span>
        </div>

        <div class="input-group position-relative">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirmar contraseña" required>
          <span class="toggle-password" onclick="toggleConfirmPassword()">
            <i class="fas fa-eye" id="toggleConfirmIcon"></i>
          </span>
        </div>

        <input type="hidden" name="id_rol" value="2">

        <button type="submit" class="btn-register">
          <i class="fas fa-save"></i> Registrar Usuario
        </button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pass = document.getElementById("contrasena");
      const icon = document.getElementById("toggleIcon");
      if (pass.type === "password") {
        pass.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        pass.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }

    function toggleConfirmPassword() {
      const pass = document.getElementById("confirmar_contrasena");
      const icon = document.getElementById("toggleConfirmIcon");
      if (pass.type === "password") {
        pass.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        pass.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }

    document.getElementById("registroForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      const contrasena = formData.get("contrasena");
      const confirmar = formData.get("confirmar_contrasena");

      if (contrasena !== confirmar) {
        mostrarMensaje("Las contraseñas no coinciden. Por favor, verifica.", true);
        return;
      }

      try {
        const response = await fetch("guardar_usuario.php", {
          method: "POST",
          body: formData
        });

        const result = await response.json();
        mostrarMensaje(result.message, !result.success);

        if (result.success) {
          setTimeout(() => {
            window.location.href = "login.php";
          }, 1500);
        }

      } catch (error) {
        mostrarMensaje("Error en el servidor. Intenta de nuevo.", true);
      }
    });

    function mostrarMensaje(mensaje, esError = false) {
      const div = document.getElementById("mensaje");
      div.textContent = mensaje;
      div.classList.remove("alert-danger");
      if (esError) {
        div.classList.add("alert-danger");
      }
      div.style.display = "flex";
      div.style.opacity = 1;
    }
  </script>
</body>

</html>
