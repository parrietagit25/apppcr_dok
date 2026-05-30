<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';

$enfermedades_criticas_col1 = [
    'Crisis Hipertensiva',
    'Accidente cerebro vascular',
    'Dolor precordial – primeras doce horas',
    'Fiebre alta continua en menores de cinco años',
    'Crisis Asmática',
    'Pérdida del Conocimiento, obnubilación',
    'Dolor abdominal agudo',
    'Cólico biliar',
    'Hemorragias',
    'Insuficiencias respiratorias agudas',
    'Deshidratación',
];

$enfermedades_criticas_col2 = [
    'Intoxicación aguda',
    'Cólico nefroureteral',
    'Trombosis',
    'Vómito o diarrea severa',
    'Convulsiones',
    'Reacciones alérgicas agudas',
    'Retención aguda de orina',
    'Infarto del miocardio',
    'Episodios neurológicos agudos',
    'Estado de choque (shock) de cualquier orden',
    'Coma',
];
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?php echo BASE_URL_IMAGE; ?>palig.jpg" alt="PALIG" style="max-width: 180px; height: auto;" class="rounded">
                    </div>

                    <h4 class="fw-bold text-primary mb-3">Pan American Life Insurance de Panamá - PALIG</h4>
                    <p class="mb-4">
                        Pan American Life Insurance de Panamá es una aseguradora con amplia trayectoria en seguros de salud y vida en la región, ofreciendo soluciones enfocadas en el bienestar y la atención oportuna de los asegurados, a través de una sólida red de proveedores médicos.
                    </p>

                    <hr>

                    <h6 class="fw-bold mt-4">📞 Contactos de la Aseguradora</h6>
                    <p class="fw-bold mb-1">Central Telefónica Pan American Life – PALIG</p>
                    <ul class="mb-3">
                        <li><strong>Teléfono:</strong> 208-8000</li>
                        <li><strong>Correo electrónico:</strong> <a href="mailto:servicioalclientepanama@palig.com">servicioalclientepanama@palig.com</a></li>
                    </ul>
                    <p class="fw-bold mb-1">Preautorizaciones</p>
                    <ul class="mb-4">
                        <li><strong>Correo electrónico:</strong> <a href="mailto:ppreautorizaciones@palig.com">ppreautorizaciones@palig.com</a></li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold mt-4">🤝 Corredores de Seguros</h6>
                    <p class="fw-bold mb-1">Indigo Advisors</p>
                    <ul class="mb-2">
                        <li><strong>Gabriella Greer</strong> – Ejecutiva Senior de Cuentas Corporativas<br>
                            Correo: <a href="mailto:gg@indigoadvisorspanama.com">gg@indigoadvisorspanama.com</a><br>
                            Teléfono: 6933-1227
                        </li>
                        <li class="mt-2"><strong>Lori Guerra</strong> – Oficial de Indemnizaciones Ramos personas<br>
                            Correo: <a href="mailto:lg@indigoadvisorspanama.com">lg@indigoadvisorspanama.com</a><br>
                            Teléfono: 6835-0361
                        </li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold mt-4">💡 ¿Qué es el Deducible y su valor actualizado?</h6>
                    <p><strong>Deducible en Seguro de Salud:</strong> Es la cantidad que debes pagar antes de que el seguro comience a cubrir los costos médicos.</p>
                    <p><strong>Deducible anual aplicable:</strong> <span class="text-success fw-bold">$350.00</span></p>

                    <h6 class="fw-bold mt-4 text-danger">⚠️ Atención en Cuarto de Urgencias</h6>

                    <p class="fw-bold mb-3">Listado de Enfermedades Crítico Detalladas – Beneficio de Cuarto de Urgencias:</p>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm align-top mb-0">
                            <tbody>
                                <tr>
                                    <td class="w-50">
                                        <ul class="mb-0 ps-3">
                                            <?php foreach ($enfermedades_criticas_col1 as $item): ?>
                                            <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                    <td class="w-50">
                                        <ul class="mb-0 ps-3">
                                            <?php foreach ($enfermedades_criticas_col2 as $item): ?>
                                            <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <ul class="mb-3">
                        <li>Cuarto de Urgencias por Accidente al <strong>100%</strong>.</li>
                        <li>Cuarto de Urgencias por Enfermedad del listado Crítico Detallado: Co-Pago <strong>$25.00</strong>.</li>
                        <li>Cuarto de Urgencias por Enfermedad fuera del listado Crítico Detallado: al <strong>80%</strong> después del deducible.</li>
                    </ul>

                    <div class="alert alert-warning mb-0">
                        <strong>👉 Importante:</strong> Si tu condición no está en esta lista, será considerada no crítica y no tendrá cobertura completa. En ese caso, acude primero a consulta externa.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br><br>

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
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?n_poliza=1" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
