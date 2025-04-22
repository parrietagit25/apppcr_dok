<?php
//session_start();
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">
    <div class="modal fade" id="frase_semana" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="solicitudModalLabel">Mantenimiento - Evaluacion de desempeño</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Iconos de funcionalidades -->
    <div class="row text-center mb-4">

        <div class="col-6">
            <div class="">
            </div>
        </div>

        <div class="row mt-4">
            <h5 class="text-center">Mis Evaluaciones</h5>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Titulo</th>
                        <th>Departamento</th>
                        <th>Link</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $estados;
                    //if (!empty($array_datos) && is_array($array_datos)){
                        
                        foreach ($seleccionar_evaluacion_departamento as $key => $value) { 
                            
                            if ($value['stat'] == 1) {
                                $estados = 'Activa';
                            }else {
                                $estados = 'No activa';
                            }
                            
                            ?>
                        <tr>
                            <td><?php echo $value['titulo']; ?></td>
                            <td><?php echo $value['departamento']; ?></td>
                            <td><a href="<?php echo $value['link']; ?>" target="_BLANK">Link</a></td>
                            <td><?php echo $estados; ?></td>
                        </tr>
                        <?php } ?>
                    <?php // }else{ ?>
                        <!--<p>No hay datos disponibles.</p> -->
                    <?php // } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
