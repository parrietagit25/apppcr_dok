-- Telemetría y seguimiento de uso de APP PCR
-- Ejecutar una vez en la base de datos.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS telemetria_eventos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_empleado VARCHAR(32) NULL,
  type_user TINYINT UNSIGNED NULL,
  evento VARCHAR(50) NOT NULL COMMENT 'login, logout, page_view, accion, registro',
  modulo VARCHAR(100) NULL,
  ruta VARCHAR(500) NULL,
  accion VARCHAR(150) NULL,
  metadata JSON NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(512) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_tel_fecha (created_at),
  KEY idx_tel_evento (evento),
  KEY idx_tel_codigo (codigo_empleado),
  KEY idx_tel_modulo (modulo),
  KEY idx_tel_fecha_evento (created_at, evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
