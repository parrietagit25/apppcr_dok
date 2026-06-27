-- Ampliación telemetría: dispositivo, resolución, ubicación, etc.
-- Ejecutar si ya creó telemetria_eventos con el script anterior.

SET NAMES utf8mb4;

ALTER TABLE telemetria_eventos
  ADD COLUMN IF NOT EXISTS dispositivo_tipo VARCHAR(24) NULL COMMENT 'mobile, tablet, desktop' AFTER user_agent,
  ADD COLUMN IF NOT EXISTS navegador VARCHAR(80) NULL AFTER dispositivo_tipo,
  ADD COLUMN IF NOT EXISTS sistema_operativo VARCHAR(80) NULL AFTER navegador,
  ADD COLUMN IF NOT EXISTS resolucion_pantalla VARCHAR(24) NULL AFTER sistema_operativo,
  ADD COLUMN IF NOT EXISTS resolucion_viewport VARCHAR(24) NULL AFTER resolucion_pantalla,
  ADD COLUMN IF NOT EXISTS pixel_ratio DECIMAL(4,2) NULL AFTER resolucion_viewport,
  ADD COLUMN IF NOT EXISTS timezone VARCHAR(64) NULL AFTER pixel_ratio,
  ADD COLUMN IF NOT EXISTS idioma VARCHAR(16) NULL AFTER timezone,
  ADD COLUMN IF NOT EXISTS latitud DECIMAL(10,7) NULL AFTER idioma,
  ADD COLUMN IF NOT EXISTS longitud DECIMAL(10,7) NULL AFTER latitud,
  ADD COLUMN IF NOT EXISTS ubicacion_texto VARCHAR(255) NULL AFTER longitud,
  ADD COLUMN IF NOT EXISTS tipo_conexion VARCHAR(32) NULL AFTER ubicacion_texto,
  ADD COLUMN IF NOT EXISTS plataforma VARCHAR(80) NULL AFTER tipo_conexion,
  ADD COLUMN IF NOT EXISTS isp VARCHAR(120) NULL AFTER plataforma,
  ADD COLUMN IF NOT EXISTS referrer VARCHAR(500) NULL AFTER isp;

-- MySQL < 8.0.12 no soporta IF NOT EXISTS en ADD COLUMN; si falla, ejecute columna por columna omitiendo las existentes.

ALTER TABLE telemetria_eventos ADD KEY idx_tel_dispositivo (dispositivo_tipo);
ALTER TABLE telemetria_eventos ADD KEY idx_tel_navegador (navegador);
