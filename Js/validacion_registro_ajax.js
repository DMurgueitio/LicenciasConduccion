document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registroForm");
    const pass = document.getElementById("contrasena");
    const confirmPass = document.getElementById("confirmar_contrasena");
    const modal = document.getElementById("modalError");
    const mensajeDiv = document.getElementById("mensaje");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (pass.value !== confirmPass.value) {
            modal.style.display = "block";
            return;
        }

        // Enviar formulario con AJAX
        const formData = new FormData(form);

        fetch("guardar_usuario.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje(data.message, 'success');
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 2000);
            } else {
                mostrarMensaje(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error("Error:", error);
            mostrarMensaje("Ocurrió un error inesperado.", 'danger');
        });
    });

    window.cerrarModal = function () {
        modal.style.display = "none";
    };

    window.togglePassword = function () {
        toggleInputType(pass, "toggleIcon");
    };

    window.toggleConfirmPassword = function () {
        toggleInputType(confirmPass, "toggleConfirmIcon");
    };

    function toggleInputType(input, iconId) {
        const icon = document.getElementById(iconId);
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

    function mostrarMensaje(mensaje, tipo) {
        mensajeDiv.className = `alert alert-${tipo}`;
        mensajeDiv.textContent = mensaje;
        mensajeDiv.style.display = "flex";
        setTimeout(() => {
            mensajeDiv.style.opacity = "1";
        }, 100);
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});