<?php
//session_start();
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">
    
    <div class="container">
        <div class="row text-center mb-3">

            <div class="col-12 text-start">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">¿Qué es MAPFRE?</h5>
                        <p>MAPFRE es una aseguradora líder con presencia global que ofrece cobertura médica local e internacional. Tu seguro te permite acceder a una red amplia de médicos, hospitales y servicios médicos en Panamá y en otros países si tu plan lo contempla.</p>

                        <h6 class="fw-bold mt-4">📞 Contactos importantes:</h6>
                        <ul>
                            <li><strong>Casa Matriz (Costa del Este):</strong> 378-9800</li>
                            <li><strong>Emergencias 24 horas:</strong> 390-9090</li>
                            <li><strong>Correo general:</strong> <a href="mailto:consultas@mapfre.com.pa">consultas@mapfre.com.pa</a></li>
                            <li><strong>Urgencias:</strong> <a href="mailto:urgencias24@mapfre.com.pa">urgencias24@mapfre.com.pa</a></li>
                            <li><strong>Reautorizaciones:</strong> <a href="mailto:preautorizaciones@mapfre.com.pa">preautorizaciones@mapfre.com.pa</a></li>
                            <li><strong>Karla Cernichiaro (Indigo):</strong> 6485-2878</li>
                            <li><strong>Indigo Corredores:</strong> <a href="mailto:lg@indigoadvisorspanama.com">lg@indigoadvisorspanama.com</a></li>
                            <li><strong>Desde el extranjero:</strong> 1-844-498-3394</li>
                        </ul>

                        <h6 class="fw-bold mt-4">💡 ¿Qué es el Deducible y su valor actualizado?</h6>
                        <p><strong>Deducible en Seguro de Salud:</strong> Es la cantidad que debes pagar antes de que el seguro comience a cubrir los costos médicos.</p>
                        <p><strong>Nuevo valor del deducible:</strong> <span class="text-success fw-bold">$350.00</span></p>
                        <p>Ejemplo: Si tus gastos médicos son $1,000.00:</p>
                        <ul>
                            <li>Tú pagas los primeros $350.00</li>
                            <li>MAPFRE cubre el resto ($650.00) según tu plan</li>
                        </ul>
                        <p class="text-danger">Nota: El deducible aplica una vez al año. Usa la red MAPFRE para reducir gastos.</p>

                        <h6 class="fw-bold mt-4">📌 Otros Servicios Importantes:</h6>
                        <ul>
                            <li><strong>Reembolsos:</strong> Si usas servicios fuera de red, paga en efectivo y solicita reembolso presentando facturas.</li>
                            <li>Los reembolsos dependerán de tu plan.</li>
                        </ul>

                        <h6 class="fw-bold mt-4">⚠️ Condiciones Críticas – Cobertura 100% sin Pre-autorización</h6>
                        <p>Situaciones médicas graves con cobertura total inmediata:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li>Fracturas, luxaciones, esguinces</li>
                                    <li>Heridas, quemaduras, estado de choque o coma</li>
                                    <li>Deshidratación severa, crisis asmática</li>
                                    <li>Convulsiones, hemorragias, trombosis</li>
                                    <li>Infarto, apendicitis, crisis hipertensiva</li>
                                    <li>Intoxicaciones, reacciones alérgicas severas</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Cuerpo extraño en ojos, oídos, nariz</li>
                                    <li>Retención aguda de orina, cólico hepático</li>
                                    <li>Cólico nefro uretral, angina de pecho</li>
                                    <li>Reacciones por medicamentos o alimentos</li>
                                    <li>Insuficiencia respiratoria severa aguda</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-3"><strong>Importante:</strong> Si tu condición no está en esta lista, será considerada no crítica y no tendrá cobertura completa. En ese caso, acude primero a consulta externa.</p>

                        <h6 class="fw-bold mt-4">✅ Pasos en una Emergencia Médica Crítica</h6>
                        <ol>
                            <li>Acude al hospital de la red MAPFRE</li>
                            <li>Llama al <strong>390-9090, opción 5</strong></li>
                            <li>Presenta carné y cédula</li>
                            <li>Realiza el copago correspondiente</li>
                            <li>Recibe atención médica</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

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
                    <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?poliza=1" class="text-white text-decoration-none d-block py-2">
                        <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>
<?php include __DIR__ . '/footer.php'; ?>
