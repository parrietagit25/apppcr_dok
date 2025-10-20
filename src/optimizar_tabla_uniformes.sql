-- ====================================================
-- SCRIPT DE OPTIMIZACIÓN PARA TABLA UNIFORMES
-- AppPCR - Módulo de Uniformes
-- ====================================================

-- Este script es OPCIONAL y solo debe ejecutarse si 
-- se desea optimizar el rendimiento de las consultas

-- ====================================================
-- 1. AGREGAR ÍNDICES PARA MEJORAR RENDIMIENTO
-- ====================================================

-- Índice en codigo_empleado para búsquedas rápidas por colaborador
CREATE INDEX idx_uniformes_codigo_empleado 
ON uniformes(codigo_empleado);

-- Índice en stat para filtrar por estado
CREATE INDEX idx_uniformes_stat 
ON uniformes(stat);

-- Índice en fecha_log para ordenamiento
CREATE INDEX idx_uniformes_fecha_log 
ON uniformes(fecha_log DESC);

-- Índice compuesto para consultas comunes de RRHH
CREATE INDEX idx_uniformes_stat_fecha 
ON uniformes(stat, fecha_log DESC);

-- Índice en tipo para reportes por tipo de uniforme
CREATE INDEX idx_uniformes_tipo 
ON uniformes(tipo);

-- ====================================================
-- 2. VERIFICAR ÍNDICES CREADOS
-- ====================================================

SHOW INDEX FROM uniformes;

-- ====================================================
-- 3. AGREGAR COMENTARIOS A LA TABLA (DOCUMENTACIÓN)
-- ====================================================

ALTER TABLE uniformes COMMENT = 'Solicitudes de uniformes corporativos para colaboradores de Grupo PCR';

-- Comentarios en columnas
ALTER TABLE uniformes MODIFY COLUMN tipo VARCHAR(100) NOT NULL 
    COMMENT 'Tipo de uniforme: camisa, pantalon, chaleco, carnet de identificacion, botas, gorra';

ALTER TABLE uniformes MODIFY COLUMN talla VARCHAR(10) NOT NULL 
    COMMENT 'Talla del uniforme según tipo';

ALTER TABLE uniformes MODIFY COLUMN stat INT DEFAULT 1 
    COMMENT '1=Solicitado, 2=En Proceso, 3=Entregado';

ALTER TABLE uniformes MODIFY COLUMN observacion VARCHAR(250) 
    COMMENT 'Observaciones o comentarios adicionales del solicitante';

-- ====================================================
-- 4. CONSULTAS ÚTILES PARA REPORTES
-- ====================================================

-- Ver todas las solicitudes pendientes (para RRHH)
SELECT 
    u.id,
    u.codigo_empleado,
    CONCAT(e.nombre, ' ', e.apellido) AS colaborador,
    e.nombre_departamento,
    u.tipo,
    u.talla,
    u.fecha_log,
    CASE u.stat
        WHEN 1 THEN 'Solicitado'
        WHEN 2 THEN 'En Proceso'
        WHEN 3 THEN 'Entregado'
    END AS estado,
    u.observacion
FROM uniformes u
INNER JOIN empleados e ON u.codigo_empleado = e.codigo_empleado
WHERE u.stat IN (1, 2)
ORDER BY u.fecha_log DESC;

-- ====================================================

-- Contar solicitudes por estado
SELECT 
    CASE stat
        WHEN 1 THEN 'Solicitado'
        WHEN 2 THEN 'En Proceso'
        WHEN 3 THEN 'Entregado'
    END AS estado,
    COUNT(*) AS total
FROM uniformes
GROUP BY stat
ORDER BY stat;

-- ====================================================

-- Solicitudes por tipo de uniforme (últimos 30 días)
SELECT 
    tipo,
    COUNT(*) AS total_solicitudes,
    SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) AS entregados,
    SUM(CASE WHEN stat IN (1, 2) THEN 1 ELSE 0 END) AS pendientes
FROM uniformes
WHERE fecha_log >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY tipo
ORDER BY total_solicitudes DESC;

-- ====================================================

-- Solicitudes por departamento (últimos 30 días)
SELECT 
    e.nombre_departamento,
    COUNT(*) AS total_solicitudes,
    SUM(CASE WHEN u.stat = 3 THEN 1 ELSE 0 END) AS entregados,
    SUM(CASE WHEN u.stat IN (1, 2) THEN 1 ELSE 0 END) AS pendientes
FROM uniformes u
INNER JOIN empleados e ON u.codigo_empleado = e.codigo_empleado
WHERE u.fecha_log >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY e.nombre_departamento
ORDER BY total_solicitudes DESC;

-- ====================================================

-- Tallas más solicitadas por tipo de uniforme
SELECT 
    tipo,
    talla,
    COUNT(*) AS cantidad
FROM uniformes
GROUP BY tipo, talla
ORDER BY tipo, cantidad DESC;

-- ====================================================

-- Colaboradores con más solicitudes
SELECT 
    u.codigo_empleado,
    CONCAT(e.nombre, ' ', e.apellido) AS colaborador,
    e.nombre_departamento,
    COUNT(*) AS total_solicitudes,
    MAX(u.fecha_log) AS ultima_solicitud
FROM uniformes u
INNER JOIN empleados e ON u.codigo_empleado = e.codigo_empleado
GROUP BY u.codigo_empleado, e.nombre, e.apellido, e.nombre_departamento
HAVING COUNT(*) > 1
ORDER BY total_solicitudes DESC;

-- ====================================================
-- 5. DATOS DE PRUEBA (OPCIONAL - SOLO PARA TESTING)
-- ====================================================

-- DESCOMENTAR SOLO SI DESEAS INSERTAR DATOS DE PRUEBA

/*
INSERT INTO uniformes (codigo_empleado, tipo, talla, observacion, stat, fecha_log) VALUES
('1326', 'camisa', 'L', 'Urgente - reunión importante', 1, NOW()),
('1326', 'pantalon', '36', 'Talla regular', 2, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('1326', 'botas', '42', 'Para trabajo en campo', 3, DATE_SUB(NOW(), INTERVAL 10 DAY));
*/

-- ====================================================
-- FIN DEL SCRIPT
-- ====================================================

-- NOTAS:
-- - Ejecutar este script DESPUÉS de crear la tabla uniformes
-- - Los índices mejoran el rendimiento en tablas con muchos registros
-- - Las consultas de reporte son ejemplos útiles para RRHH
-- - Los datos de prueba son opcionales

