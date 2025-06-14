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
      <h3 class="mb-4">Beneficio Exclusivo para colaboradores de Grupo PCR</h3>
    
      <!-- Beneficio Automarket Seminuevos -->
      <div class="card mb-4 border-success">
        <img src="image/automarket.jpg" class="card-img-top" alt="Automarket Seminuevos">
        <div class="card-body">
          <h4 class="card-title">Automarket Seminuevos – Descuento Exclusivo</h4>
          <p><strong>Hasta 10% de descuento</strong> para colaboradores de Grupo PCR en autos de flota con más de 13 meses de uso.</p>
          <ul>
            <li><strong>Colaboradores con 2 años:</strong> 10% de descuento sobre precio de lista (con ITBMS).</li>
            <li><strong>Colaboradores con 1 año:</strong> 5% de descuento sobre precio de lista (con ITBMS).</li>
            <li>Aplica solo a vehículos de uso corporativo o retail con más de 13 meses.</li>
            <li>Válido una vez cada 5 años. Transferible a familiares de primer grado de consanguinidad.</li>
          </ul>
          <p><strong>Ejemplo:</strong> Un auto con precio de lista $10,000.00 recibiría un descuento de $1,000 (10%). Total a pagar: $9,000.00 <em>(sin incluir traspaso ni combustible).</em></p>
        </div>
      </div>

      <!-- Beneficio Lentes -->
      <div class="card mb-4 border-primary">
        <div class="card-body">
          <h4 class="card-title">Apoyo para Compra de Lentes – Grupo PCR</h4>
          <ul>
            <li>Disponible para colaboradores con 6 meses o más de antigüedad.</li>
            <li>Apoyo económico de <strong>B/.50.00</strong> para compra de lentes.</li>
            <li>Utilizable una vez cada 2 años.</li>
            <li>El pago se realiza directamente a la óptica o proveedor.</li>
            <li>Debe presentarse factura o recibo con datos bancarios del proveedor.</li>
            <li>Trámite a través del Departamento de Recursos Humanos.</li>
          </ul>
        </div>
      </div>

      <!-- Beneficio Beca Escolar -->
      <div class="card mb-4 border-warning">
        <div class="card-body">
          <h4 class="card-title">Beca Escolar para Hijos de Colaboradores</h4>
          <ul>
            <li>Aplica a hijos de colaboradores con 6+ meses de antigüedad.</li>
            <li>Dirigido a estudiantes de Primaria, Premedia y Media con promedio mínimo de <strong>4.50</strong> en el año escolar 2023.</li>
            <li>Inscripción mediante formulario en línea (compartido por RRHH).</li>
            <li>Se realiza proceso de preselección.</li>
            <li>Convocatoria anual anunciada por RRHH.</li>
          </ul>
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
