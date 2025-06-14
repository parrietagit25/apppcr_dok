<?php
require_once __DIR__ . '/../config/conexion.php'; // Ajusta la ruta si es distinta

$codigo = $_GET['codigo'] ?? null;
$empleado = null;
$noEncontrado = false;

if ($codigo) {
    $stmt = $pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = ?");
    $stmt->execute([$codigo]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$empleado) {
        $noEncontrado = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Carnet</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            text-align: center;
            padding: 30px;
        }
        .carnet {
            width: 300px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .carnet img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .carnet h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .carnet p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .footer {
            margin-top: 15px;
            font-size: 0.8em;
            color: #777;
        }
        #reader {
            width: 300px;
            margin: 20px auto;
            display: none;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .not-found {
            background: #fff3f3;
            color: #a00;
            padding: 15px;
            margin: auto;
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255,0,0,0.1);
        }
    </style>
</head>
<body>

<h2>Verificación de Carnet</h2>

<?php if ($empleado): ?>
    <div class="carnet">
        <h1>GRUPO <b>PCR</b></h1>
        <img src="<?php echo '../public/imagen_carnet/' . substr($empleado['codigo_empleado'], 2) . '.jpeg'; ?>" alt="Foto del empleado">
        <h3><?php echo $empleado['nombre'] . ' ' . $empleado['apellido']; ?></h3>
        <p><b>Código:</b> <?php echo $empleado['codigo_empleado']; ?></p>
        <p><b>Departamento:</b> <?php echo $empleado['departamento']; ?></p>
        <p><b>Sangre:</b> <?php echo $empleado['sangre']; ?></p>
        <div class="footer">
            <p>Grupo PCR</p>
            <p>Líderes Movilizando Panamá</p>
        </div>
    </div>
<?php elseif ($noEncontrado): ?>
    <div class="not-found">
        <p>❌ Usuario no encontrado.</p>
    </div>
<?php endif; ?>

<!-- Botón para escanear QR -->
<button class="btn" onclick="levantarCamara()">📷 Escanear QR</button>

<!-- Escáner QR -->
<div id="reader"></div>

<script>
function levantarCamara() {
    document.getElementById("reader").style.display = "block";

    const scanner = new Html5Qrcode("reader");
    scanner.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250
        },
        qrCodeMessage => {
            scanner.stop(); // Detener cámara
            window.location.href = "validar_empleado.php?codigo=" + encodeURIComponent(qrCodeMessage);
        },
        errorMessage => {
            // Opcional: console.log(errorMessage);
        }
    ).catch(err => {
        alert("Error al acceder a la cámara: " + err);
    });
}
</script>

</body>
</html>
