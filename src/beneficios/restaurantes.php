<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beneficios Grupo PCR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .parallax {
      background-image: url('https://images.unsplash.com/photo-1519999482648-25049ddd37b1');
      height: 300px;
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
    }
    .section-content {
      display: none;
    }
    #loader {
      display: none;
      text-align: center;
    }
    #backToTopBtn {
      display: none;
      position: fixed;
      bottom: 40px;
      right: 30px;
      z-index: 99;
      font-size: 18px;
      border: none;
      outline: none;
      background-color: #0d6efd;
      color: white;
      cursor: pointer;
      padding: 10px 15px;
      border-radius: 50px;
    }
    #backToTopBtn:hover {
      background-color: #0b5ed7;
    }
  </style>
</head>
<body>
  <header class="parallax">
    <div class="text-center">
      <h1 class="display-5 fw-bold">Beneficios para Colaboradores del Grupo PCR</h1>
      <p class="lead">Presenta tu carnet de colaborador para gozar de estos beneficios. #GrupoPCRteCuida</p>
    </div>
  </header>

  <main class="container my-5">
    <div class="alert alert-info">
      Si necesitas renovar tu carnet, acércate o escribe al Departamento de Recursos Humanos.
    </div>
      
      
    <div>
        <h3 class="mb-4">Restaurantes</h3>
      
        <!-- Sirena Restaurante -->
        <div class="card mb-4">
          <img src="image/sirena.png" class="card-img-top" alt="Sirena Restaurante">
          <div class="card-body">
            <h4 class="card-title">Sirena Restaurante</h4>
            <p><strong>Descuento:</strong> 15% en alimentos y bebidas (no aplica a menú promocional).</p>
            <p><strong>Ubicación:</strong> La Calzada de Amador, Isla Flamenco.</p>
            <p>Experiencia culinaria con productos del mar, carnes, pescados y vinos. Almuerzo y cena en un ambiente excepcional.</p>
            <div class="alert alert-warning">El colaborador debe presentar su carnet para gozar de este beneficio.</div>
          </div>
        </div>
      
        <!-- TGI Fridays -->
        <div class="card mb-4">
          <img src="image/friday.png" class="card-img-top" alt="TGI Fridays">
          <div class="card-body">
            <h4 class="card-title">TGI Fridays</h4>
            <p><strong>Descuento:</strong> 15% en alimentos y bebidas (no aplica a menú promocional).</p>
            <p><strong>Ubicación:</strong> Todas las sucursales del país.</p>
            <div class="alert alert-warning">El colaborador debe presentar su carnet para gozar de este beneficio.</div>
          </div>
        </div>
      
        <!-- Os Segregados de la Carne -->
        <div class="card mb-4">
          <img src="image/ossegredo.jpg" class="card-img-top" alt="Os Segregados de la Carne">
          <div class="card-body">
            <h4 class="card-title">Os Segregados de la Carne</h4>
            <ul>
              <li>25% en Rodizio completo para colaborador.</li>
              <li>10% en Rodizio completo para acompañante.</li>
              <li>Precios especiales para grupos con bebidas incluidas.</li>
              <li>Certificado de regalo 20% para fechas especiales.</li>
              <li>Paquetes todo incluido para eventos.</li>
              <li>Salón VIP sin costo y atención personalizada.</li>
            </ul>
            <p><strong>Ubicación:</strong> Torre Universal, Av. Federico Boyd y Calle 51 Este.</p>
            <div class="alert alert-warning">El colaborador debe presentar su carnet para gozar de este beneficio.</div>
          </div>
        </div>
      
        <!-- A Beber -->
        <div class="card mb-4">
          <img src="image/abeber.jpeg" class="card-img-top" alt="A Beber">
          <div class="card-body">
            <h4 class="card-title">A Beber</h4>
            <p><strong>Descuento:</strong> 15% en alimentos (no incluye menú promocional).</p>
            <p><strong>Ubicación:</strong> Brisas del Golf Norte, Plaza Panamá.</p>
            <div class="alert alert-warning">El colaborador debe presentar su carnet para gozar de este beneficio.</div>
          </div>
        </div>
      
        <!-- Brava Pizza -->
        <div class="card mb-4">
          <img src="image/bravapizza.png" class="card-img-top" alt="Brava Pizza">
          <div class="card-body">
            <h4 class="card-title">Brava Pizza</h4>
            <p><strong>Descuento:</strong> 15% en alimentos y bebidas (no incluye menú promocional).</p>
            <p><strong>Ubicación:</strong> Multiplaza, Costa del Este y Altaplaza.</p>
            <div class="alert alert-warning">El colaborador debe presentar su carnet para gozar de este beneficio.</div>
          </div>
        </div>
      
        <!-- Sushi Express -->
        <div class="card mb-4">
          <img src="image/sushiexpress.png" class="card-img-top" alt="Sushi Express">
          <div class="card-body">
            <h4 class="card-title">Sushi Express</h4>
            <p><strong>Descuento:</strong> 15% en el total de la compra.</p>
            <ul>
              <li>Aplica en las 9 sucursales.</li>
              <li>Consumo en local o para llevar (no aplica en delivery ni apps).</li>
              <li>Válido de lunes a domingo de 11:30 a.m. a 10:00 p.m.</li>
              <li>Presentar carnet y cédula vigente.</li>
              <li><em>No es transferible. No aplica a bebidas alcohólicas ni promociones.</em></li>
            </ul>
          </div>
        </div>
      
        <!-- MISAWA -->
        <div class="card mb-4">
          <img src="image/misawa.png" class="card-img-top" alt="Misawa">
          <div class="card-body">
            <h4 class="card-title">Misawa</h4>
            <p><strong>Descuento:</strong> 10% exclusivo para colaboradores de Grupo PCR.</p>
            <p><strong>Ubicación:</strong> Sucursal Los Pueblos.</p>
            <div class="alert alert-warning">Presentar carnet de colaborador.</div>
          </div>
        </div>
      
        <!-- Krispy Kreme -->
        <div class="card mb-4">
          <img src="image/krispy.png" class="card-img-top" alt="Krispy Kreme">
          <div class="card-body">
            <h4 class="card-title">Krispy Kreme</h4>
            <p><strong>Descuento:</strong> 15% en el total de la factura al consumir $3.75 o más.</p>
            <p><strong>Restricciones:</strong> No aplica en delivery ni en sucursal del Aeropuerto de Tocumen.</p>
            <div class="alert alert-warning">Presentar carnet de colaborador.</div>
          </div>
        </div>
      
        <!-- Zabah Kitchen 
        <div class="card mb-4">
          <img src="image/zabah.jpg" class="card-img-top" alt="Zabah Kitchen">
          <div class="card-body">
            <h4 class="card-title">Zabah Kitchen - Casco Antiguo</h4>
            <p><strong>Descuento:</strong> 20% sobre el total de la cuenta.</p>
            <div class="alert alert-warning">Recuerda presentar tu carnet de colaborador.</div>
          </div>
        </div>-->
      
        <!-- Papa John's -->
        <div class="card mb-4">
          <img src="image/PapaJ.png" class="card-img-top" alt="Papa John's Panamá">
          <div class="card-body">
            <h4 class="card-title">Papa John's Panamá</h4>
            <p><strong>Descuento:</strong> 25% en cualquier compra (sobre precio regular).</p>
            <p>Aplica también en compras online y app usando el código: <strong>PJCARRENTA25</strong></p>
            <div class="alert alert-warning">Presentar carnet de colaborador para aplicar el beneficio.</div>
          </div>
        </div>
      
        <!-- PF Chang's -->
        <div class="card mb-4">
          <img src="image/Chang.png" class="card-img-top" alt="P.F. Chang's">
          <div class="card-body">
            <h4 class="card-title">P.F. Chang's</h4>
            <p><strong>Descuento:</strong> 10% en el total de la cuenta (menú a la carta).</p>
            <p><strong>Ubicación:</strong> Multiplaza.</p>
            <p><strong>Restricciones:</strong> No aplica a promociones, delivery, menús de temporada, lunch bowls, etc.</p>
            <p><a href="https://www.pfchangs.com.pa/menu.html" target="_blank">Ver menú a la carta</a></p>
            <div class="alert alert-warning">Presentar carné de colaborador. Válido de lunes a domingo.</div>
          </div>
        </div>
      
        <!-- Rio de Oro -->
        <div class="card mb-4">
          <img src="image/riooro.png" class="card-img-top" alt="Rio de Oro">
          <div class="card-body">
            <h4 class="card-title">Rio de Oro</h4>
            <ul>
              <li>15% en pedidos de pasteles de cumpleaños con 3 días hábiles de anticipación.</li>
              <li>10% en pedidos del menú de boquitas con 3 días hábiles de anticipación.</li>
            </ul>
            <img src="image/rio_oro.png" class="card-img-top" alt="P.F. Chang's">
          </div>
        </div>
    </div>


     <div class="d-grid gap-3 mb-4">
      <a href="https://apppcr.net/app/controllers/BeneficiosController.php" class="btn btn-primary" >Volver</a>
     </div>

  </main>

  <button onclick="scrollToTop()" id="backToTopBtn" title="Ir arriba">↑</button>

  <footer class="bg-light text-center py-3">
    <p class="mb-0">Grupo PCR &copy; 2025. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
