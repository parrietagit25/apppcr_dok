<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';

// Simulación de respuesta de IA (puedes reemplazar esto con una variable real)
$respuesta = "La inteligencia artificial (IA) es un campo de la informática que se centra en la creación de sistemas capaces de realizar tareas que normalmente requieren inteligencia humana. Esto incluye el reconocimiento de voz, la toma de decisiones, la traducción de idiomas y mucho más. Los avances en IA están transformando industrias enteras, desde la salud hasta la educación.";

function generarResumen($texto, $limite = 40) {
    $palabras = explode(" ", strip_tags($texto));
    if (count($palabras) <= $limite) {
        return $texto;
    }
    return implode(" ", array_slice($palabras, 0, $limite)) . "...";
}

$resumen = generarResumen($respuesta);
?>

<div class="container mt-4">
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - AI PCR</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido de la IA -->
    <div class="container mt-4">
        <h5>Haz tu pregunta a la IA</h5>
        <form id="form-ia">
            <div class="mb-3">
                <textarea class="form-control" id="pregunta" name="pregunta" rows="3" placeholder="Escribe tu pregunta..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
        <div class="mt-4">
            <h5>Respuesta:</h5>
            <div id="respuesta" class="border p-3 bg-light rounded"></div>
        </div>
    </div>

</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<script>
    document.getElementById("form-ia").addEventListener("submit", function(e) {
        e.preventDefault();
        const pregunta = document.getElementById("pregunta").value;

        fetch("/src/app/views/procesar_ia.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "pregunta=" + encodeURIComponent(pregunta)
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById("respuesta").textContent = data;
            const utterance = new SpeechSynthesisUtterance(data);
            speechSynthesis.speak(utterance);
        })
        .catch(err => {
            document.getElementById("respuesta").textContent = "Error al consultar la IA.";
            console.error(err);
        });
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
