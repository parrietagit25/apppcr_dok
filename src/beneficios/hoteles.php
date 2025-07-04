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
    <h3 class="mb-4">Hoteles</h3>

        
    <!-- Hotel El Panamá -->
    <div class="card mb-4">
      <img src="image/hetel_el_panama.png" class="card-img-top" alt="Hotel El Panamá">
      <div class="card-body">
      <h4 class="card-title">Hotel El Panamá</h4>
      <p>Ubicado cerca de sitios turísticos como el Canal de Panamá y el Palacio Presidencial. Accesible en metro o auto.</p>
      <p><strong>Descuento:</strong> del 10% al 30% en habitaciones con desayuno incluido.</p>
      <p><strong>Contacto:</strong> Liliane Baloyes Gordon<br>
      <strong>Cargo:</strong> Ejecutiva corporativa - Hoteles Faranda Panamá<br>
      <strong>Tel:</strong> (507) 215-9633 / 6430-9383<br>
      <strong>Email:</strong> <a href="mailto:liliane.baloyes@farandahotels.com">liliane.baloyes@farandahotels.com</a></p>
      </div>
    </div>
    
    <!-- Hotel Best Western -->
    <div class="card mb-4">
        <img src="image/hotel_wester.jpg" class="card-img-top" alt="Hotel Best Western Plus Panamá Zen">
        <div class="card-body">
        <h4 class="card-title">Hotel Best Western Plus Panamá Zen</h4>
        <p class="card-text">
            Ubicado cerca del centro financiero de Panamá, a 16 km del Aeropuerto de Tocumen y a 20 metros del Metro Vía Argentina. Ideal para negocios y placer.
        </p>
        <h5 class="mt-3">Tarifas Especiales:</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                <th>Tipo de Habitación</th>
                <th>Adultos</th>
                <th>Niños (hasta 12 años)</th>
                <th>Tarifa</th>
                <th>Impuestos</th>
                <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Sencilla Queen</td><td>2</td><td>0</td><td>$70.00</td><td>$7.00</td><td>$77.00</td></tr>
                <tr><td>Sencilla King</td><td>2</td><td>0</td><td>$72.00</td><td>$7.20</td><td>$79.20</td></tr>
                <tr><td>Doble QQ</td><td>2</td><td>2</td><td>$75.00</td><td>$7.50</td><td>$82.50</td></tr>
                <tr><td>Suites K&</td><td>2</td><td>2</td><td>$90.00</td><td>$9.00</td><td>$99.00</td></tr>
            </tbody>
            </table>
        </div>
        <p><strong>Contacto:</strong> Verushka Badilla<br>
        <strong>Tel:</strong> (507) 300-3700 / 300-3701<br>
        <strong>Email:</strong> <a href="mailto:veroushkab@unesa.com">veroushkab@unesa.com</a></p>
        </div>
    </div>
    
    <!-- Hotel Le Meridien -->
    <div class="card mb-4">
        <img src="image/hotel_le_meridien.jpg" class="card-img-top" alt="Hotel Le Meridien Panamá">
        <div class="card-body">
        <h4 class="card-title">Hotel Le Meridien Panamá</h4>
        <p>Frente a la bahía de Panamá. Incluye spa, piscina, restaurante Latitudes y bar lounge. Habitaciones modernas con TV y minibar.</p>
        <ul>
            <li>Deluxe Sencilla: $105.00</li>
            <li>Deluxe Doble: $115.00</li>
            <li>+ $10 para tercera persona. Incluye desayuno buffet y Wifi.</li>
        </ul>
        </div>
    </div>
    
    <!-- Crown Plaza - Panama Airport -->
    <div class="card mb-4">
        <img src="image/hotel_crowne_plaza.jpg" class="card-img-top" alt="Hotel Crown Plaza - Panama Airport">
        <div class="card-body">
        <h4 class="card-title">Hotel Crown Plaza - Panama Airport</h4>
        <p>A 5 min del Aeropuerto de Tocumen. Shuttle 24h, piscina, gimnasio y restaurante Jetstream.</p>
        <ul>
            <li>Sencilla: $95.00</li>
            <li>Doble: $105.00</li>
            <li>+ $10 para tercera persona. Incluye desayuno buffet y Wifi.</li>
        </ul>
        <p><strong>Contacto:</strong> Benjamín Bonilla<br>
        <strong>Celular:</strong> (507) 6997-8913 / 206-8858<br>
        <strong>Email:</strong> <a href="mailto:bbonilla@bernhotelspanama.com">bbonilla@bernhotelspanama.com</a></p>
        </div>
    </div>
    
    <!-- Hotel Intercontinental Miramar -->
    <div class="card mb-4">
        <img src="image/hotel_intercontinental_miramar.jpg" class="card-img-top" alt="Hotel Intercontinental Miramar Panamá">
        <div class="card-body">
        <h4 class="card-title">Hotel Intercontinental Miramar Panamá</h4>
        <p>Spa, sauna, gimnasio, restaurante Bay View con vista al mar. Cerca de la Cinta Costera y Casco Viejo.</p>
        <ul>
            <li>Classic: Sencilla $120 | Doble $140</li>
            <li>Premium: Sencilla $135 | Doble $155</li>
            <li>Piso Ejecutivo: Sencilla $155 | Doble $175</li>
            <li>Suite 1 hab: Sencilla $215 | Doble $235</li>
            <li>+ $20 para tercera persona. Incluye desayuno, Wifi, café/té en la habitación.</li>
        </ul>
        <p><strong>Contacto:</strong> Benjamín Bonilla<br>
        <strong>Celular:</strong> (507) 6997-8913 / 206-8858<br>
        <strong>Email:</strong> <a href="mailto:bbonilla@bernhotelspanama.com">bbonilla@bernhotelspanama.com</a></p>
        </div>
    </div>
    
    <!-- The Westin Panama -->
    <div class="card mb-4">
        <img src="image/hotel_westing.jpg" class="card-img-top" alt="Hotel The Westin Panama">
        <div class="card-body">
        <h4 class="card-title">Hotel The Westin Panama</h4>
        <p>En Costa del Este. Gimnasio, piscina, spa Sensory, restaurante, lounge ejecutivo y habitaciones Heavenly Bed y Bath.</p>
        <p><strong>Contacto:</strong> Benjamín Bonilla<br>
        <strong>Celular:</strong> (507) 6997-8913 / 206-8858<br>
        <strong>Email:</strong> <a href="mailto:bbonilla@bernhotelspanama.com">bbonilla@bernhotelspanama.com</a></p>
        </div>
    </div>
    
    <!-- Crown Plaza - Zona Bancaria -->
    <div class="card mb-4">
        <img src="image/hotel_crowne_plaza_panama_city.jpg" class="card-img-top" alt="Crown Plaza - Zona Bancaria">
        <div class="card-body">
        <h4 class="card-title">Hotel Crown Plaza - Zona Bancaria</h4>
        <p>Cerca de embajadas, Universidad de Panamá, World Trade Center, malls y Casco Antiguo. Restaurante, bar, piscina, gimnasio y programa Sleep Advantage.</p>
        <ul>
            <li>Sencilla: $80.00</li>
            <li>Doble: $90.00</li>
            <li>+ $10 para tercera persona. Incluye desayuno buffet y Wifi.</li>
        </ul>
        <p><strong>Contacto:</strong> Benjamín Bonilla<br>
        <strong>Celular:</strong> (507) 6997-8913 / 206-8858<br>
        <strong>Email:</strong> <a href="mailto:bbonilla@bernhotelspanama.com">bbonilla@bernhotelspanama.com</a></p>
        </div>
    </div>

    <!-- Torres de Alba Hotel & Suites -->
    <div class="card mb-4">
      <img src="image/torres_de_alba.jpg" class="card-img-top" alt="Torres de Alba Hotel & Suites">
      <div class="card-body">
        <h4 class="card-title">Torres de Alba Hotel & Suites</h4>
        <p>Ubicado en el corazón de la ciudad, ofrece suites con 2 camas dobles, sala, comedor, cocina equipada, lavandería, WiFi y desayuno incluido.</p>
        
        <h5>Tarifas por noche:</h5>
        <ul>
          <li><strong>Suite Standard (Torre 1 y 2):</strong> $89.00 (1-15 noches) / $79.00 (16-29 noches)</li>
          <li><strong>Suite Superior (Torre 3):</strong> $109.00 (1-15 noches) / $99.00 (16-29 noches)</li>
        </ul>

        <h5>Tarifas mensuales:</h5>
        <ul>
          <li><strong>Suite Standard (Torre 1 y 2):</strong> $1,850.00 (sin desayuno) / $1,950.00 (con desayuno)</li>
          <li><strong>Suite Superior (Torre 3):</strong> $2,150.00 (sin desayuno) / $2,250.00 (con desayuno)</li>
        </ul>

        <p><strong>Incluye:</strong> Piscina, gimnasio, estacionamiento, limpieza cada 2 días, seguridad 24/7, agua, luz, caja fuerte, plancha y WiFi.</p>
        
        <p><strong>Contacto:</strong> Angie Tuñon<br>
        <strong>Teléfono:</strong> +507 6429-8160<br>
        <strong>Email:</strong> <a href="mailto:angie.tunon@torresdealba.com.pa">angie.tunon@torresdealba.com.pa</a></p>
      </div>
    </div>


    </div>

    <?php /*

    <div id="educacion" class="section-content">
        <h3 class="mb-4">Educación</h3>
        <div class="card">
            <img src="image/ume.jpg" class="card-img-top" alt="UMECIT">
            <div class="card-body">
            <h4 class="card-title">UMECIT</h4>
            <p class="card-text">Universidad Metropolitana de Educación, Ciencia y Tecnología.</p>
            <ul>
                <li><strong>10%</strong> de descuento en Técnicos y Licenciaturas</li>
                <li><strong>5%</strong> de descuento en Maestrías</li>
                <li><strong>15%</strong> de descuento en el Instituto Bilingüe Internacional</li>
            </ul>
            <p><strong>📍 Ubicación:</strong> Todas las sedes a nivel nacional.</p>
            <div class="alert alert-warning mt-3">
                Nota importante: El colaborador debe presentar el carnet para gozar del beneficio.
            </div>
            </div>
        </div>

        <div class="card">
            <img src="image/wall.jpg" class="card-img-top" alt="UMECIT">
            <div class="card-body">
            <h4 class="card-title">Aprende Inglés con Wall Street English</h4>
            <p class="card-text">¡Exclusivo para colaboradores de Grupo Panamá Car Rental y sus familiares hasta 4° grado de consanguinidad!</p>
            <p class="card-text"> Gracias a nuestra alianza con Wall Street English, ahora puedes mejorar tu inglés con descuentos e incentivos exclusivos: </p>

            <h6>🎁 Beneficios disponibles:</h6> 
            <ul>
                <li>✅ <strong>15%</strong>  de descuento en cualquier plan de estudio.</li>
                <li>✅ <strong>20%</strong>  de descuento en planes pagados al contado.</li>
                <li>✅ <strong>Incentivos trimestrales exclusivos.</strong></li>
            </ul>
            <h6>🌟 Especial Segundo Trimestre (abril - junio 2025): Compra un plan de 12 meses al contado y recibe:</h6>
            <ul>
                <li>+2 meses GRATIS</li>
                <li>Inscripción incluida</li>
                <li>Inscríbete con financiamiento + 1 referido y no pagas inscripción.</li>
                <li>Si tu referido se inscribe, obtienes 1 mes GRATIS adicional.</li>
            </ul>

            <p>📌 ¿Cómo accedo a los beneficios?
                Solo debes registrarte en el siguiente enlace exclusivo para colaboradores de Grupo Panamá Car Rental:</p>
            <div class="alert alert-warning mt-3">
                🔗 <a href="https://www.wallstreetenglish.com.pa/" target="_blank" rel="noopener noreferrer">wallstreetenglish</a>
            </div>
            </div>
        </div>
    </div>
      
    <div id="pcr" class="section-content">
        <h3 class="mb-4">Beneficios Internos PCR</h3>
      
        <!-- Automarket Seminuevos 
        <div class="card mb-4">
          <img src="image/automarket.jpg" class="card-img-top" alt="Automarket Seminuevos">
          <div class="card-body">
            <h4 class="card-title">Automarket Seminuevos</h4>
            <p>Recibe <strong>10% de descuento</strong> en la compra de tu próximo auto seminuevo.</p>
            <ul>
              <li>Aplica para colaboradores con <strong>2 o más años</strong> de antigüedad.</li>
              <li>Solo para autos de la flota de Panama Car Rental con más de <strong>13 meses de uso</strong>.</li>
              <li>El descuento aplica sobre el <strong>precio de lista (incluye ITBMS)</strong>.</li>
              <li>El colaborador asume el pago del traspaso y combustible.</li>
              <li>Beneficio <strong>válido una vez cada 5 años</strong> y <strong>transferible</strong> a un familiar.</li>
            </ul>
            <p><strong>Ejemplo:</strong> Si el modelo XYZ 2022 tiene un precio de $10,000.00 (incluye ITBMS), el 10% equivale a $1,000. El colaborador pagaría $9,000.00. Este precio no incluye el traspaso ni combustible.</p>
          </div>
        </div>-->
      
        <!-- AutoService Taller -->
        <div class="card mb-4">
          <img src="image/autoservice.png" class="card-img-top" alt="AutoService Taller">
          <div class="card-body">
            <h4 class="card-title">AutoService Taller</h4>
            <p><strong>Beneficios:</strong></p>
            <ul>
              <li>15% de descuento en todos los servicios de taller (alineamiento, balance, rotación, mecánica, etc).</li>
              <li>15% de descuento en CDO.</li>
              <li>15% de descuento en lubricantes, filtros y mano de obra al realizar el mantenimiento.</li>
              <li>10% de descuento en baterías.</li>
            </ul>
            <p><strong>Beneficios adicionales:</strong></p>
            <ul>
              <li>Diagnóstico multipuntos del vehículo <strong>GRATIS</strong>.</li>
              <li>Diagnóstico computarizado del vehículo <strong>GRATIS</strong>.</li>
              <li>Diagnóstico de batería computarizado <strong>GRATIS</strong>.</li>
            </ul>
            <p>Para agendar citas, visita: <a href="https://autoservicepanama.com/pages/contactanos" target="_blank">autoservicepanama.com/pages/contactanos</a></p>
          </div>
        </div>
    </div>
      
    <div id="restaurantes" class="section-content">
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

    <div id="salud" class="section-content">
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
    </div>

    <div id="servicios" class="section-content">
      <h3 class="mb-4">Servicios</h3>
    
      <!-- El Machetazo -->
      <div class="card mb-4">
        <img src="image/machetazo.png" class="card-img-top" alt="Supermercado El Machetazo">
        <div class="card-body">
          <h4 class="card-title">Supermercado El Machetazo - Programa Clientazo</h4>
          <p><strong>Si ya tienes Clientazo:</strong></p>
          <ul>
            <li>Acércate a Atención al Cliente en cualquier sucursal.</li>
            <li>Muestra tu carnet de colaborador para activar beneficios del Plan Corporativo.</li>
          </ul>
          <p><strong>Si no tienes Clientazo:</strong></p>
          <ul>
            <li>Regístrate en <a href="https://www.clientazo.com" target="_blank">www.clientazo.com</a></li>
            <li>Luego acércate a cualquier sucursal y presenta tu carnet para activar los beneficios.</li>
          </ul>
          <p>Consulta promociones y beneficios en: <a href="https://www.clientazo.com" target="_blank">www.clientazo.com</a></p>
        </div>
      </div>
    
      <!-- Bremen -->
      <div class="card mb-4">
        <img src="image/bremen.png" class="card-img-top" alt="Bremen">
        <div class="card-body">
          <h4 class="card-title">Bremen</h4>
          <p><strong>Descuentos:</strong></p>
          <ul>
            <li>15% en todos los servicios del taller</li>
            <li>15% en accesorios</li>
            <li>15% en productos de limpieza</li>
            <li>15% en lubricantes, filtros y mano de obra</li>
            <li>10% en baterías</li>
            <li>15% en rines y llantas</li>
          </ul>
          <p><strong>Sucursales:</strong> David, Santiago, Chitré, Calle 50, Tumba Muerto, 24 de diciembre</p>
          <div class="alert alert-warning">Nota: No aplica con otras promociones o descuentos. Presentar carnet de colaborador.</div>
        </div>
      </div>
    
      <!-- Tulipán Services -->
      <div class="card mb-4">
        <img src="image/tulipan.png" class="card-img-top" alt="Tulipán Services">
        <div class="card-body">
          <h4 class="card-title">Tulipán Services</h4>
          <p><strong>Servicios:</strong></p>
          <ul>
            <li>Pintura, Albañilería, Electricidad, Cielo raso de PVC</li>
            <li>Pisos de baldosa y revestimientos</li>
            <li>Trabajos en gypsum, Plomería, Soldadura</li>
            <li>Impermeabilización de techos y losas</li>
            <li>Instalación y mantenimiento de aire acondicionado</li>
          </ul>
          <p><strong>Beneficio:</strong> 5% de descuento en mano de obra para colaboradores.</p>
          <p><strong>Pago a plazos:</strong> Si la mano de obra supera los B/.500.00, se puede pagar en 6 quincenas mediante descuentos directos.</p>
          <p><strong>Requisitos:</strong></p>
          <ul>
            <li>Monto mínimo de mano de obra: B/.300.00</li>
            <li>Enviar correo mínimo 15 días antes a: <a href="mailto:ventas@tulipanservices.com">ventas@tulipanservices.com</a> o <a href="mailto:icando@tulipanservices.com">icando@tulipanservices.com</a></li>
            <li>Materiales y otros insumos corren por cuenta del colaborador.</li>
          </ul>
        </div>
      </div>
    
      <div class="card mb-4 border-secondary">
        <!-- AIRBOX -->
        <div class="card mb-4">
          <img src="image/airbox.png" class="card-img-top" alt="AIRBOX">
          <div class="card-body">
            <h4 class="card-title">AIRBOX – Compras por Internet</h4>
            <p><strong>Plan de Beneficios Extendidos (P.B.E.)</strong> para colaboradores de Grupo PCR, sin membresía ni cuotas.</p>
            <ul>
              <li>Dirección en Miami <strong>GRATIS</strong></li>
              <li>Entregas a domicilio <strong>GRATIS</strong>* (área metropolitana)</li>
              <li>Asesoría personalizada en sucursales y Contact Center</li>
              <li>Tarifa de <strong>$2.50 x lb</strong> en Panamá (Ciudad y Oeste), $3.00 para el resto del país</li>
              <li><strong>0% de impuesto en USA</strong>*</li>
              <li><strong>Plan Compramos por ti:</strong> uso de tarjeta de crédito AIRBOX y beneficios de Amazon Prime</li>
            </ul>
            <p>*Consulta condiciones en: <a href="https://www.airbox.com.pa/condiciones/" target="_blank">www.airbox.com.pa/condiciones</a></p>

            <h5 class="mt-4">Direcciones en Miami:</h5>
            <div class="row">
              <div class="col-md-6">
                <strong>AÉREO</strong>
                <ul>
                  <li>Premium: 2-3 días</li>
                  <li>Economy: 4-6 días</li>
                  <li>Dirección: 7801 NW 37th ST, Doral, FL 33195-6503</li>
                  <li>Tel: (305) 735 8551</li>
                </ul>
              </div>
              <div class="col-md-6">
                <strong>MARÍTIMO</strong>
                <ul>
                  <li>Super Económica: 12 a 17 días</li>
                  <li>Dirección: 8530 NW 72 ST, Miami, FL 33166-6217</li>
                  <li>Tel: (305) 735 8551</li>
                </ul>
              </div>
            </div>

            <div class="mt-3">
              <p>📞 <strong>Teléfonos:</strong> 304-1438 / 6982-1029</p>
              <p>📩 Síguelos en redes sociales: Facebook, Instagram, WhatsApp</p>
            </div>
          </div>
        </div>

      </div>
    </div>
    
    <div id="exclusivos" class="section-content">
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

    */ ?>

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
