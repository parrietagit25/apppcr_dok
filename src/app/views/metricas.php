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

    <!-- Filtros de Fecha -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary mb-3">
            <i class="bi bi-funnel"></i> Filtros de Fecha
        </h6>
        <form method="GET" action="" id="filtrosForm">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="fecha_desde" class="form-label small">Desde:</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_desde" name="fecha_desde" 
                           value="<?= isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '' ?>">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="fecha_hasta" class="form-label small">Hasta:</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_hasta" name="fecha_hasta" 
                           value="<?= isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '' ?>">
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarMetricas()">
                            <i class="bi bi-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="metricas" value="1">
        </form>
    </div>

    <!-- Rango de fechas seleccionado -->
    <?php if (isset($_GET['fecha_desde']) || isset($_GET['fecha_hasta'])): ?>
    <div class="alert alert-info mb-3">
        <i class="bi bi-calendar-range"></i> 
        <strong>Período seleccionado:</strong> 
        <?= isset($_GET['fecha_desde']) ? date('d/m/Y', strtotime($_GET['fecha_desde'])) : 'Inicio' ?> 
        - 
        <?= isset($_GET['fecha_hasta']) ? date('d/m/Y', strtotime($_GET['fecha_hasta'])) : 'Hoy' ?>
    </div>
    <?php endif; ?>

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

    <!-- UNIFORMES -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary">UNIFORMES</h6>
        <div class="row text-center mt-3">
            <div class="col-6 col-md-3 mb-3">
                <div class="border rounded p-3 h-100">
                    <h2 class="text-primary"><?= number_format($uniformes_total) ?></h2>
                    <div class="text-muted small">Total solicitudes</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="border rounded p-3 h-100">
                    <h2 class="text-warning"><?= number_format($uniformes_solicitado) ?></h2>
                    <div class="text-muted small">Solicitado</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="border rounded p-3 h-100">
                    <h2 class="text-info"><?= number_format($uniformes_en_proceso) ?></h2>
                    <div class="text-muted small">En proceso</div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="border rounded p-3 h-100">
                    <h2 class="text-success"><?= number_format($uniformes_entregado) ?></h2>
                    <div class="text-muted small">Entregado</div>
                </div>
            </div>
        </div>
        <?php if (array_sum($uniformesChartData) > 0): ?>
        <div class="row mt-2">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header py-2 text-center small">Distribución de uniformes por estado</div>
                    <div class="card-body py-2">
                        <canvas id="uniformesChart" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Distribución de permisos -->
    <div class="bg-white p-3 rounded shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div class="card my-3">
            <div class="card-header text-center">Distribución de permisos solicitados</div>
            <div class="card-body">
                <canvas id="permisosChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="card my-3">
        <div class="card-header text-center">Totales generales</div>
        <div class="card-body">
            <canvas id="totalesChart" height="270"></canvas>
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
    // Gráfico de permisos por tipo
    const labels = <?= json_encode(array_keys($permisosChartData)) ?>;
    const data   = <?= json_encode(array_values($permisosChartData)) ?>;

    const ctx = document.getElementById('permisosChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
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
                            const pct   = total > 0 ? (value * 100 / total).toFixed(1) : 0;
                            return `${context.label}: ${value} (${pct} %)`;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de uniformes por estado (si existe el canvas)
    const canvasUniformes = document.getElementById('uniformesChart');
    if (canvasUniformes) {
        const labelsUni = <?= json_encode(array_keys($uniformesChartData)) ?>;
        const dataUni   = <?= json_encode(array_values($uniformesChartData)) ?>;
        new Chart(canvasUniformes.getContext('2d'), {
            type: 'pie',
            data: {
                labels: labelsUni,
                datasets: [{
                    data: dataUni,
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const total = dataUni.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const pct   = total > 0 ? (value * 100 / total).toFixed(1) : 0;
                                return `${context.label}: ${value} (${pct} %)`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const totLabels = <?= json_encode(array_keys($dashboardTotals)) ?>;
  const totData   = <?= json_encode(array_values($dashboardTotals)) ?>;

  new Chart(document.getElementById('totalesChart'), {
      type: 'bar',
      data: {
          labels: totLabels,
          datasets: [{
              data: totData,
              // Chart.js generará colores por defecto;
              // si quieres fijarlos, añade `backgroundColor: [...]`
          }]
      },
      options: {
          indexAxis: 'y',               //  ←   barras horizontales
          responsive: true,
          scales: {
              x: { beginAtZero: true }
          },
          plugins: {
              legend: { display: false },
              tooltip: {
                  callbacks: {
                      label: ctx => `${ctx.raw} solicitudes`
                  }
              }
          }
      }
  });
});
</script>

<script>
// Función para limpiar los filtros de fecha
function limpiarFiltros() {
    document.getElementById('fecha_desde').value = '';
    document.getElementById('fecha_hasta').value = '';
    document.getElementById('filtrosForm').submit();
}

// Validación de fechas
document.getElementById('filtrosForm').addEventListener('submit', function(e) {
    const fechaDesde = document.getElementById('fecha_desde').value;
    const fechaHasta = document.getElementById('fecha_hasta').value;
    
    if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
        e.preventDefault();
        alert('La fecha "Desde" no puede ser mayor que la fecha "Hasta"');
        return false;
    }
});

// Establecer fecha por defecto si no hay filtros
document.addEventListener('DOMContentLoaded', function() {
    const fechaDesde = document.getElementById('fecha_desde').value;
    const fechaHasta = document.getElementById('fecha_hasta').value;
    
    if (!fechaDesde && !fechaHasta) {
        // Establecer fecha de inicio del mes actual como por defecto
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('fecha_desde').value = primerDia.toISOString().split('T')[0];
        document.getElementById('fecha_hasta').value = hoy.toISOString().split('T')[0];
    }
});

// Función para exportar métricas a Excel
function exportarMetricas() {
    const fechaDesde = document.getElementById('fecha_desde').value;
    const fechaHasta = document.getElementById('fecha_hasta').value;
    
    let url = '<?= BASE_URL_CONTROLLER ?>/MetricasController.php?exportar_excel=1';
    
    if (fechaDesde) {
        url += '&fecha_desde=' + encodeURIComponent(fechaDesde);
    }
    if (fechaHasta) {
        url += '&fecha_hasta=' + encodeURIComponent(fechaHasta);
    }
    
    window.open(url, '_blank');
}
</script>


<?php include __DIR__ . '/footer.php'; ?>
