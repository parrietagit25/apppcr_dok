-- Ampliación telemetría: dispositivo, resolución, ubicación, etc.
-- Compatible MySQL 5.7 / 8.x / MariaDB (sin IF NOT EXISTS).
-- Ejecutar solo si ya tiene la tabla telemetria_eventos base.

SET NAMES utf8mb4;

-- Ver columnas actuales (opcional):
-- SHOW COLUMNS FROM telemetria_eventos;

ALTER TABLE telemetria_eventos
  ADD COLUMN dispositivo_tipo VARCHAR(24) NULL COMMENT 'mobile, tablet, desktop' AFTER user_agent,
  ADD COLUMN navegador VARCHAR(80) NULL AFTER dispositivo_tipo,
  ADD COLUMN sistema_operativo VARCHAR(80) NULL AFTER navegador,
  ADD COLUMN resolucion_pantalla VARCHAR(24) NULL AFTER sistema_operativo,
  ADD COLUMN resolucion_viewport VARCHAR(24) NULL AFTER resolucion_pantalla,
  ADD COLUMN pixel_ratio DECIMAL(4,2) NULL AFTER resolucion_viewport,
  ADD COLUMN timezone VARCHAR(64) NULL AFTER pixel_ratio,
  ADD COLUMN idioma VARCHAR(16) NULL AFTER timezone,
  ADD COLUMN latitud DECIMAL(10,7) NULL AFTER idioma,
  ADD COLUMN longitud DECIMAL(10,7) NULL AFTER latitud,
  ADD COLUMN ubicacion_texto VARCHAR(255) NULL AFTER longitud,
  ADD COLUMN tipo_conexion VARCHAR(32) NULL AFTER ubicacion_texto,
  ADD COLUMN plataforma VARCHAR(80) NULL AFTER tipo_conexion,
  ADD COLUMN isp VARCHAR(120) NULL AFTER plataforma,
  ADD COLUMN referrer VARCHAR(500) NULL AFTER isp;

ALTER TABLE telemetria_eventos ADD INDEX idx_tel_dispositivo (dispositivo_tipo);
ALTER TABLE telemetria_eventos ADD INDEX idx_tel_navegador (navegador);

-- Si alguna columna ya existe y falla el ALTER completo, ejecute solo las que falten, por ejemplo:
-- ALTER TABLE telemetria_eventos ADD COLUMN dispositivo_tipo VARCHAR(24) NULL AFTER user_agent;
