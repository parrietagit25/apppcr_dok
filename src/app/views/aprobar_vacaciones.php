<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d6efd">
    <!-- iphone -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Aprobar vacaciones - Gente PCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #003399;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            /*background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .login-btn {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px;
            padding: 10px;
            width: 100%;
        }
        .link-container {
            margin-top: 15px;
            text-align: center;
        }
        .link-container a {
            display: block;
            margin-top: 5px;
            color:rgb(255, 255, 255);
            text-decoration: none;
            font-weight: bold;
        }
        .link-container a:hover {
            text-decoration: underline;
        }
    </style>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <script src="/comp/app.js"></script>
</head>
<body>
<div class="login-container">
    <?php 

        if (isset($_GET['msg'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>'.$_GET['msg'].'</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                   </div>';
        }
    ?>
    <img src="/src/public/images/ico_login.png" alt="" width="300">
    <form action="" method="POST">
        <div class="form-group">
            <button type="submit" class="btn btn-primary mt-3">Aprobar</button>
        </div>
        <br>
        <div class="form-group">
            <button type="submit" class="btn btn-danger mt-3">Declinar</button>
        </div>
    </form>

    <div class="link-container">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RegcolaController.php?reg_col=1">Registrar Colaborador</a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RegcolaController.php?restore_pass=1">Recuperar Contraseña</a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
