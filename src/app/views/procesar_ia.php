<?php
require_once __DIR__ . '/../../config/config.php';

$apiKey = API_IA; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pregunta'])) {
    $pregunta = trim($_POST['pregunta']);

    $url = "https://api.openai.com/v1/chat/completions";

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "user", "content" => $pregunta]
        ],
        "temperature" => 0.7
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
        echo $response['choices'][0]['message']['content'];
    } else {
        echo "No se pudo obtener una respuesta de la IA.";
    }
} else {
    echo "Petición no válida.";
}
