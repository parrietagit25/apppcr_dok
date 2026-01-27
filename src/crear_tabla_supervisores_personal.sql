-- ====================================================
-- SCRIPT DE CREACIÓN - SISTEMA DE SUPERVISORES Y PERSONAL A CARGO
-- Tabla: supervisores_personal_cargo
-- Base de datos: apppcr
-- ====================================================

-- Crear tabla para relacionar supervisores con su personal a cargo
CREATE TABLE IF NOT EXISTS supervisores_personal_cargo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Código del supervisor (type_user = 6)
    supervisor_code VARCHAR(10) NOT NULL COMMENT 'Código del empleado supervisor',
    
    -- Código del colaborador/personal a cargo
    colaborador_code VARCHAR(10) NOT NULL COMMENT 'Código del empleado que está a cargo',
    
    -- Fecha de asignación
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha en que se asignó el personal a cargo',
    
    -- Estado activo (para soft delete)
    activo TINYINT(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    
    -- Índices para optimizar consultas
    INDEX idx_supervisor (supervisor_code),
    INDEX idx_colaborador (colaborador_code),
    INDEX idx_activo (activo),
    
    -- Índice único para evitar duplicados
    UNIQUE KEY unique_supervisor_colaborador (supervisor_code, colaborador_code),
    
    -- Foreign keys (opcional, comentado por si hay problemas de integridad)
    -- FOREIGN KEY (supervisor_code) REFERENCES empleados(codigo_empleado) ON DELETE CASCADE,
    -- FOREIGN KEY (colaborador_code) REFERENCES empleados(codigo_empleado) ON DELETE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Relación entre supervisores y su personal a cargo';

-- ====================================================
-- VERIFICAR TABLA CREADA
-- ====================================================

-- Mostrar estructura de la tabla
DESCRIBE supervisores_personal_cargo;

-- ====================================================
-- NOTAS IMPORTANTES
-- ====================================================
-- 1. Un colaborador puede tener múltiples supervisores
-- 2. Un supervisor puede ser personal a cargo de otro supervisor (jerarquía)
-- 3. El campo 'activo' permite desactivar relaciones sin eliminarlas
-- 4. El índice único evita duplicar la misma relación supervisor-colaborador
