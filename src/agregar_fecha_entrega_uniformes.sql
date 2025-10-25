-- ====================================================
-- AGREGAR CAMPO FECHA_ENTREGA A TABLA UNIFORMES
-- ====================================================

-- Agregar campo fecha_entrega (registra cuándo se marcó como entregado)
ALTER TABLE uniformes 
ADD COLUMN IF NOT EXISTS fecha_entrega DATETIME NULL 
COMMENT 'Fecha en que el uniforme fue marcado como entregado';

-- Agregar campo fecha_proceso (opcional - registra cuándo se marcó en proceso)
ALTER TABLE uniformes 
ADD COLUMN IF NOT EXISTS fecha_proceso DATETIME NULL 
COMMENT 'Fecha en que el uniforme fue marcado como en proceso';

-- Verificar la estructura actualizada
DESCRIBE uniformes;

-- Ver ejemplo de registros
SELECT id, codigo_empleado, tipo, talla, cantidad, stat, fecha_log, fecha_proceso, fecha_entrega 
FROM uniformes 
ORDER BY fecha_log DESC 
LIMIT 5;

-- ====================================================
-- FIN DEL SCRIPT
-- ====================================================

