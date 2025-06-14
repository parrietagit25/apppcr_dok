<?php
// validar_empleado.php

require_once __DIR__ . '/../config/conexion.php'; // Ajusta la ruta a tu archivo de conexión

$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
    echo "⚠️ Código no proporcionado.";
    exit;
}

// Buscar al empleado en la base de datos
$stmt = $pdo->prepare("SELECT nombre, apellido, departamento, sangre FROM empleados WHERE codigo_empleado = ?");
$stmt->execute([$codigo]);
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Validación de Empleado</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 40px;
      background-color: #f2f2f2;
    }
    .card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      max-width: 400px;
      margin: auto;
    }
    .card h2 {
      margin-bottom: 10px;
    }
    .card p {
      font-size: 16px;
      margin: 6px 0;
    }
    .status {
      margin-top: 20px;
      font-weight: bold;
      color: green;
    }
  </style>
</head>
<body>

<?php if ($empleado): ?>
  <div class="card">
    <h2><?php echo $empleado['nombre'] . ' ' . $empleado['apellido']; ?></h2>
    <p><b>Código:</b> <?php echo htmlspecialchars($codigo); ?></p>
    <p><b>Departamento:</b> <?php echo $empleado['departamento']; ?></p>
    <p><b>Tipo de Sangre:</b> <?php echo $empleado['sangre']; ?></p>
    <div class="status">✅ Empleado verificado</div>
  </div>
<?php else: ?>
  <div class="card">
    <h2>❌ Código no válido</h2>
    <p>No se encontró ningún colaborador con ese código.</p>
  </div>
<?php endif; ?>

</body>
</html>
