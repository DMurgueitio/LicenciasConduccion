<?php
session_start();
$cedula_cliente = '';
if (isset($_GET['cedula'])) {
    $cedula_cliente = htmlspecialchars($_GET['cedula']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Registro de Cartera - CRC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        <?php include '../css/registrar_cartera.css'; ?>
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Registrar Nuevo Pago - Cartera</h2>

        <form id="formCartera" method="POST">
            <div class="form-grid">
                <!-- Cliente -->
                <div class="form-group">
                    <label><i class="fas fa-id-badge"></i> Cédula del Cliente</label>
                    <input type="text" name="cedula_cliente" value="<?= $cedula_cliente ?>" readonly>
                </div>

                <!-- Otros campos -->
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Número de Orden</label>
                    <input type="text" name="nro_orden" placeholder="Ej: ORD-1234" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Fecha de Pago</label>
                    <input type="date" name="fecha_pago" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-dollar-sign"></i> Valor Pagado</label>
                    <input type="number" step="0.01" name="valor_pago" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-money-bill-wave"></i> Medio de Pago</label>
                    <select name="id_medio_pago" required id="id_medio_pago"></select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-university"></i> Banco</label>
                    <select name="id_banco" id="id_banco"></select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-percentage"></i> Comisión (%)</label>
                    <input type="number" step="0.01" name="comision" value="0.00">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-sticky-note"></i> Observaciones</label>
                    <textarea name="observaciones"></textarea>
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-form">
                    <i class="fas fa-save"></i> Registrar Pago
                </button>
            </div>
        </form>
    </div>

    <script>
        window.onload = () => {
            fetch('cargar_medios_pago.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('id_medio_pago');
                    data.forEach(item => {
                        let opt = document.createElement('option');
                        opt.value = item.id_medio_pago;
                        opt.textContent = item.desc_medio_pago;
                        select.appendChild(opt);
                    });
                });

            fetch('cargar_bancos.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('id_banco');
                    select.innerHTML = '<option value="">Selecciona banco</option>';
                    data.forEach(item => {
                        let opt = document.createElement('option');
                        opt.value = item.id_banco;
                        opt.textContent = item.desc_banco;
                        select.appendChild(opt);
                    });
                });
        };

        document.getElementById('formCartera').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = new FormData(this);

            Swal.fire({
                title: '¿Registrar pago?',
                text: "Confirma que los datos son correctos.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Registrar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('guardar_cartera_ajax.php', {
                            method: 'POST',
                            body: form
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Registro exitoso',
                                    text: data.message,
                                    icon: 'success',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ir al Dashboard',
                                    cancelButtonText: 'Registrar otro cliente',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'dashboard.php'; // Ajusta a la ruta real de tu dashboard
                                    } else {
                                        window.location.href = 'registrar_cliente.php'; // Ajusta si tienes otra ruta
                                    }
                                });

                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Error al enviar datos.', 'error'));
                }
            });
        });
    </script>
</body>

</html>