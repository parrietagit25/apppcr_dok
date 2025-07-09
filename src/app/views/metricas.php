<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-3">

    <!-- Tarjeta introductoria -->
    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary text-white px-3 py-2 rounded-start">
            <i class="bi bi-person-circle"></i> Metricas
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Resumen de métricas laborales GENTE PCR
        </div>
    </div>

    <!-- INFORMACIÓN PERSONAL -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary">INFORMACIÓN PERSONAL</h6>
        <div class="row text-center mt-3">
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2>41</h2>
                    <div class="text-muted small">Actualizacion de  Datos</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($cartas_trabajos) ?></h2>
                    <div class="text-muted small">Solicitud de Carta de trabajo</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($calamidades) ?></h2>
                    <div class="text-muted small">Solicitud de Calamidades</div>
                </a>
            </div>
        </div>
    </div>

    <!-- GESTIÓN DE TIEMPO Y AUSENCIAS -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary">GESTIÓN DE TIEMPO Y AUSENCIAS</h6>
        <div class="row text-center mt-3">
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($incapacidades) ?></h2>
                    <div class="text-muted small">Incapacidades Registradas</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($permisos) ?></h2>
                    <div class="text-muted small">Total de Permisos Solicitados</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($vacaciones) ?></h2>
                    <div class="text-muted small">Permisos por Vacaciones</div>
                </a>
            </div>

            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($compensatorio) ?></h2>
                    <div class="text-muted small">Permisos por Compensarotios</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($cita_medica) ?></h2>
                    <div class="text-muted small">Permisos por Citas medicas</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($enfermedad) ?></h2>
                    <div class="text-muted small">Permisos por enfermedad</div>
                </a>
            </div>

            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($tele_trabajo) ?></h2>
                    <div class="text-muted small">Permisos por Teletrabajo</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($duelo) ?></h2>
                    <div class="text-muted small">Permisos por Duelo</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="#" class="text-decoration-none">
                    <h2><?= number_format($tiempo_sin_pago) ?></h2>
                    <div class="text-muted small">Permisos por Tiempo sin pago</div>
                </a>
            </div>

        </div>
    </div>

    <!-- Vacantes -->
    <div class="bg-white p-3 rounded shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div class="card my-3">
            <div class="card-header text-center">Distribución de permisos solicitados</div>
            <div class="card-body">
                <canvas id="permisosChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>

</div>

<!-- Footer navegación -->
<nav class="navbar fixed-bottom navbar-light bg-primary">
    <div class="container-fluid text-center text-white">
        <div class="row w-100">
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-house-door-fill fs-4"></i><br><small>Inicio</small>
                </a>
            </div>
            <div class="col">
                <a href="#" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-gear-fill fs-4"></i><br><small>Ajustes</small>
                </a>
            </div>
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // PHP → JS
    const labels = <?= json_encode(array_keys($permisosChartData)) ?>;
    const data   = <?= json_encode(array_values($permisosChartData)) ?>;

    const ctx = document.getElementById('permisosChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                // Chart.js asigna colores por defecto; los podrías personalizar si lo deseas
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const total = data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const pct   = (value * 100 / total).toFixed(1);
                            return `${context.label}: ${value} (${pct} %)`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
