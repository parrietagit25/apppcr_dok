<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="input-group mb-3"></div>

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Verificación de Uniformes</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Uniformes</h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Talla</th>
                        <th>Cant.</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($uniformes)) {
                        foreach ($uniformes as $row) {
                            $estado_texto = match($row['stat']) {
                                1 => 'Solicitado',
                                2 => 'En Proceso',
                                3 => 'Entregado',
                                default => 'Desconocido'
                            };
                            
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['codigo_empleado']) . "</td>
                                    <td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>
                                    <td>" . htmlspecialchars(ucfirst($row['tipo'])) . "</td>
                                    <td class='text-center'>" . htmlspecialchars($row['talla']) . "</td>
                                    <td class='text-center'><strong>" . htmlspecialchars($row['cantidad'] ?? 1) . "</strong></td>
                                    <td>
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#modalUniforme{$row['id']}'>
                                            " . htmlspecialchars($estado_texto) . "
                                        </a>
                                    </td>
                                  </tr>";

                            // Modal por solicitud
                            echo "
                            <div class='modal fade' id='modalUniforme{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header bg-primary text-white'>
                                            <h5 class='modal-title' id='modalLabel{$row['id']}'>Detalle de Solicitud de Uniforme</h5>
                                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <form action='' method='POST'>
                                                <input type='hidden' name='uniforme_id' value='{$row['id']}'>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Código de Empleado</b></label>
                                                    <p>{$row['codigo_empleado']}</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Colaborador</b></label>
                                                    <p>{$row['nombre']} {$row['apellido']}</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Departamento</b></label>
                                                    <p>{$row['nombre_departamento']}</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Cargo</b></label>
                                                    <p>{$row['nombre_cargo']}</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Tipo de Uniforme</b></label>
                                                    <p>" . ucfirst($row['tipo']) . "</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Talla</b></label>
                                                    <p>{$row['talla']}</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Cantidad</b></label>
                                                    <p class='fs-5'><strong>" . ($row['cantidad'] ?? 1) . "</strong> unidad(es)</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Fecha de Solicitud</b></label>
                                                    <p>" . date('d/m/Y H:i', strtotime($row['fecha_log'])) . "</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Observaciones</b></label>
                                                    <p>" . (!empty($row['observacion']) ? htmlspecialchars($row['observacion']) : 'Sin observaciones') . "</p>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Estado Actual</b></label>
                                                    <p class='text-primary fw-bold'>$estado_texto</p>
                                                </div>
                                                
                                                <div class='mb-3'>
                                                    <label class='form-label'><b>Cambiar Estado</b></label>
                                                    <select name='nuevo_estado' class='form-select' required>
                                                        <option value=''>-- Seleccionar --</option>
                                                        <option value='1' " . ($row['stat'] == 1 ? 'selected' : '') . ">Solicitado</option>
                                                        <option value='2' " . ($row['stat'] == 2 ? 'selected' : '') . ">En Proceso</option>
                                                        <option value='3' " . ($row['stat'] == 3 ? 'selected' : '') . ">Entregado</option>
                                                    </select>
                                                </div>
                                                
                                                <div class='d-grid gap-2'>
                                                    <button type='submit' class='btn btn-primary' name='actualizar_uniforme'>
                                                        <i class='bi bi-check-circle'></i> Actualizar Estado
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No hay solicitudes registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Navegación inferior -->
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
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/footer.php'; ?>
