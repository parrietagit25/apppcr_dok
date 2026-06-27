<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';

$labelsDiario = array_column($actividadDiaria ?? [], 'fecha');
$dataLogins = array_column($actividadDiaria ?? [], 'logins');
$dataVistas = array_column($actividadDiaria ?? [], 'vistas');
$dataAcciones = array_column($actividadDiaria ?? [], 'acciones');

$labelsModulos = array_column($topModulos ?? [], 'modulo');
$dataModulos = array_column($topModulos ?? [], 'total');

$labelsEventos = array_map(function ($r) {
    $map = [
        'login' => 'Ingresos',
        'logout' => 'Salidas',
        'page_view' => 'Vistas',
        'accion' => 'Acciones',
        'registro' => 'Registros',
    ];
    return $map[$r['evento']] ?? $r['evento'];
}, $eventosPorTipo ?? []);
$dataEventos = array_column($eventosPorTipo ?? [], 'total');

$labelsHoras = array_map(function ($r) {
    return sprintf('%02d:00', (int) $r['hora']);
}, $actividadPorHora ?? []);
$dataHoras = array_column($actividadPorHora ?? [], 'total');

$labelsUsuariosDia = array_column($usuariosPorDia ?? [], 'fecha');
$dataUsuariosDia = array_column($usuariosPorDia ?? [], 'usuarios');

$labelsTopUsers = array_map(function ($r) {
    $n = trim($r['nombre'] ?? '');
    return $n !== '' ? $n : ($r['codigo_empleado'] ?? '');
}, $topUsuarios ?? []);
$dataTopUsers = array_column($topUsuarios ?? [], 'total');
?>

