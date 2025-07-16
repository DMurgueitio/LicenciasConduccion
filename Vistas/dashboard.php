<?php
session_start();

if (!isset($_SESSION['ID_Usuario'])) {
    header("Location: login.php");
    exit();
}


require_once "../modelos/conexion.php"; 

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>SICC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <header class="topbar">
        <div class="logo">
            <i class="fas fa-shield-alt"></i> Sistema Integral de Cartera y Cobranza
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

                <?php if (isset($_SESSION['NivelRol']) && $_SESSION['NivelRol'] == 1): ?>
                    <li><a href="gestion_escuelas.php"><i class="fas fa-school"></i> Gestionar Escuelas</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <section class="content">
            <div class="welcome-box">
                <h1>¡Bienvenido al Sistema Integral de Cartera y Cobranza!</h1>
                <strong>
                    <p>SICC.</p>
                </strong>
            </div>
            
            <div class="cards">
                <div class="card card-clientes">
                    <i class="fas fa-users icon-card"></i>
                    <h3>Total Clientes del Día</h3>
                    <p id="total-clientes">Cargando...</p>
                </div>

                <div class="card card-pagos">
                    <i class="fas fa-dollar-sign icon-card"></i>
                    <h3>Ingreso Total del Día</h3>
                    <p id="total-pagos">Cargando...</p>
                </div>

                <div class="card card-medios-pago">
                    <i class="fas fa-money-check-alt icon-card"></i>
                    <h3>Desglose por Medio de Pago</h3>
                    <ul id="medios-pago-detalle" style="margin-top: 10px; font-size: 14px; text-align: left;">
                        <li>Efectivo: <span id="efectivo">0.00</span></li>
                        <li>Crédito: <span id="credito">0.00</span></li>
                        <li>Transferencias: <span id="transferencias">0.00</span></li>
                    </ul>
                </div>
            </div>

            <div class="table-section">
                <div class="actions">
                    <a href="./exportar_clientes_excel.php" class="btn-export" target="_blank">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                </div>
                <h2>Clientes del Día</h2>
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

    <div class="charts-row">
        <div class="chart-box">
            <canvas id="chartComparativoAnual"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="chartEscuelasTramites"></canvas>
        </div>
        <div class="chart-box">
            <canvas id="chartTipoTramite"></canvas>
        </div>
    </div>

    <script>
        // Helper function to format numbers with thousand separators and 2 decimal places
        function formatoMiles(numero) {
            const n = parseFloat(numero);
            if (isNaN(n)) return "0,00"; // Handle non-numeric input gracefully

            const [entero, decimal] = n.toFixed(2).split(".");
            const enteroFormateado = entero.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            return `${enteroFormateado},${decimal}`;
        }

        // --- Data Loading Functions ---

        // Function to fetch and display daily totals (cards)
        async function loadDailyTotals() {
            try {
                const response = await fetch('obtener_totales_del_dia.php', { credentials: 'include' });
                if (!response.ok) {
                    if (response.status === 401) {
                        console.error('Error 401: No autorizado para obtener totales. Por favor, inicie sesión.');
                        document.getElementById('total-clientes').textContent = 'N/A';
                        document.getElementById('total-pagos').textContent = 'N/A';
                        document.getElementById('efectivo').textContent = 'N/A';
                        document.getElementById('credito').textContent = 'N/A';
                        document.getElementById('transferencias').textContent = 'N/A';
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                document.getElementById('total-clientes').textContent = `${data.total_clientes ?? 0} clientes`;
                document.getElementById('total-pagos').textContent = `$${formatoMiles(data.total_pagos ?? 0)}`;
                document.getElementById('efectivo').textContent = `$${formatoMiles(data.efectivo ?? 0)}`;
                document.getElementById('credito').textContent = `$${formatoMiles(data.credito ?? 0)}`;
                document.getElementById('transferencias').textContent = `$${formatoMiles(data.transferencia ?? 0)}`;

            } catch (error) {
                console.error("Error cargando totales:", error);
                document.getElementById('total-clientes').textContent = "Error";
                document.getElementById('total-pagos').textContent = "Error";
                document.getElementById('efectivo').textContent = "Error";
                document.getElementById('credito').textContent = "Error";
                document.getElementById('transferencias').textContent = "Error";
            }
        }

        // Función para obtener y mostrar los clientes del día en una tabla
async function loadDailyClients() {
    const tbody = document.querySelector("#tabla-clientes tbody");
    tbody.innerHTML = "<tr><td colspan='5'>Cargando datos...</td></tr>";

    try {
        const response = await fetch('obtener_clientes_del_dia.php');
        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
        }

        const clientes = await response.json();
        tbody.innerHTML = ""; // Limpiar datos anteriores

        if (!Array.isArray(clientes) || clientes.length === 0) {
            tbody.innerHTML = "<tr><td colspan='5'>No hay clientes registrados hoy.</td></tr>";
            return;
        }

        clientes.forEach(c => {
            const row = document.createElement("tr");

            const cedula = document.createElement("td");
            cedula.textContent = c.Cedula_Cliente || "N/A";
            row.appendChild(cedula);

            const nombre = document.createElement("td");
            nombre.textContent = c.Apellidos_Nombre || "Sin nombre";
            row.appendChild(nombre);

            const telefono = document.createElement("td");
            telefono.textContent = c.Telefono || "-";
            row.appendChild(telefono);

            const categoria = document.createElement("td");
            categoria.textContent = c.Categoria || "Sin categoría";
            row.appendChild(categoria);

            const escuela = document.createElement("td");
            escuela.textContent = c.Escuela || "Sin escuela";
            row.appendChild(escuela);

            tbody.appendChild(row);
        });

    } catch (err) {
        console.error("Error al cargar los clientes del día:", err);
        tbody.innerHTML = "<tr><td colspan='5'>Error al cargar los datos.</td></tr>";
    }
}


        // --- Chart Loading Functions ---

        // Gráfico Comparativo Anual
        async function loadAnnualComparisonChart() {
            try {
                const response = await fetch('obtener_totales_del_dia.php'); // Re-using data from here
                const data = await response.json();

                const ctxAnual = document.getElementById('chartComparativoAnual').getContext('2d');
                new Chart(ctxAnual, {
                    type: 'line',
                    data: {
                        labels: [data.currentYear, data.lastYear],
                        datasets: [{
                            label: 'Ingresos Totales',
                            data: [data.anual_actual, data.anual_anterior],
                            borderColor: '#FF6B6B',
                            backgroundColor: '#FF6B6B',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: context => "$" + context.parsed.y.toLocaleString('es-CO') // Use locale for better formatting
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: value => "$" + value.toLocaleString('es-CO')
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error("Error cargando gráfico comparativo anual:", error);
            }
        }

        // Gráfico Escuelas con más trámites
        async function loadSchoolsChart() {
            try {
                const response = await fetch('obtener_escuelas_tramites.php');
                const datos = await response.json();
                const ctxEscuela = document.getElementById('chartEscuelasTramites').getContext('2d');
                new Chart(ctxEscuela, {
                    type: 'bar',
                    data: {
                        labels: datos.labels,
                        datasets: datos.datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: { // Add a title for clarity
                                display: true,
                                text: 'Escuelas con Más Trámites'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            } catch (err) {
                console.error("Error cargando datos de escuelas:", err);
            }
        }

        // Gráfico Tipos de Trámites por Año
        async function loadTransactionTypesChart() {
            try {
                const response = await fetch('obtener_tipo_tramite.php');
                const datos = await response.json();
                const ctxTramite = document.getElementById('chartTipoTramite').getContext('2d');
                new Chart(ctxTramite, {
                    type: 'bar',
                    data: {
                        labels: datos.labels,
                        datasets: datos.datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Comparativo de Tipos de Trámites'
                            }
                        }
                    }
                });
            } catch (err) {
                console.error("Error cargando tipos de trámites:", err);
            }
        }

        // --- Call all loading functions when the DOM is ready ---
        document.addEventListener("DOMContentLoaded", function () {
            loadDailyTotals();
            loadDailyClients();
            loadAnnualComparisonChart();
            loadSchoolsChart();
            loadTransactionTypesChart();
        });
    </script>
</body>
</html>