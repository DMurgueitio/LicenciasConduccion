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

                <!-- Mostrar solo si es administrador -->
                <?php if (isset($_SESSION['NivelRol']) && $_SESSION['NivelRol'] == 1): ?>
                    <li><a href="gestion_escuelas.php"><i class="fas fa-school"></i> Gestionar Escuelas</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <section class="content">
            <div class="welcome-box">
                <h1>¡Bienvenido al Sistema Integral de Cartera y Cobranza!</h1>
                <STRONg>
                    <p>SICC.</p>
                </STRONg>
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
    <!-- ... (resto del HTML sin cambios) ... -->
<div class="charts-container">
    <canvas id="chartPagos" width="400" height="150"></canvas>
</div>

<!-- Nuevos gráficos -->
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
    let dataTotales = {}; // Variable global para reutilizar datos

    // Cargar estadísticas del día con AJAX
    fetch('obtener_totales_del_dia.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error("Error desde servidor:", data.error);
                return;
            }

            dataTotales = data;

            document.getElementById('total-clientes').textContent = `${data.total_clientes} clientes`;
            document.getElementById('total-pagos').textContent = `$${data.total_pagos.toFixed(2)}`;
            document.getElementById('efectivo').textContent = data.efectivo.toFixed(2);
            document.getElementById('credito').textContent = data.credito.toFixed(2);
            document.getElementById('transferencias').textContent = data.transferencias.toFixed(2);

            // Gráfico Comparativo Anual
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

        })
        .catch(err => {
            console.error("Error cargando totales:", err);
            document.getElementById('total-clientes').textContent = "Error";
            document.getElementById('total-pagos').textContent = "Error";
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

    // Gráfico Escuelas con más trámites
    fetch('obtener_escuelas_tramites.php')
        .then(res => res.json())
        .then(datos => {
            const ctxEscuela = document.getElementById('chartEscuelasTramites').getContext('2d');
            new Chart(ctxEscuela, {
                type: 'bar',
                data: {
                    labels: datos.labels,
                    datasets: [{
                        label: 'Número de Trámites',
                        data: datos.values,
                        backgroundColor: '#4ECDC4'
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });

    // Gráfico Tipos de Trámites por Año
    fetch('obtener_tipo_tramite.php')
        .then(res => res.json())
        .then(datos => {
            const ctxTramite = document.getElementById('chartTipoTramite').getContext('2d');
            new Chart(ctxTramite, {
                type: 'pie',
                data: {
                    labels: datos.labels,
                    datasets: [{
                        label: 'Trámites por Tipo',
                        data: datos.values,
                        backgroundColor: ['#45B7D1', '#92C952', '#FDB45C']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });

</script>
</body>

</html>