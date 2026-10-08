<?php
date_default_timezone_set('America/Panama');

class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) { // ✅ Solo conectar si aún no existe una conexión activa
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->exec("SET time_zone = '-05:00'");
            } catch (PDOException $e) {
                die("Error en la conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>