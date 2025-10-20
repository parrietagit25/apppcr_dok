<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenidos | Automarket</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f8f8f8;
    }

    .container {
      max-height: 900px; /* altura máxima visible */
      overflow: hidden;
      margin: 50px auto;
      padding: 20px;
      border-radius: 10px;
    }

    h1 {
      font-size: 30px;
      font-weight: bold;
    }

    ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    li {
      font-size: 25px;
      font-weight: bold;
      margin: 5px 0;
    }

    .logo {
      margin-top: 20px;
      font-weight: bold;
      color: red;
    }

    #lista-wrapper {
      max-height: 900px;
      overflow: hidden;
      position: relative;
    }

    #lista-clientes {
      transition: transform 1s ease;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo"><img src="1.png" alt="Logo" width="400"></div>
    <h1>BIENVENIDO / WELCOME</h1>
    <div id="lista-wrapper">
      <ul id="lista-clientes"></ul>
    </div>
    
  </div>

  <script>
    let intervalScroll;
    let scrollDown = true;

    function cargarClientes() {
      console.log('Actualizando lista...');
      fetch('consulta_clientes.php')
        .then(response => response.json())
        .then(data => {
          const lista = document.getElementById('lista-clientes');
          lista.innerHTML = '';

          data.forEach(cliente => {
            const li = document.createElement('li');
            li.textContent = cliente.customer;

            if (cliente.sourcecode === '210') {
              li.style.color = 'green';
              li.style.fontWeight = 'bold';
            }

            lista.appendChild(li);
          });

          if (data.length > 25) {
            iniciarScroll();
          } else {
            detenerScroll();
          }
        })
        .catch(error => console.error('Error al obtener los clientes:', error));
    }

    function iniciarScroll() {
      detenerScroll(); // Limpiar scroll anterior

      const listaWrapper = document.getElementById('lista-wrapper');
      const lista = document.getElementById('lista-clientes');

      let pos = 0;
      const maxScroll = lista.scrollHeight - listaWrapper.clientHeight;

      intervalScroll = setInterval(() => {
        if (scrollDown) {
          pos += 1;
          if (pos >= maxScroll) {
            scrollDown = false;
            clearInterval(intervalScroll);
            setTimeout(() => iniciarScroll(), 10000);
          }
        } else {
          pos -= 1;
          if (pos <= 0) {
            scrollDown = true;
            clearInterval(intervalScroll);
            setTimeout(() => iniciarScroll(), 10000);
          }
        }
        listaWrapper.scrollTop = pos;
      }, 100); // velocidad de desplazamiento (más bajo = más lento)
    }

    function detenerScroll() {
      clearInterval(intervalScroll);
      document.getElementById('lista-wrapper').scrollTop = 0;
    }

    cargarClientes();
    setInterval(cargarClientes, 60000); // Actualiza cada minuto
  </script>
</body>
</html>
