-- ====================================================
-- AGREGAR CAMPO CANTIDAD A TABLA UNIFORMES
-- ====================================================

-- Agregar campo cantidad (si no existe)
ALTER TABLE uniformes 
ADD COLUMN IF NOT EXISTS cantidad INT DEFAULT 1 NOT NULL 
COMMENT 'Cantidad de uniformes solicitados';

-- Verificar la estructura actualizada
DESCRIBE uniformes;

-- Actualizar registros existentes que tengan NULL o 0
UPDATE uniformes SET cantidad = 1 WHERE cantidad IS NULL OR cantidad = 0;

-- Ver ejemplo de registros
SELECT id, codigo_empleado, tipo, talla, cantidad, stat, fecha_log 
FROM uniformes 
ORDER BY fecha_log DESC 
LIMIT 5;

-- ====================================================
-- FIN DEL SCRIPT
-- ====================================================

