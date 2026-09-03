-- Agrega campo observacion_rrhh a la tabla uniformes
-- Ejecutar una sola vez en producción
ALTER TABLE uniformes
    ADD COLUMN IF NOT EXISTS observacion_rrhh VARCHAR(500) NULL DEFAULT NULL
        COMMENT 'Observaciones del área de RRHH al actualizar el estado del uniforme';
