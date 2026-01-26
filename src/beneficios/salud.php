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
      <h3 class="mb-4">Salud y Bienestar</h3>
    
      <!-- Laboratorio Clínico Fernández -->
      <div class="card mb-4">
        <img src="image/laboratorio_fernandez.png" class="card-img-top" alt="Laboratorio Clínico Fernández">
        <div class="card-body">
          <h4 class="card-title">Laboratorio Clínico Fernández</h4>
          <p><strong>Descuento:</strong> 15% en pruebas de rutina.</p>
          <p><strong>Observaciones:</strong></p>
          <ul>
            <li>No aplica para pruebas especiales (#,+) o exportación (*).</li>
            <li>No aplica con otros descuentos o promociones.</li>
            <li>No aplica para pruebas SARS CoV-2.</li>
          </ul>
          <div class="alert alert-warning">Nota importante: el colaborador debe presentar el carnet para gozar del beneficio.</div>
        </div>
      </div>
    
      <!-- Smart Fit -->
      <div class="card mb-4">
        <img src="image/Smart_Fit.png" class="card-img-top" alt="Smart Fit">
        <div class="card-body">
          <h4 class="card-title">Smart Fit</h4>
          <p><strong>Beneficios:</strong></p>
          <ul>
            <li>No pagas inscripción.</li>
            <li>No pagas mantenimiento.</li>
            <li>Sin cláusula de permanencia.</li>
            <li>Sólo pagas la mensualidad del plan Black.</li>
            <li>Ingreso en todas las sedes a nivel nacional e internacional.</li>
            <li>5 ingresos gratis al mes para amigos o familiares.</li>
            <li>Acceso a salón spa y clases grupales.</li>
          </ul>
          <p><strong>Registro:</strong> <a href="https://www.smartfit.com.pa/gimnasios" target="_blank">https://www.smartfit.com.pa/gimnasios</a><br>
          Código promocional: <strong>SMARTFITGRUPOPCR02</strong></p>
          <div class="alert alert-warning">Nota importante: el colaborador debe registrarse usando el código promocional.</div>
        </div>
      </div>
    
      <!-- VIDATEC -->
      <div class="card mb-4">
        <img src="image/vidatec.png" class="card-img-top" alt="VIDATEC">
        <div class="card-body">
          <h4 class="card-title">VIDATEC</h4>
          <p><strong>Descuento:</strong> 15% exclusivo para colaboradores de Grupo PCR.</p>
          <p>Aplica en todas las pruebas de rutina y se extiende a familiares en primer grado de consanguinidad (padres, hijos, cónyuges).</p>
          <p><strong>Ubicaciones:</strong></p>
          <ul>
            <li><strong>Ciudad de Panamá:</strong> Calle 64 Este, Casa 17 San Francisco | Plaza 770, Costa del Este</li>
            <li><strong>Chiriquí:</strong> Calle A Sur, David</li>
          </ul>
        </div>
      </div>
    
      <!-- Red Bucal -->
      <div class="card mb-4">
        <img src="image/redbucal.png" class="card-img-top" alt="Red Bucal">
        <div class="card-body">
          <h4 class="card-title">Red Bucal</h4>
          <p><strong>Plan prémium:</strong> $16.08 para colaboradores de Grupo Panama Car Rental</p>
          <p><strong>Incluye:</strong></p>
          <ul>
            <li>Consultas de emergencias dentales ilimitadas 100%</li>
            <li>Primera limpieza dental anual 100%</li>
            <li>Siguientes limpiezas al 50%</li>
            <li>Primera consulta odontológica anual 100%</li>
            <li>Plan de frenos a $40/mes sin abono inicial</li>
            <li>Tratamientos dentales con 20% - 80% de cobertura</li>
            <li>Consultas médicas generales con 75% de cobertura</li>
            <li>Consultas de especialidades con 10% de cobertura ilimitada</li>
            <li>Más de 350 laboratorios clínicos con 25% cobertura</li>
            <li>Más de 175 exámenes especializados con 25% cobertura</li>
          </ul>
          <p><strong>Contacto para adquirir el plan:</strong> <a href=\"tel:50763281368\">📞 6328-1368</a></p>
        </div>
      </div>
    
      <!-- Clínica La Sonrisa PTY -->
      <div class="card mb-4">
        <img src="image/sonrrisa.png" class="card-img-top" alt="Clínica La Sonrisa PTY">
        <div class="card-body">
          <h4 class="card-title">🦷 Clínica La Sonrisa PTY</h4>
          <p class="lead">¡Tu sonrisa también es parte de nuestros beneficios!</p>
          <p>En Automarket Panamá seguimos apostando por tu bienestar. Por eso, ahora cuentas con un convenio exclusivo en Clínica La Sonrisa PTY, diseñado especialmente para ti y tu familia, con precios preferenciales y grandes ahorros.</p>
          
          <p><strong>¿Qué incluye este beneficio?</strong></p>
          <ul>
            <li>✅ Evaluación odontológica general</li>
            <li>✅ Limpieza dental profesional</li>
            <li>✅ Radiografía panorámica</li>
            <li>✅ Plan de tratamiento personalizado</li>
          </ul>
          
          <div class="alert alert-success">
            <strong>💰 Precio especial por colaborador: B/. 34.99</strong>
          </div>
          
          <p><strong>Además, disfruta de:</strong></p>
          <ul>
            <li>🦷 Plan de ortodoncia sin abono inicial</li>
            <li>🎉 15% de descuento en todos los tratamientos odontológicos</li>
            <li>👨‍👩‍👧 Beneficios extensivos a cónyuges e hijos</li>
            <li>💳 Opciones de pago y financiamiento</li>
            <li>📅 Participación en ferias de salud corporativas</li>
            <li>📚 Charlas de salud bucal y cuidado preventivo</li>
          </ul>
          
          <div class="alert alert-info">
            <strong>Sin inscripción, sin cláusulas ocultas y con atención profesional de calidad.</strong><br>
            👉 Solo debes presentar tu carné institucional o validar tu pertenencia a la empresa.
          </div>
          
          <p><strong>Contacto:</strong></p>
          <ul>
            <li>🌐 <a href="https://www.lasonrisapty.com" target="_blank">www.lasonrisapty.com</a></li>
            <li>📞 <a href="tel:50765786903">+507 6578-6903</a></li>
          </ul>
          
          <p class="text-muted"><em>Cuidar tu salud también es cuidar tu futuro.</em></p>
        </div>
      </div>
    
      <!-- Mi Óptica Panamá -->
      <div class="card mb-4">
        <img src="image/miopticapanama.png" class="card-img-top" alt="Mi Óptica Panamá">
        <div class="card-body">
          <h4 class="card-title">👓 Mi Óptica Panamá</h4>
          <p class="lead">Ver bien también es un beneficio para ti</p>
          <p>En Automarket Panamá seguimos sumando beneficios pensados en tu bienestar. Ahora cuentas con un convenio exclusivo con Mi Óptica Panamá, para que cuides tu salud visual con precios preferenciales y atención de primera.</p>
          
          <p><strong>¿Qué incluye este beneficio?</strong></p>
          <ul>
            <li>✅ Examen de la vista GRATIS una vez al año<br>
            <small class="text-muted">(para colaboradores y familiares directos: padres, hijos y cónyuge)</small></li>
          </ul>
          
          <p><strong>✅ Descuentos especiales en lentes:</strong></p>
          <ul>
            <li>20% de descuento para colaboradores</li>
            <li>15% de descuento para familiares</li>
            <li>Aros desde $5.00</li>
            <li>Promoción 2x1 en lentes fotocromáticos con antirreflejo</li>
          </ul>
          
          <p><strong>✅ Planes de pago flexibles:</strong></p>
          <ul>
            <li>Hasta 3 cuotas sin intereses</li>
            <li>Opción de descuento por planilla (según aprobación)</li>
          </ul>
          
          <p><strong>✅ Garantía extendida y ajustes ilimitados:</strong></p>
          <ul>
            <li>Cobertura adicional en defectos de fábrica</li>
            <li>Limpieza y ajuste de armazón sin costo</li>
          </ul>
          
          <p><strong>✅ Atención preferencial y jornadas empresariales:</strong></p>
          <ul>
            <li>Prioridad en ferias y giras de salud visual</li>
            <li>Entrega de lentes a domicilio</li>
          </ul>
          
          <div class="alert alert-info">
            <strong>👨‍👩‍👧 Beneficios extensivos a tu familia</strong><br>
            Porque su bienestar también importa.
          </div>
          
          <p class="text-muted"><em>Cuidar tu visión mejora tu rendimiento, reduce la fatiga visual y aporta a tu calidad de vida. Aprovecha este beneficio y mira el futuro con mayor claridad.</em></p>
          
          <p><strong>Contacto:</strong></p>
          <ul>
            <li>📍 <strong>Ubicación:</strong> Plaza Cantabria, Local 23</li>
            <li>📞 <a href="tel:50769474925">+507 6947-4925</a></li>
          </ul>
          
          <p class="text-primary"><strong>✨ Cuidamos tu visión, potenciamos tu rendimiento.</strong></p>
        </div>
      </div>
    
      <!-- Visual Point Óptica -->
      <div class="card mb-4">
        <img src="image/visualpoint.png" class="card-img-top" alt="Visual Point Óptica">
        <div class="card-body">
          <h4 class="card-title">👓 Visual Point Óptica – Panamá</h4>
          <p class="lead">¡Más beneficios para tu visión!</p>
          <p>Como parte de nuestros beneficios corporativos, ahora puedes aprovechar promociones exclusivas con Visual Point Óptica – Panamá, pensadas para cuidar tu salud visual y tu bolsillo.</p>
          
          <p><strong>🎯 Beneficios disponibles:</strong></p>
          <ul>
            <li>✅ 30% de descuento en aros de diseñador<br>
            <small class="text-muted">(al confeccionar tus lentes)</small></li>
            <li>✅ Aros GRATIS en marca propia<br>
            <small class="text-muted">(al confeccionar tus lentes)</small></li>
          </ul>
          
          <div class="alert alert-info">
            <strong>Diseño, calidad y asesoría profesional en un solo lugar.</strong>
          </div>
          
          <p><strong>📍 Visítalos en cualquiera de sus 8 sucursales:</strong></p>
          <ul>
            <li>Albrook Mall (x2)</li>
            <li>Altaplaza Mall</li>
            <li>Supercentro El Dorado</li>
            <li>Los Pueblos</li>
            <li>Los Andes</li>
            <li>Costa Verde</li>
            <li>David, Chiriquí</li>
          </ul>
          
          <p><strong>Contacto:</strong></p>
          <ul>
            <li>📞 <a href="tel:50760402663">(+507) 6040-2663</a></li>
            <li>🌐 <a href="https://www.opticasvisualpointpanama.com" target="_blank">www.opticasvisualpointpanama.com</a></li>
          </ul>
          
          <p class="text-muted"><em>Aprovecha este beneficio exclusivo y dale a tu vista el cuidado que se merece. Ver bien también es parte de tu bienestar.</em></p>
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
