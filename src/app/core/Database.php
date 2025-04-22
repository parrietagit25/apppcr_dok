<?php
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
            } catch (PDOException $e) {
                die("Error en la conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>