<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';

$pdf_telemedicina = BASE_URL_IMAGE . 'instructivos/PAN_TELEMEDICINA_INSTRUCTIVO.pdf';
$url_google_play = 'https://palig.com/_next/image?url=https%3A%2F%2Fimages.ctfassets.net%2Fem61esqt6ro0%2F4gZsKvnLZC2szm48mAolEq%2F82f49cb7fcb2593b9d34931db1bb162c%2FGetItOnGooglePlay_Badge_Web_color_Spanish-LATAM.png&w=1600&q=75';
$url_app_store = 'https://palig.com/_next/image?url=https%3A%2F%2Fimages.ctfassets.net%2Fem61esqt6ro0%2F4E9Ia9VbFY1OLzAqz2PLiZ%2Fec9315d9e8dce61bc8c91f2fde9325ed%2FDownload_on_the_App_Store_Badge_ES_wht_100217.png&w=1600&q=75';
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-primary mb-4">Beneficio de TELEMEDICINA</h4>

                    <p class="mb-3">
                        Beneficio ilimitado, el cual será ofrecido únicamente por nuestro proveedor, a través de video consultas médicas, para el asegurado y sus dependientes. No requiere co-pago.
                    </p>

                    <ol class="mb-4">
                        <li class="mb-2">Este beneficio mantiene una aplicación móvil a través de la cual se brindará el servicio.</li>
                        <li class="mb-2">Horario de servicio, a través de la aplicación: <strong>7:00 a.m. a 11:00 p.m.</strong></li>
                        <li>Horario <strong>24 horas</strong> para la atención telefónica, a través de <strong>PALIG ASISTENCIA 800-4200</strong>.</li>
                    </ol>

                    <div class="mb-4">
                        <a href="<?php echo htmlspecialchars($pdf_telemedicina, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf"></i> Ver información (PDF)
                        </a>
                    </div>

                    <h6 class="fw-bold mb-3">Descargar la aplicación</h6>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="<?php echo htmlspecialchars($url_google_play, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="d-inline-block">
                            <img src="<?php echo BASE_URL_IMAGE; ?>googleplay.png" alt="Descargar en Google Play" style="max-height: 52px; width: auto;">
                        </a>
                        <a href="<?php echo htmlspecialchars($url_app_store, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="d-inline-block">
                            <img src="<?php echo BASE_URL_IMAGE; ?>appstore.png" alt="Consíguelo en el App Store" style="max-height: 52px; width: auto;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
<br>
<br>

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
