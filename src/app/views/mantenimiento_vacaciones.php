<?php
// app/views/solicitudes_registradas.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <h2>Solicitudes de Vacaciones</h2>
    <a href="https://apppcr.net/app/views/exportar_vacaciones_excel.php" target="_BLANK" class="btn btn-success mb-3">Exportar a Excel</a>
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
            </tr>
        </thead>
        <tbody>
        <?php foreach ($vacaciones as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['codigo_empleado'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['apellido'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['fecha_log'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['descripcion'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['fecha_inicio'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['fecha_fin'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['stat'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['respuesta_jefe'] ?? '') ?></td>
                <td><?= htmlspecialchars($v['comentario_jefe'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
