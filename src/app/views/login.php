<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['code'])) {
    header("Location: main.php");
    exit;
}
?>
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
    <title>Login - GrupoPCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .login-btn {
            background-color: #003366;
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
            color: #0d6efd;
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
        if (isset($mensaje)){
             echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>'.$mensaje.'</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                   </div>';
        }elseif (isset($_GET['msg'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>'.$_GET['msg'].'</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                   </div>';
        }
    ?>
    <img src="<?php echo BASE_URL_IMAGE; ?>logo/1.png" alt="" width="300">
    <form action="" method="POST">
        <div class="form-group">
            <input type="text" name="code" placeholder="Código de Colaborador" class="form-control prefijo">
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" class="form-control">
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit" class="btn login-btn mt-3">Login</button>
    </form>

    <div class="link-container">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RegcolaController.php?reg_col=1">Registrar Colaborador</a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RegcolaController.php?restore_pass=1">Recuperar Contraseña</a>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        const input = document.querySelector(".prefijo");

        // Siempre inicia con 00
        input.value = "00";

        // Coloca el cursor después del 00 al hacer clic
        input.addEventListener("focus", function () {
            if (input.value.length <= 2) {
            input.setSelectionRange(2, 2);
            }
        });

        // Evita que se borre el prefijo
        input.addEventListener("keydown", function (e) {
            // Previene el borrado del 00
            if ((input.selectionStart <= 2) &&
                (e.key === "Backspace" || e.key === "Delete")) {
            e.preventDefault();
            }
        });

        // Asegura que el valor siempre comience con 00
        input.addEventListener("input", function () {
            if (!input.value.startsWith("00")) {
            input.value = "00" + input.value.replace(/^0+/, '');
            }
        });
    </script>
</body>
</html>
