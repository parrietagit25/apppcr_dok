<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';

$apiKey = API_IA;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pregunta'])) {
    $pregunta = trim($_POST['pregunta']);

    $columnas = [
        'empleados' => ['codigo_empleado', 'nombre', 'apellido', 'departamento', 'puesto', 'correo', 'fecha_ingreso'],
        'empleado_log' => ['id', 'codigo_empleado', 'fecha_log', 'accion'],
        'calamidades' => ['id', 'codigo_empleado', 'tipo_calamidad', 'fecha'],
        'carta_trabajo' => ['id', 'codigo_empleado', 'fecha_solicitud', 'estatus'],
        'colaboradores_externos' => ['id', 'codigo', 'nombre', 'empresa', 'fecha_registro'],
        'encargados_colab' => ['id', 'codigo_empleado', 'nombre_encargado'],
        'incapacidad' => ['id', 'codigo_empleado', 'fecha_inicio', 'fecha_fin', 'tipo_incapacidad'],
        'solicitud_permiso' => ['id', 'codigo_empleado', 'fecha_inicio', 'fecha_fin', 'motivo', 'estatus']
    ];

    $prompt = "
Eres un experto en bases de datos MySQL. Solo responde con consultas SELECT.

Estas son las tablas disponibles:
- empleados
- empleado_log
- calamidades
- carta_trabajo
- colaboradores_externos
- encargados_colab
- incapacidad
- solicitud_permiso

La tabla principal es 'empleados', que contiene toda la información de los colaboradores.
La tabla 'empleado_log' contiene los accesos y registros de la app, relacionada con 'empleados' por la columna 'codigo_empleado'.

En general, todas las tablas están relacionadas con 'empleados' por la columna 'codigo_empleado' o 'codigo'.

⚠️ Usa alias como: empleados AS e, empleado_log AS l, calamidades AS ca, etc.
Y referencia columnas así: e.nombre, l.fecha_log, ca.tipo_calamidad, etc.

Ejemplo correcto:
SELECT e.nombre, l.fecha_log FROM empleados AS e INNER JOIN empleado_log AS l ON e.codigo_empleado = l.codigo_empleado;

Responde solo con una consulta SQL válida en una sola línea, sin explicaciones.
Pregunta: $pregunta
    ";

    $url = "https://api.openai.com/v1/chat/completions";
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "Eres un asistente experto en consultas MySQL."],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.2
    ];

    $headers = [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($result, true);
    if (isset($response['choices'][0]['message']['content'])) {
        $sql_generado = trim($response['choices'][0]['message']['content']);

        try {
            $pdo = Database::connect();
            $stmt = $pdo->query($sql_generado);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = count($resultados);
            $html = "<p><strong>Total de registros:</strong> {$total}</p>";
            $html .= "<p><strong>Consulta generada:</strong></p><pre><code>" . htmlspecialchars($sql_generado) . "</code></pre>";

            if ($total > 0) {
                $html .= "<div style='overflow-x:auto'><table border='1' cellpadding='6' cellspacing='0' style='border-collapse: collapse; min-width: 400px;'>";
                $html .= "<thead><tr>";
                foreach (array_keys($resultados[0]) as $col) {
                    $html .= "<th style='background-color:#f4f4f4'>" . htmlspecialchars($col) . "</th>";
                }
                $html .= "</tr></thead><tbody>";

                foreach ($resultados as $fila) {
                    $html .= "<tr>";
                    foreach ($fila as $valor) {
                        $html .= "<td>" . htmlspecialchars($valor) . "</td>";
                    }
                    $html .= "</tr>";
                }
                $html .= "</tbody></table></div>";
            } else {
                $html .= "<p>No se encontraron resultados.</p>";
            }

            echo $html;
        } catch (PDOException $e) {
            echo "<p><strong>Error al ejecutar la consulta:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "No se pudo obtener una respuesta de la IA.";
    }
} else {
    echo "Petición no válida.";
}
