<?php
require_once __DIR__ . '/../core/Database.php';

$pdo = Database::connect();

// Consulta solo vacaciones
$sql = "
    SELECT 
        sp.id,
        e.codigo_empleado,
        e.nombre,
        e.apellido,
        sp.fecha_log,
        sp.descripcion,
        sp.tipo_licencia,
        sp.fecha_inicio,
        sp.fecha_fin,
        sp.stat,
        sp.respuesta_jefe,
        sp.comentario_jefe,
        sp.archivo_adjunto
    FROM solicitud_permiso sp
    INNER JOIN empleados e 
        ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
           CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
    WHERE sp.tipo_licencia = 'Vacaciones'
    ORDER BY sp.fecha_log DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$vacaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Vacaciones</title>
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Solicitudes de Vacaciones</h2>
    <a href="exportar_vacaciones_excel.php" class="btn btn-success mb-3">Exportar a Excel</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha Solicitud</th>
                <th>Descripción</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Respuesta Jefe</th>
                <th>Comentario Jefe</th>
                <th>Archivo</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($vacaciones as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['id']) ?></td>
                <td><?= htmlspecialchars($v['codigo_empleado']) ?></td>
                <td><?= htmlspecialchars($v['nombre']) ?></td>
                <td><?= htmlspecialchars($v['apellido']) ?></td>
                <td><?= htmlspecialchars($v['fecha_log']) ?></td>
                <td><?= htmlspecialchars($v['descripcion']) ?></td>
                <td><?= htmlspecialchars($v['fecha_inicio']) ?></td>
                <td><?= htmlspecialchars($v['fecha_fin']) ?></td>
                <td><?= htmlspecialchars($v['stat']) ?></td>
                <td><?= htmlspecialchars($v['respuesta_jefe']) ?></td>
                <td><?= htmlspecialchars($v['comentario_jefe']) ?></td>
                <td>
                    <?php if ($v['archivo_adjunto']): ?>
                        <a href="/ruta/archivos/<?= urlencode($v['archivo_adjunto']) ?>" target="_blank">Ver</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
