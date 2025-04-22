<?php

// app/core/Bootstrap.php

// Cargar Configuración Global
require_once __DIR__ . '/../../config/config.php';

// Bootstrap.php - Inicializa la aplicación cargando los archivos principales

require_once __DIR__ . '/Database.php'; // Cargar la conexión a la base de datos
require_once __DIR__ . '/../controllers/AuthController.php'; // Cargar el controlador de autenticación
require_once __DIR__ . '/../models/User.php'; // Cargar el modelo de usuario

// Se pueden añadir más configuraciones aquí en el futuro
