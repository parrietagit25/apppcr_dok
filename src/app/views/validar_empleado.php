<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verificación de Carnet</title>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <style>
    body { font-family: Arial; text-align: center; padding: 20px; background: #f9f9f9; }
    .carnet {
        background: #fff; padding: 20px; border-radius: 10px;
        max-width: 350px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .carnet img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; }
    .footer { margin-top: 20px; font-size: 0.8em; color: #666; }
    #reader { width: 300px; margin: 20px auto; display: none; }
    .btn { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
  </style>
</head>
<body>

<h2>Validación de Carnet</h2>

<?php if (!empty($empleadoVerificado)): ?>
  <div class="carnet">
    <h1>GRUPO <b>PCR</b></h1>
    <img src="<?php echo BASE_URL_IMAGE . 'imagen_carnet/' . substr($empleadoVerificado['codigo_empleado'], 2) . '.jpeg'; ?>" alt="Foto">
    <h3><?php echo $empleadoVerificado['nombre'] . ' ' . $empleadoVerificado['apellido']; ?></h3>
    <p><b>Código:</b> <?php echo $empleadoVerificado['codigo_empleado']; ?></p>
    <p><b>Departamento:</b> <?php echo $empleadoVerificado['departamento']; ?></p>
    <p><b>Sangre:</b> <?php echo $empleadoVerificado['sangre']; ?></p>
    <div class="footer">Grupo PCR<br>Líderes Movilizando Panamá</div>
  </div>
<?php elseif (isset($_GET['codigo'])): ?>
  <div style="color: red; margin: 20px;">❌ Código no encontrado</div>
<?php endif; ?>

<!-- Escaneo -->
<button class="btn" onclick="levantarCamara()">📷 Escanear Carnet</button>
<div id="reader"></div>

<script>
function levantarCamara() {
    document.getElementById("reader").style.display = "block";
    const scanner = new Html5Qrcode("reader");

    scanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        qrCodeMessage => {
            scanner.stop();
            window.location.href = `MainController.php?verificar_carnet=1&codigo=${encodeURIComponent(qrCodeMessage)}`;
        },
        errorMessage => { /* opcional */ }
    ).catch(err => {
        alert("No se pudo acceder a la cámara: " + err);
    });
}
</script>

</body>
</html>
