<?php
session_start();

if (!isset($_SESSION['ID_Usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard - Centro CRC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Estilos profesionales -->
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <header class="topbar">
        <div class="logo">
            <i class="fas fa-shield-alt"></i> Centro de Reconocimiento Conductores
        </div>
        <nav class="user-nav">
            <span><i class="fas fa-user-circle"></i> <?= $_SESSION['NombreUsuario'] ?? 'Usuario' ?></span>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </nav>
    </header>

    <main class="container-main">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-tachometer-alt"></i> Panel Principal</h3>
            </div>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="registrar_cliente.php"><i class="fas fa-id-card"></i> Registrar Cliente</a></li>
                <li><a href="gestion_pagos.php"><i class="fas fa-money-check-alt"></i> Gestión de Pagos</a></li>
                <li><a href="reportes.php"><i class="fas fa-chart-line"></i> Reportes</a></li>
                <li><a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a></li>
            </ul>
        </aside>

        <section class="content">
            <div class="welcome-box">
                <h1>¡Bienvenido al Sistema CRC!</h1>
                <p>Selecciona una opción del menú lateral para comenzar.</p>
            </div>

            <div class="cards">
                <!-- Tarjeta Clientes -->
                <div class="card card-clientes">
                    <i class="fas fa-users icon-card"></i>
                    <h3>Total Clientes del Día</h3>
                    <p id="total-clientes">Cargando...</p>
                </div>

                <!-- Tarjeta Ingresos Totales -->
                <div class="card card-pagos">
                    <i class="fas fa-dollar-sign icon-card"></i>
                    <h3>Ingreso Total del Día</h3>
                    <p id="total-pagos">Cargando...</p>
                </div>

                <!-- Tarjeta Detalles de Pago -->
                <div class="card card-medios-pago">
                    <i class="fas fa-money-check-alt icon-card"></i>
                    <h3>Desglose por Medio de Pago</h3>
                    <ul id="medios-pago-detalle" style="margin-top: 10px; font-size: 14px; text-align: left;">
                        <li>Efectivo: $<span id="efectivo">0.00</span></li>
                        <li>Crédito: $<span id="credito">0.00</span></li>
                        <li>Transferencias: $<span id="transferencias">0.00</span></li>
                    </ul>
                </div>
            </div>

            <div class="charts-container">
                <canvas id="chartPagos" width="400" height="150"></canvas>
            </div>

            <div class="table-section">
                <div class="actions">
                    <button id="btnExportar" class="btn-export">
                        <i class="fas fa-file-pdf"></i> Exportar Resumen del Día
                    </button>
                </div>
                <h2>Últimos Clientes Registrados</h2>
                <table class="data-table" id="tabla-clientes">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Categoría</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">Cargando datos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        // Cargar estadísticas del día con AJAX
        fetch('obtener_totales_del_dia.php')
            .then(res => res.json())
            .then(data => {
                document.getElementById('total-clientes').textContent = `${data.total_clientes} clientes`;
                document.getElementById('total-pagos').textContent = `$${data.total_pagos.toFixed(2)}`;
                document.getElementById('efectivo').textContent = data.efectivo.toFixed(2);
                document.getElementById('credito').textContent = data.credito.toFixed(2);
                document.getElementById('transferencias').textContent = data.transferencias.toFixed(2);
            });

        // Cargar tabla de clientes recientes
        fetch('obtener_clientes_recientes.php')
            .then(res => res.json())
            .then(clientes => {
                const tbody = document.querySelector("#tabla-clientes tbody");
                tbody.innerHTML = "";
                if (clientes.length === 0) {
                    tbody.innerHTML = "<tr><td colspan='4'>No hay registros</td></tr>";
                    return;
                }
                clientes.forEach(c => {
                    tbody.innerHTML += `
                    <tr>
                        <td>${c.cedula_cliente}</td>
                        <td>${c.apellidos_nombre}</td>
                        <td>${c.telefono || '-'}</td>
                        <td>${c.descripcion || '-'}</td>
                    </tr>`;
                });
            });

        // Gráfico de pagos por categoría
        fetch('obtener_pagos_por_categoria.php')
            .then(res => res.json())
            .then(datos => {
                const ctx = document.getElementById('chartPagos').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: datos.labels,
                        datasets: [{
                            label: 'Ingresos por Categoría',
                            data: datos.values,
                            backgroundColor: '#5FDAF5'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => "$" + context.parsed.y.toLocaleString()
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: value => "$" + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            });
    </script>
    <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<!-- jsPDF (genera PDF) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> 

<script>
document.getElementById("btnExportar").addEventListener("click", () => {
    fetch('obtener_totales_del_dia.php')
        .then(res => res.json())
        .then(data => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const hoy = new Date().toLocaleDateString();
            const totalClientes = data.total_clientes;
            const ingresoTotal = `$${parseFloat(data.total_pagos).toFixed(2)}`;
            const efectivo = `$${parseFloat(data.efectivo).toFixed(2)}`;
            const credito = `$${parseFloat(data.credito).toFixed(2)}`;
            const transferencias = `$${parseFloat(data.transferencias).toFixed(2)}`;

            // Título del documento
            doc.setFontSize(18);
            doc.text("Resumen Diario - CRC", 20, 20);

            doc.setFontSize(12);
            doc.text(`Fecha: ${hoy}`, 20, 30);
            doc.text(`Total Clientes: ${totalClientes}`, 20, 40);
            doc.text(`Ingreso Total: ${ingresoTotal}`, 20, 50);
            doc.text(`Efectivo: ${efectivo}`, 20, 60);
            doc.text(`Crédito: ${credito}`, 20, 70);
            doc.text(`Transferencias: ${transferencias}`, 20, 80);

            // Tabla de clientes recientes
            doc.text("Últimos Clientes Registrados:", 20, 90);

            // Obtener tabla de clientes
            fetch('obtener_clientes_recientes.php')
                .then(res => res.json())
                .then(clientes => {
                    let y = 100;
                    if (clientes.length === 0) {
                        doc.text("No hay registros.", 20, y + 10);
                    } else {
                        clientes.forEach((cliente, index) => {
                            doc.text(`${index + 1}. ${cliente.apellidos_nombre} - ${cliente.descripcion || 'Sin categoría'}`, 20, y);
                            y += 10;
                        });
                    }

                    // Guardar PDF
                    doc.save(`Reporte_CRC_${hoy}.pdf`);
                    Swal.fire({
                        icon: "success",
                        title: "PDF Generado",
                        text: "El reporte se ha descargado correctamente.",
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
        })
        .catch(err => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudieron obtener los datos para el PDF."
            });
        });
});
</script>
</body>

</html>