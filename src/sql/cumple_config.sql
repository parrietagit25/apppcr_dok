-- Mantenimiento de cumpleañeros (Mant Cumple)
-- Ejecutar una vez en la base de datos de producción.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS cumple_config (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_empleado VARCHAR(32) NOT NULL,
  accion ENUM('ocultar', 'forzar') NOT NULL DEFAULT 'ocultar',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  motivo VARCHAR(255) NULL,
  modificado_por VARCHAR(32) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cumple_codigo (codigo_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reglas que antes estaban fijas en código (opcional si ya existían a mano)
INSERT IGNORE INTO cumple_config (codigo_empleado, accion, activo, motivo) VALUES
('002567', 'ocultar', 1, 'Regla histórica'),
('001023', 'ocultar', 1, 'Regla histórica'),
('002465', 'forzar', 1, 'Regla histórica');
