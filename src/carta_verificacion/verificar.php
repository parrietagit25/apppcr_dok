<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/DatabaseExternal.php';
require_once __DIR__ . '/CartaVerificacionService.php';

// Obtener token de la URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    mostrarError("Token de verificación no proporcionado");
    exit;
}

// Inicializar servicio
$service = new CartaVerificacionService();

// Conectar a BD externa
try {
    $db = DatabaseExternal::getInstance();
    $pdo = $db->getConnection();

    // Buscar carta por token (no desencriptamos, buscamos directamente el token)
    $stmt = $pdo->prepare("SELECT * FROM cartas_trabajo_verificacion WHERE token_qr = :token");
    $stmt->execute([':token' => $token]);
    $carta = $stmt->fetch();

    if (!$carta) {
        mostrarError("Carta de trabajo no encontrada");
        exit;
    }

    // Verificar estado
    if ($carta['estado'] !== 'activa') {
        mostrarError("Esta carta ha sido " . $carta['estado'] . " y ya no es válida", $carta);
        exit;
    }

    // Verificar expiración
    if ($carta['fecha_expiracion'] && strtotime($carta['fecha_expiracion']) < time()) {
        mostrarError("Esta carta de trabajo ha expirado", $carta);
        exit;
    }

    // Obtener deducciones si aplica
    $deducciones = [];
    if ($carta['incluye_desglose_salarial']) {
        $stmt_ded = $pdo->prepare("SELECT * FROM cartas_deducciones WHERE carta_id = :id ORDER BY orden ASC");
        $stmt_ded->execute([':id' => $carta['id']]);
        $deducciones = $stmt_ded->fetchAll();
    }

    // Registrar verificación en log
    $stmt_log = $pdo->prepare("INSERT INTO cartas_verificaciones_log (
        carta_id, token_usado, ip_verificador, user_agent, resultado, fecha_verificacion
    ) VALUES (
        :carta_id, :token, :ip, :user_agent, 'exitosa', NOW()
    )");
    $stmt_log->execute([
        ':carta_id' => $carta['id'],
        ':token' => $token,
        ':ip' => $service->obtenerIPCliente(),
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    // Actualizar contador de verificaciones
    $stmt_update = $pdo->prepare("UPDATE cartas_trabajo_verificacion 
        SET total_verificaciones = total_verificaciones + 1, ultima_verificacion = NOW() 
        WHERE id = :id");
    $stmt_update->execute([':id' => $carta['id']]);

    // Construir ruta de la foto del colaborador
    $codigo_empleado = str_pad($carta['codigo_empleado'], 4, '0', STR_PAD_LEFT); // Asegurar 4 dígitos
    
    // Buscar foto en diferentes extensiones posibles
    $extensiones = ['jpeg', 'jpg', 'png', 'gif'];
    $foto_url = null;
    
    foreach ($extensiones as $ext) {
        $foto_path = __DIR__ . '/fotos/' . $codigo_empleado . '.' . $ext;
        if (file_exists($foto_path)) {
            $foto_url = '/carta/fotos/' . $codigo_empleado . '.' . $ext;
            break;
        }
    }
    
    // Verificar si existe la foto
    $tiene_foto = ($foto_url !== null);
    
    // Mostrar página de verificación exitosa
    mostrarVerificacionExitosa($carta, $deducciones, $tiene_foto ? $foto_url : null);

} catch (Exception $e) {
    error_log("Error en verificación: " . $e->getMessage());
    mostrarError("Error al procesar la verificación. Por favor, intente más tarde.");
}

function mostrarError($mensaje, $carta = null) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error de Verificación - Automarket</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 30px; }
            .logo { width: 200px; margin-bottom: 20px; }
            .error-icon { font-size: 60px; color: #dc3545; margin-bottom: 20px; }
            h1 { color: #dc3545; font-size: 24px; margin-bottom: 20px; }
            p { color: #666; line-height: 1.6; margin-bottom: 15px; }
            .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="error-icon">⚠️</div>
                <h1>Error de Verificación</h1>
            </div>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
            <?php if ($carta): ?>
                <p><small>Código de referencia: <?php echo htmlspecialchars($carta['codigo_empleado']); ?></small></p>
            <?php endif; ?>
            <div class="footer">
                <p><strong><?php echo EMPRESA_NOMBRE; ?></strong></p>
                <p>Si considera que esto es un error, por favor contacte al departamento de RRHH</p>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function mostrarVerificacionExitosa($carta, $deducciones, $foto_url = null) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificación de Carta de Trabajo - Automarket</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 800px; margin: 20px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #28a745; padding-bottom: 20px; }
            .verified-icon { font-size: 60px; color: #28a745; margin-bottom: 20px; }
            h1 { color: #28a745; font-size: 28px; margin-bottom: 10px; }
            .subtitle { color: #666; font-size: 16px; }
            .info-section { margin: 30px 0; }
            .info-section h2 { color: #333; font-size: 20px; margin-bottom: 15px; border-left: 4px solid #007bff; padding-left: 15px; }
            .info-row { display: flex; padding: 10px 0; border-bottom: 1px solid #eee; }
            .info-label { font-weight: bold; color: #555; width: 200px; }
            .info-value { color: #333; flex: 1; }
            .deducciones-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
            .deducciones-table th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6; }
            .deducciones-table td { padding: 10px; border-bottom: 1px solid #dee2e6; }
            .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }
            .status-activa { background: #28a745; color: white; }
            .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 14px; }
            .alert-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
            @media print { body { background: white; } .container { box-shadow: none; } }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="verified-icon">✓</div>
                <h1>Carta de Trabajo Verificada</h1>
                <p class="subtitle">Este documento ha sido verificado correctamente</p>
            </div>

            <div class="alert-info">
                <strong>⚠️ Importante:</strong> Esta carta es válida y ha sido emitida oficialmente por <?php echo EMPRESA_NOMBRE; ?>. 
                Fecha de emisión: <?php echo date('d/m/Y', strtotime($carta['fecha_emision'])); ?>
            </div>

            <div class="info-section">
                <h2>Información del Colaborador</h2>
                
                <?php//if ($foto_url): ?>
                <div style="text-align: center; margin-bottom: 20px;">
                    <img width="250" src="<?php echo 'fotos/'.ltrim($carta['codigo_empleado'], '0').'.jpeg'; ?>" 
                         alt="Foto de <?php echo htmlspecialchars($carta['nombre'] . ' ' . $carta['apellido']); ?>">
                </div>
                <?php// endif; ?>
                
                <div class="info-row">
                    <div class="info-label">Nombre Completo:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['nombre'] . ' ' . $carta['apellido']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Código Empleado:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['codigo_empleado']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cédula:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['cedula']); ?></div>
                </div>
                <?php if ($carta['seguro_social']): ?>
                <div class="info-row">
                    <div class="info-label">Seguro Social:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['seguro_social']); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-section">
                <h2>Información Laboral</h2>
                <div class="info-row">
                    <div class="info-label">Cargo:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['cargo']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Ingreso:</div>
                    <div class="info-value"><?php echo date('d/m/Y', strtotime($carta['fecha_ingreso'])); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Empresa:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['empresa']); ?></div>
                </div>
            </div>

            <?php if ($carta['incluye_desglose_salarial'] && !empty($deducciones)): ?>
            <div class="info-section">
                <h2>Información Salarial</h2>
                <div class="info-row">
                    <div class="info-label">Salario Bruto:</div>
                    <div class="info-value">B/. <?php echo number_format($carta['salario_bruto'], 2); ?></div>
                </div>
                
                <h3 style="margin-top: 20px; font-size: 16px; color: #666;">Deducciones:</h3>
                <table class="deducciones-table">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th style="text-align: right;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_deducciones = 0;
                        foreach ($deducciones as $ded): 
                            $total_deducciones += $ded['monto'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ded['descripcion']); ?></td>
                            <td style="text-align: right;">B/. <?php echo number_format($ded['monto'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="font-weight: bold; background: #f8f9fa;">
                            <td>Total Deducciones</td>
                            <td style="text-align: right;">B/. <?php echo number_format($total_deducciones, 2); ?></td>
                        </tr>
                        <tr style="font-weight: bold; font-size: 16px; background: #e9ecef;">
                            <td>Salario Neto (Aproximado)</td>
                            <td style="text-align: right;">B/. <?php echo number_format($carta['salario_bruto'] - $total_deducciones, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="info-section">
                <h2>Detalles de la Carta</h2>
                <?php if ($carta['descripcion']): ?>
                <div class="info-row">
                    <div class="info-label">Propósito:</div>
                    <div class="info-value"><?php echo htmlspecialchars($carta['descripcion']); ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label">Fecha de Emisión:</div>
                    <div class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($carta['fecha_emision'])); ?></div>
                </div>
                <?php if ($carta['fecha_expiracion']): ?>
                <div class="info-row">
                    <div class="info-label">Fecha de Expiración:</div>
                    <div class="info-value"><?php echo date('d/m/Y', strtotime($carta['fecha_expiracion'])); ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo $carta['estado']; ?>">
                            <?php echo strtoupper($carta['estado']); ?>
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Verificaciones:</div>
                    <div class="info-value"><?php echo $carta['total_verificaciones']; ?> vez(ces)</div>
                </div>
            </div>

            <div class="footer">
                <p><strong><?php echo EMPRESA_NOMBRE; ?></strong></p>
                <p>Hash de verificación: <?php echo htmlspecialchars(substr($carta['hash_verificacion'], 0, 16)); ?>...</p>
                <p style="margin-top: 10px;">Este documento fue verificado el <?php echo date('d/m/Y H:i:s'); ?></p>
                <p style="margin-top: 15px; font-size: 12px;">
                    Para validar la autenticidad de este documento, escanee el código QR en la carta original<br>
                    o contacte al Departamento de Recursos Humanos de <?php echo EMPRESA_NOMBRE; ?>
                </p>
            </div>
            
            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; margin-top: 30px; text-align: center;">
                <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">
                    🔒 <strong>IMPORTANTE - VERIFICACIÓN DE AUTENTICIDAD:</strong><br>
                    <span style="font-size: 15px; font-weight: bold; color: #000;">grupopcr.com.pa</span> es el <strong>único dominio legítimo</strong> de <?php echo EMPRESA_NOMBRE; ?>.<br>
                    Si está visualizando esta carta en este dominio, puede <strong>confirmar su autenticidad</strong>.<br>
                    <span style="font-size: 12px; color: #666; margin-top: 5px; display: inline-block;">
                        Cualquier verificación fuera de este dominio oficial debe considerarse <strong>no válida</strong>.
                    </span>
                </p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

