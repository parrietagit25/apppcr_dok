<?php
require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación - Usuario Regular (Tipo 2)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .doc-header {
            background: linear-gradient(135deg, #003399 0%, #0d6efd 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .section-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .section-card:hover {
            transform: translateY(-5px);
        }
        .section-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .feature-item {
            padding: 0.75rem;
            border-left: 3px solid #0d6efd;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
        }
        .back-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="doc-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="bi bi-book"></i> Documentación del Sistema</h1>
                    <p class="mb-0 mt-2">Guía completa para usuarios regulares (Tipo 2)</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?php echo BASE_URL_CONTROLLER; ?>/AuthController.php" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Volver al Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Introducción -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-info-circle section-icon"></i> Introducción</h3>
                <p class="card-text">
                    Esta documentación está diseñada para ayudarte a utilizar todas las funcionalidades disponibles 
                    en el sistema APP PCR como usuario regular (Tipo 2). Aquí encontrarás información detallada sobre 
                    cada opción y cómo utilizarla.
                </p>
            </div>
        </div>

        <!-- Página Principal -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-house-door section-icon"></i> Página Principal</h3>
                <p class="card-text">Al iniciar sesión, accederás a la página principal donde encontrarás las siguientes opciones:</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-person-circle"></i> <strong>Mi Espacio</strong></h6>
                            <p class="mb-0">Accede a todas tus gestiones personales, solicitudes y documentos.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-gift"></i> <strong>Mis Beneficios</strong></h6>
                            <p class="mb-0">Consulta todos los beneficios disponibles para colaboradores.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-card-text"></i> <strong>Mi Carnet</strong></h6>
                            <p class="mb-0">Visualiza y descarga tu carnet de colaborador en formato digital.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-balloon"></i> <strong>Cumpleaños</strong></h6>
                            <p class="mb-0">Consulta los cumpleaños del mes de tus compañeros.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-envelope"></i> <strong>Correo</strong></h6>
                            <p class="mb-0">Acceso directo para contactar a RRHH por correo electrónico.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-shield-check"></i> <strong>Mi Póliza</strong></h6>
                            <p class="mb-0">Consulta información sobre tu póliza de seguro.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-clipboard-check"></i> <strong>Evaluación</strong></h6>
                            <p class="mb-0">Accede al sistema de evaluaciones de desempeño (Talentos en 360).</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-telephone"></i> <strong>Línea de Apoyo</strong></h6>
                            <p class="mb-0">Contacto directo para soporte y asistencia telefónica.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-bank"></i> <strong>Mi Caja Digital</strong></h6>
                            <p class="mb-0">Acceso al portal de la Caja de Seguro Social de Panamá.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mi Espacio - Información Personal -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-person-badge section-icon"></i> Mi Espacio - Información Personal</h3>
                
                <div class="feature-item mt-3">
                    <h6><i class="bi bi-person"></i> <strong>Mis Datos</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Consulta y actualiza tu información personal.</p>
                    <p class="mb-1"><strong>Funcionalidades:</strong></p>
                    <ul>
                        <li>Ver tu información personal completa</li>
                        <li>Actualizar datos de contacto (teléfono, email, dirección)</li>
                        <li>Consultar información laboral (departamento, cargo, fecha de ingreso)</li>
                        <li>Ver estado de tu empleado</li>
                    </ul>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-file-text"></i> <strong>Solicitar Carta</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Solicita cartas de trabajo o constancias laborales.</p>
                    <p class="mb-1"><strong>Pasos para solicitar:</strong></p>
                    <ol>
                        <li>Haz clic en "Solicitar Carta"</li>
                        <li>Selecciona el tipo de carta que necesitas</li>
                        <li>Completa la descripción o motivo de la solicitud</li>
                        <li>Adjunta documentos si es necesario</li>
                        <li>Envía la solicitud</li>
                    </ol>
                    <p class="mb-0"><strong>Nota:</strong> Recibirás una notificación cuando tu carta esté lista.</p>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-exclamation-triangle"></i> <strong>Solicitar Calamidad</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Solicita ayuda por situaciones de calamidad doméstica.</p>
                    <p class="mb-1"><strong>Pasos para solicitar:</strong></p>
                    <ol>
                        <li>Haz clic en "Solicitar Calamidad"</li>
                        <li>Describe la situación de calamidad</li>
                        <li>Adjunta documentos de respaldo (si aplica)</li>
                        <li>Envía la solicitud</li>
                    </ol>
                    <p class="mb-0"><strong>Nota:</strong> Esta solicitud será revisada por RRHH.</p>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-tshirt"></i> <strong>Solicitar Uniforme</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Solicita uniformes de trabajo.</p>
                    <p class="mb-1"><strong>Pasos para solicitar:</strong></p>
                    <ol>
                        <li>Haz clic en "Solicitar Uniforme"</li>
                        <li>Selecciona el tipo de uniforme que necesitas</li>
                        <li>Especifica talla y cantidad</li>
                        <li>Envía la solicitud</li>
                    </ol>
                    <p class="mb-0"><strong>Nota:</strong> Los uniformes se entregan según disponibilidad.</p>
                </div>
            </div>
        </div>

        <!-- Gestión de Tiempo y Ausencias -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-calendar-check section-icon"></i> Gestión de Tiempo y Ausencias</h3>
                
                <div class="feature-item mt-3">
                    <h6><i class="bi bi-calendar3"></i> <strong>Mis Vacaciones</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Consulta tus días de vacaciones disponibles y solicita tus vacaciones.</p>
                    <p class="mb-1"><strong>Funcionalidades:</strong></p>
                    <ul>
                        <li>Ver días de vacaciones acumulados</li>
                        <li>Ver historial de solicitudes de vacaciones</li>
                        <li>Solicitar nuevas vacaciones</li>
                        <li>Ver el estado de tus solicitudes (pendiente, aprobada, rechazada)</li>
                    </ul>
                    <p class="mb-1"><strong>Pasos para solicitar vacaciones:</strong></p>
                    <ol>
                        <li>Haz clic en "Mis Vacaciones"</li>
                        <li>Selecciona "Solicitar Vacaciones"</li>
                        <li>Elige las fechas de inicio y fin</li>
                        <li>Agrega una descripción o comentario (opcional)</li>
                        <li>Selecciona tu supervisor</li>
                        <li>Envía la solicitud</li>
                    </ol>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-clock-history"></i> <strong>Solicitar Permiso</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Solicita permisos para ausentarte del trabajo.</p>
                    <p class="mb-1"><strong>Tipos de permisos disponibles:</strong></p>
                    <ul>
                        <li><strong>Vacaciones:</strong> Para días de descanso planificados</li>
                        <li><strong>Enfermedad:</strong> Para ausencias por enfermedad</li>
                        <li><strong>Duelo:</strong> Para ausencias por fallecimiento de familiar</li>
                        <li><strong>Tiempo sin pago:</strong> Para ausencias no pagadas</li>
                        <li><strong>Compensatorio:</strong> Para días compensatorios</li>
                        <li><strong>Flex day:</strong> Para días flexibles</li>
                        <li><strong>Cita Médica:</strong> Para citas médicas</li>
                        <li><strong>Teletrabajo:</strong> Para trabajar desde casa</li>
                    </ul>
                    <p class="mb-1"><strong>Pasos para solicitar permiso:</strong></p>
                    <ol>
                        <li>Haz clic en "Solicitar Permiso"</li>
                        <li>Selecciona el tipo de licencia</li>
                        <li>Elige las fechas de inicio y fin</li>
                        <li>Agrega una descripción del motivo</li>
                        <li>Adjunta un archivo si es necesario (opcional)</li>
                        <li>Selecciona tu supervisor</li>
                        <li>Envía la solicitud</li>
                    </ol>
                    <p class="mb-0"><strong>Nota:</strong> Recibirás una notificación cuando tu supervisor apruebe o rechace la solicitud.</p>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-hospital"></i> <strong>Mis Incapacidades</strong></h6>
                    <p class="mb-2"><strong>Descripción:</strong> Consulta y gestiona tus incapacidades médicas.</p>
                    <p class="mb-1"><strong>Funcionalidades:</strong></p>
                    <ul>
                        <li>Ver historial de incapacidades</li>
                        <li>Registrar nuevas incapacidades</li>
                        <li>Adjuntar documentos médicos</li>
                        <li>Ver el estado de tus incapacidades</li>
                    </ul>
                    <p class="mb-1"><strong>Pasos para registrar una incapacidad:</strong></p>
                    <ol>
                        <li>Haz clic en "Mis Incapacidades"</li>
                        <li>Selecciona "Registrar Incapacidad"</li>
                        <li>Completa la información requerida</li>
                        <li>Adjunta el documento médico</li>
                        <li>Envía la solicitud</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Beneficios -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-gift section-icon"></i> Mis Beneficios</h3>
                <p class="card-text">En esta sección encontrarás información sobre todos los beneficios disponibles para colaboradores:</p>
                
                <div class="feature-item mt-3">
                    <h6><i class="bi bi-heart-pulse"></i> <strong>Beneficios de Salud</strong></h6>
                    <p class="mb-0">Descuentos y convenios con clínicas, ópticas y servicios de salud.</p>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-bag"></i> <strong>Beneficios Comerciales</strong></h6>
                    <p class="mb-0">Descuentos en restaurantes, hoteles, tiendas y servicios diversos.</p>
                </div>

                <div class="feature-item mt-3">
                    <h6><i class="bi bi-info-circle"></i> <strong>Información</strong></h6>
                    <p class="mb-0">Cada beneficio incluye detalles sobre cómo utilizarlo, requisitos y contactos.</p>
                </div>
            </div>
        </div>

        <!-- Consejos y Buenas Prácticas -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-lightbulb section-icon"></i> Consejos y Buenas Prácticas</h3>
                
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> <strong>Solicitudes con anticipación</strong></h6>
                    <p class="mb-0">Para vacaciones y permisos, es recomendable solicitarlos con al menos una semana de anticipación.</p>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle"></i> <strong>Documentos adjuntos</strong></h6>
                    <p class="mb-0">Asegúrate de adjuntar todos los documentos necesarios cuando solicites permisos o incapacidades.</p>
                </div>

                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle"></i> <strong>Revisa el estado</strong></h6>
                    <p class="mb-0">Revisa regularmente el estado de tus solicitudes para estar al tanto de su aprobación.</p>
                </div>

                <div class="alert alert-primary">
                    <h6><i class="bi bi-envelope"></i> <strong>Notificaciones</strong></h6>
                    <p class="mb-0">Recibirás notificaciones por correo electrónico cuando tus solicitudes sean aprobadas o rechazadas.</p>
                </div>
            </div>
        </div>

        <!-- Soporte -->
        <div class="card section-card">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-headset section-icon"></i> Soporte y Ayuda</h3>
                <p class="card-text">Si tienes problemas o preguntas sobre el uso del sistema:</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-envelope"></i> <strong>Correo Electrónico</strong></h6>
                            <p class="mb-0">Contacta a RRHH: <a href="mailto:rrhh@grupopcr.com.pa">rrhh@grupopcr.com.pa</a></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-item">
                            <h6><i class="bi bi-telephone"></i> <strong>Línea de Apoyo</strong></h6>
                            <p class="mb-0">Llama al: <a href="tel:+50763796524">+507 6379-6524</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para volver -->
    <a href="<?php echo BASE_URL_CONTROLLER; ?>/AuthController.php" class="btn btn-primary back-btn rounded-circle" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-arrow-left fs-4"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
