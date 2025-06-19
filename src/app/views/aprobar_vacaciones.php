<?php
if (!isset($_GET['codigo_empleado'])) {
    die("Error: Acceso restringido.");
}

include_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include_once __DIR__ . '/../../config/config.php';

class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error en la conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

    function enviar_correo($email, $mail_copia, $asunto, $mensaje){

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8'; 

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-mail.outlook.com'; // Cambia esto según tu proveedor
            $mail->SMTPAuth = true;
            $mail->Username = 'notificaciones@grupopcr.com.pa';
            $mail->Password = EMAIL_GLOBAL;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('notificaciones@grupopcr.com.pa', 'PCR notificaciones');
            $mail->addAddress($email);
            //$mail->addCC('rrhh@grupopcr.com.pa', $mail_copia);
            foreach ($mail_copia as $cc) {
                $mail->addCC($cc);
            }

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            echo '<br>Pasando 1';
            $mail->send();
            //return 'Correo enviado correctamente';
        } catch (Exception $e) {
            return "Error al enviar el correo: {$mail->ErrorInfo}";
        } 
    }

    function get_email_permiso($id_permiso) {
        $pdo = Database::connect(); // ✅ obtener conexión dentro de la función
        $stmt = $pdo->prepare("SELECT e.email, e.nombre, e.apellido 
                       FROM solicitud_permiso sp 
                       INNER JOIN empleados e 
                           ON CONVERT(sp.code USING utf8mb4) = CONVERT(e.codigo_empleado USING utf8mb4)
                       WHERE sp.id = :id_permiso");
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


$pdo = Database::connect();
$code = $_GET['codigo_empleado'];
$array_datos = [];

// Obtener ID del permiso (solo el primero en caso de múltiples)
$id_permiso = $array_datos[0]['id'] ?? 0;

// Procesar acción POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_post = $_POST['id_permiso'] ?? null;

    if ($id_post && in_array($accion, ['aprobar', 'declinar'])) {
        $nuevo_estado = $accion === 'aprobar' ? 2 : 3;
        $stmt = $pdo->prepare("UPDATE solicitud_permiso SET stat = :stat WHERE id = :id");
        $stmt->bindParam(':stat', $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_post, PDO::PARAM_INT);
        $stmt->execute();

        $mensaje = $accion === 'aprobar' ? 'Solicitud aprobada con éxito.' : 'Solicitud declinada.';

        $get_email_colab = get_email_permiso($id_permiso);

        if ($get_email_colab && is_array($get_email_colab)) {
            $nombre_comple = $get_email_colab['nombre'] . ' ' . $get_email_colab['apellido'];
            $email = $get_email_colab['email'];

            $mensaje_mail = 'Estimado ' . $nombre_comple . '<br> 
            Ha solicitado un permiso tipo vacaciones.<br>
            La respuesta de su jefe directo fue: <strong>' . $accion . '</strong><br>';

            $copiacoo = ["pedroarrieta25@hotmail.com"];

            enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje_mail);
        }
        echo 'Pasando 0';
        header("Location: ?codigo_empleado=$code&nombre_completo={$_GET['nombre_completo']}&fecha_desde={$_GET['fecha_desde']}&fecha_hasta={$_GET['fecha_hasta']}&cantidad_dias={$_GET['cantidad_dias']}&msg=" . urlencode($mensaje));
        exit;
    }
}

$stmt_frase = $pdo->prepare("SELECT * FROM solicitud_permiso WHERE code = :code AND stat = 1 AND tipo_licencia = 'Vacaciones'");
$stmt_frase->bindParam(':code', $code, PDO::PARAM_STR);
$stmt_frase->execute();
$array_datos = $stmt_frase->fetchAll(PDO::FETCH_ASSOC);

if (empty($array_datos)) {
    echo '<div style="background-color:#003399; color: white; padding: 40px; text-align: center; font-size: 1.5rem; height: 100vh;">
            Esta solicitud ya fue procesada.
          </div>';
    exit; // ⚠️ Detiene la ejecución del resto del HTML
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background-color:rgb(168, 179, 202);
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .link-container {
            margin-top: 15px;
            text-align: center;
        }
        .link-container a {
            display: block;
            margin-top: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .link-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-container">
    <?php 
    if (isset($_GET['msg'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>'.htmlspecialchars($_GET['msg']).'</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
    ?>
    <img src="/src/public/images/ico_login.png" alt="" width="300">
    <p>El colaborador: <strong><?= htmlspecialchars($_GET['nombre_completo'] ?? '') ?></strong> <br> con código: <strong><?= htmlspecialchars($_GET['codigo_empleado'] ?? '') ?></strong></p>
    <p>Ha solicitado vacaciones desde: <strong><?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?></strong> hasta <strong><?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?></strong></p>
    <br>
    <p>Cantidad de días: <strong><?= htmlspecialchars($_GET['cantidad_dias'] ?? '') ?></strong></p>

    <!-- Botones para abrir modales -->
    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalAprobar">
        Aprobar
    </button>
    <br><br>
    <button type="button" class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#modalDeclinar">
        Declinar
    </button>
</div>

<!-- Modal Aprobar -->
<div class="modal fade" id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="aprobar">
        <input type="hidden" name="id_permiso" value="<?= htmlspecialchars($array_datos[0]['id'] ?? '') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAprobarLabel">Confirmar aprobación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de aprobar esta solicitud?
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Sí, aprobar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Declinar -->
<div class="modal fade" id="modalDeclinar" tabindex="-1" aria-labelledby="modalDeclinarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="accion" value="declinar">
        <input type="hidden" name="id_permiso" value="<?= htmlspecialchars($array_datos[0]['id'] ?? '') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDeclinarLabel">Confirmar rechazo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de declinar esta solicitud?
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Sí, declinar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
