-- Script para agregar campo es_externo a la tabla empleados
-- Ejecutar este script en la base de datos antes de usar el sistema

-- Agregar campo es_externo a la tabla empleados
ALTER TABLE empleados 
ADD COLUMN es_externo TINYINT(1) DEFAULT 0 COMMENT '1=Colaborador externo, 0=Empleado regular';

-- Crear índice para mejorar rendimiento de consultas
CREATE INDEX idx_empleados_es_externo ON empleados(es_externo);

-- Opcional: Marcar empleados existentes como no externos (si es necesario)
-- UPDATE empleados SET es_externo = 0 WHERE es_externo IS NULL;

-- Verificar que el campo se agregó correctamente
-- DESCRIBE empleados;
