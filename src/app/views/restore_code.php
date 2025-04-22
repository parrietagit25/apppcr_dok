<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0d6efd">
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

        #reg_col {
            display: block; 
            margin: 10px auto; 
            text-align: center; 
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Animación de giro */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Centrar el loader */
        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
            /*height: 100vh;*/
            height: 50px;
            background-color: #f8f9fa;
        }

        #buscar_user{
            height: 60px;
        }

        #restore_col{
            display: block; 
            margin: 10px auto; 
            text-align: center; 
            margin-top: 60px !important
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

    </nav>

    <div class="header">
        <h1>Grupo <b>PCR</b></h1>
    </div>


<div class="container mt-4">

    <!-- Slider con frase -->
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-toggle="modal" data-bs-target="#frase_semana">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Recuperar Contraseña</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    Modal frase de la semana
    -->

    <!-- Iconos de funcionalidades -->
    <div class="row text-center mb-4">

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="restore_code" placeholder="Código de Colaborador" class="form-control prefijo" required id="reg_col_code" oninput="buscar_code_rest_pass()">
            </div>
            <div class="form-group" id="buscar_user">
            </div>
            <button type="submit" class="btn btn-primary mt-3" id="restore_col" name="restore_col">Recuperar Contraseña</button>
            <br>
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/AuthController.php" class="btn btn-primary mt-3">Volver al login</a>
            <input type="hidden" id="email" name="email" value="">
        </form>
       
    </div>

</div>



<?php include __DIR__ . '/footer.php'; ?>
