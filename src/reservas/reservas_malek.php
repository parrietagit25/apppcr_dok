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
            
            
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;

            /* 
            width: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid black;
            */
        }
        h1 {
            font-size: 60px;
            font-weight: bold;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            font-size: 50px;
            margin: 5px 0;
        }
        .logo {
            margin-top: 20px;
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>BIENVENIDO / WELCOME</h1>
        <ul id="lista-clientes"></ul>
        <div class="logo"><img src="1.png" alt="" srcset="" width="400"></div>
    </div>

    <script>
        function cargarClientes() {
            console.log('pasando');
            fetch('consulta_clientes_malek.php')
                .then(response => response.json())
                .then(data => {
                    let lista = document.getElementById('lista-clientes');
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
                })
                .catch(error => console.error('Error al obtener los clientes:', error));
        }
        
        cargarClientes();
        setInterval(cargarClientes, 60000); // Actualiza cada 10 segundos
    </script>
</body>
</html>
