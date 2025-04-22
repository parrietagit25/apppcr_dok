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
                <div class="carousel slide mb-6" data-bs-ride="carousel" data-bs-toggle="modal" data-bs-target="#reg_eval">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="p-12 bg-light rounded">
                                <a href="#" class="btn btn-primary">
                                    <h5 class="fw-bold">Registrar Evaluacion </h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 
                Modal frase de la evaluaciones
                -->
                <div class="modal fade" id="reg_eval" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="solicitudModalLabel">Registrar Evaluacion de desempeño </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class='mb-3'>
                                        <p>Titulo de la Evaluacion</p>
                                        <textarea name="titulo_eva" class="form-control" style="margin:10px;"></textarea>
                                    </div>
                                    <div class='mb-3'>
                                        <p>Seleccionar Departamento</p>
                                        <select name="select_departamento" id="" class="form-control">
                                            <option value="">Seleccionar</option>
                                            <?php foreach ($evaluacion as $key => $value) { 
                                                
                                                    if ($value['nombre_departamento'] == '') {
                                                        continue;
                                                    }

                                                ?>
                                                    <option value="<?php echo $value['nombre_departamento']; ?>"><?php echo $value['nombre_departamento']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class='mb-3'>
                                        <p>Link de la Evaluacion</p>
                                        <textarea name="link_eval" class="form-control" style="margin:10px;"></textarea>
                                    </div>
                                    <br>
                                    <input type="submit" class="btn btn-primary" value="Registrar evaluacion" name="bo_reg_eval">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row mt-4">
            <h5 class="text-center">Evaluaciones Registradas </h5>
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
                    foreach ($seleccionar_evaluacion as $key => $value) { 
                        
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
