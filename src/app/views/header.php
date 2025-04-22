<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d6efd">
    <link rel="manifest" href="/manifest.json">
<!-- iphone -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>APP PCR</title>
    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="manifest" href="manifest.json">
    <style>
        .carnet {
            width: 300px;
            border: 2px solid #333;
            border-radius: 10px;
            background: #fff;
            text-align: center;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }
        .carnet img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .carnet h3 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }
        .carnet p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #555;
        }
        .carnet .footer {
            margin-top: 20px;
            font-size: 0.8em;
            color: #777;
        }

        /* main */

        body {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Encabezado */
        .header {
            background-color: #002f6c; /* Azul oscuro */
            color: white;
            padding: 20px;
            text-align: left;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            position: relative;
            margin-top: -5px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        /* navbar */

        .navbar {
            background-color: #002f6c !important; /* Azul oscuro */
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: white !important;
        }

        .navbar .nav-link:hover {
            color: #cccccc !important; /* Gris claro al pasar el cursor */
        }

        .navbar-toggler {
            border-color: white !important;
        }

        .navbar-toggler-icon {
            filter: invert(1); /* Hace el icono de hamburguesa blanco */
        }

    </style>
    <style>
        body {
            background: linear-gradient(to right, #FFDEE9, #B5FFFC);
            font-family: Arial, sans-serif;
        }
        .card {
            border-radius: 10px;
            border: none;
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .date-box {
            background: #FF7F50;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            min-width: 60px;
        }
        .birthday-info h5 {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PCR App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php">Inicio</a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" href="#">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL_LINK; ?>/salir.php">Salir</a> 
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="header">
        <h1>Grupo <b>PCR</b></h1>
        <p>Hola <?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></p>
        <!--<span class="notification-icon">
            <i class="bi bi-bell"></i>
            <span class="badge"></span>
        </span>-->
    </div>
