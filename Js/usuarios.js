document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalEditar');
    const btnCerrar = document.getElementById('cerrarModal');
    const btnEditar = document.querySelectorAll('.btn-edit');

    btnCerrar.onclick = () => {
        modal.style.display = 'none';
    };

    btnEditar.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            fetch(`obtener_usuario.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editId').value = data.ID_Usuario;
                    document.getElementById('editNombre').value = data.nombre_usuario;
                    document.getElementById('editRol').value = data.id_rol;
                    modal.style.display = 'block';
                });
        });
    });

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
});