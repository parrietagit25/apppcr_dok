<?php
// Clase para manejar la conexión a la base de datos externa
class DatabaseExternal {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_EXTERNAL_HOST . ";dbname=" . DB_EXTERNAL_NAME . ";charset=" . DB_EXTERNAL_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->pdo = new PDO($dsn, DB_EXTERNAL_USER, DB_EXTERNAL_PASS, $options);
        } catch (PDOException $e) {
            error_log("Error conectando a BD externa: " . $e->getMessage());
            throw new Exception("Error de conexión a base de datos externa");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}

