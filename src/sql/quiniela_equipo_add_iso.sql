-- Añade ISO a equipos (instalaciones ya creadas sin esta columna).
-- Tras ejecutar, puede actualizar iso manualmente o recrear grupos.

ALTER TABLE quiniela_equipo
  ADD COLUMN iso VARCHAR(2) NULL COMMENT 'ISO 3166-1 alpha-2 minúsculas' AFTER nombre;