<style>
    .tel-dashboard {
        background: #f3f2f1;
        min-height: 100vh;
        padding-bottom: 5rem;
    }
    .tel-hero {
        background: linear-gradient(135deg, #1a1f36 0%, #2b4c7e 50%, #0078d4 100%);
        color: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    .tel-kpi {
        background: #fff;
        border-radius: 10px;
        padding: 1rem 1.1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border-left: 4px solid #0078d4;
        height: 100%;
    }
    .tel-kpi .valor {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a1f36;
        line-height: 1.1;
    }
    .tel-kpi .etiqueta {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .tel-kpi.kpi-green { border-left-color: #107c10; }
    .tel-kpi.kpi-purple { border-left-color: #5c2d91; }
    .tel-kpi.kpi-orange { border-left-color: #d83b01; }
    .tel-kpi.kpi-teal { border-left-color: #008272; }
    .tel-kpi.kpi-navy { border-left-color: #1a1f36; }
    .tel-chart-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        padding: 1rem 1.1rem;
        height: 100%;
    }
    .tel-chart-card h6 {
        font-weight: 700;
        color: #1a1f36;
        margin-bottom: 0.75rem;
    }
    .tel-filtros {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.25rem;
    }
    .tel-table-wrap {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }
    .tel-table-wrap table {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .badge-evento-login { background: #107c10; }
    .badge-evento-page_view { background: #0078d4; }
    .badge-evento-accion { background: #d83b01; }
    .badge-evento-logout { background: #6c757d; }
    .badge-evento-registro { background: #5c2d91; }
</style>

<div class="tel-dashboard container-fluid px-3 mt-3">
    <div class="tel-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <h4 class="mb-1"><i class="bi bi-graph-up-arrow"></i> Telemetría APP PCR</h4>
                <p class="mb-0 small opacity-75">Seguimiento de ingresos, navegación e interacciones de usuarios</p>
            </div>
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Inicio
            </a>
        </div>
    </div>

    <?php if (empty($tablaOk)) { ?>
        <div class="alert alert-warning">
            Ejecute el script <code>src/sql/telemetria.sql</code> en la base de datos para activar el registro de eventos.
        </div>
    <?php } ?>

    <div class="tel-filtros">
        <form method="GET" action="<?php echo BASE_URL_CONTROLLER; ?>/TelemetriaController.php" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-0">Desde</label>
                <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fecha_desde); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i> Aplicar filtro</button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="telRango(7)">Últimos 7 días</button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
            <div class="tel-kpi">
                <div class="valor"><?php echo number_format($kpis['total_eventos'] ?? 0); ?></div>
                <div class="etiqueta">Eventos totales</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="tel-kpi kpi-green">
                <div class="valor"><?php echo number_format($kpis['logins'] ?? 0); ?></div>
                <div class="etiqueta">Ingresos</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="tel-kpi kpi-purple">
                <div class="valor"><?php echo number_format($kpis['page_views'] ?? 0); ?></div>
                <div class="etiqueta">Vistas</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="tel-kpi kpi-orange">
                <div class="valor"><?php echo number_format($kpis['acciones'] ?? 0); ?></div>
                <div class="etiqueta">Interacciones</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="tel-kpi kpi-teal">
                <div class="valor"><?php echo number_format($kpis['usuarios_unicos'] ?? 0); ?></div>
                <div class="etiqueta">Usuarios únicos</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="tel-kpi kpi-navy">
                <div class="valor"><?php echo number_format($kpis['modulos_activos'] ?? 0); ?></div>
                <div class="etiqueta">Módulos usados</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="tel-chart-card">
                <h6><i class="bi bi-activity"></i> Actividad diaria</h6>
                <canvas id="chartDiario" height="110"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="tel-chart-card">
                <h6><i class="bi bi-pie-chart"></i> Tipos de evento</h6>
                <canvas id="chartEventos" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="tel-chart-card">
                <h6><i class="bi bi-bar-chart"></i> Módulos más visitados</h6>
                <canvas id="chartModulos" height="140"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="tel-chart-card">
                <h6><i class="bi bi-people"></i> Usuarios más activos</h6>
                <canvas id="chartUsuarios" height="140"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="tel-chart-card">
                <h6><i class="bi bi-clock"></i> Actividad por hora del día</h6>
                <canvas id="chartHoras" height="120"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="tel-chart-card">
                <h6><i class="bi bi-person-lines-fill"></i> Usuarios activos por día</h6>
                <canvas id="chartUsuariosDia" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="tel-table-wrap mb-4">
        <div class="p-3 border-bottom">
            <h6 class="mb-0 fw-bold"><i class="bi bi-list-ul"></i> Eventos recientes</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>Módulo</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($eventosRecientes)) { ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Sin eventos en el período seleccionado</td></tr>
                    <?php } else { ?>
                        <?php foreach ($eventosRecientes as $ev) {
                            $evClass = 'badge-evento-' . preg_replace('/[^a-z_]/', '', $ev['evento']);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($ev['created_at']))); ?></td>
                                <td>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($ev['codigo_empleado'] ?? '—'); ?></small>
                                    <?php echo htmlspecialchars($ev['nombre'] ?? ''); ?>
                                </td>
                                <td><span class="badge <?php echo htmlspecialchars($evClass); ?>"><?php echo htmlspecialchars($ev['evento']); ?></span></td>
                                <td><?php echo htmlspecialchars($ev['modulo'] ?? '—'); ?></td>
                                <td><small><?php echo htmlspecialchars($ev['accion'] ?? $ev['ruta'] ?? '—'); ?></small></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const palette = ['#0078d4', '#107c10', '#d83b01', '#5c2d91', '#008272', '#ffb900', '#e81123', '#00188f'];

function telChartDefaults() {
    Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
    Chart.defaults.color = '#444';
}

function telRango(dias) {
    const h = new Date();
    const d = new Date();
    d.setDate(h.getDate() - dias + 1);
    document.querySelector('[name=fecha_hasta]').value = h.toISOString().split('T')[0];
    document.querySelector('[name=fecha_desde]').value = d.toISOString().split('T')[0];
    document.querySelector('.tel-filtros form').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    telChartDefaults();

    new Chart(document.getElementById('chartDiario'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsDiario) ?>,
            datasets: [
                { label: 'Ingresos', data: <?= json_encode($dataLogins) ?>, borderColor: '#107c10', backgroundColor: 'rgba(16,124,16,.12)', tension: 0.35, fill: true },
                { label: 'Vistas', data: <?= json_encode($dataVistas) ?>, borderColor: '#0078d4', backgroundColor: 'rgba(0,120,212,.1)', tension: 0.35, fill: true },
                { label: 'Interacciones', data: <?= json_encode($dataAcciones) ?>, borderColor: '#d83b01', backgroundColor: 'rgba(216,59,1,.08)', tension: 0.35, fill: true }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, grid: { color: '#eee' } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('chartEventos'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labelsEventos) ?>,
            datasets: [{ data: <?= json_encode($dataEventos) ?>, backgroundColor: palette, borderWidth: 2, borderColor: '#fff' }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartModulos'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsModulos) ?>,
            datasets: [{ label: 'Visitas', data: <?= json_encode($dataModulos) ?>, backgroundColor: '#0078d4', borderRadius: 4 }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#eee' } } }
        }
    });

    new Chart(document.getElementById('chartUsuarios'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsTopUsers) ?>,
            datasets: [{ label: 'Eventos', data: <?= json_encode($dataTopUsers) ?>, backgroundColor: '#5c2d91', borderRadius: 4 }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#eee' } } }
        }
    });

    new Chart(document.getElementById('chartHoras'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsHoras) ?>,
            datasets: [{ label: 'Eventos', data: <?= json_encode($dataHoras) ?>, backgroundColor: '#008272', borderRadius: 3 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#eee' } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('chartUsuariosDia'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsUsuariosDia) ?>,
            datasets: [{
                label: 'Usuarios únicos',
                data: <?= json_encode($dataUsuariosDia) ?>,
                borderColor: '#00188f',
                backgroundColor: 'rgba(0,24,143,.1)',
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
        }
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
