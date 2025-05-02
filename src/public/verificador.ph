<?php
$urls = [
    '/manifest.json',
    '/sw.js',
    '/icons/ico-192.png',
    '/icons/ico-512.png',
    '/js/app.js',
];

echo "<h2>Verificación de recursos PWA</h2>";
foreach ($urls as $url) {
    $full = "https://{$_SERVER['HTTP_HOST']}{$url}";
    $headers = @get_headers($full);
    $status = $headers && strpos($headers[0], '200') !== false ? '✅ OK' : '❌ ERROR';
    echo "<p>$status - <a href='$full' target='_blank'>$full</a></p>";
}
?>

