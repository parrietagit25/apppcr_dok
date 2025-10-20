-- ====================================================
-- SCRIPT DE CREACIÓN - SISTEMA DE RESERVAS AUTOMARKET
-- Tabla: reservas
-- Base de datos: apppcr
-- ====================================================

-- Eliminar tabla si existe (solo para desarrollo/reinstalación)
-- DROP TABLE IF EXISTS reservas;

-- Crear tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
    -- ID único de cada registro
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos principales de la reserva
    commonid INT NOT NULL COMMENT 'ID común de la reserva en sistema externo',
    resnumber VARCHAR(50) NULL COMMENT 'Número de reserva',
    ranumber VARCHAR(50) NULL COMMENT 'Número de RA (Rental Agreement)',
    company VARCHAR(100) NULL COMMENT 'Compañía/empresa del cliente',
    
    -- Fechas
    dateout DATE NOT NULL COMMENT 'Fecha de salida/retiro del vehículo',
    datein DATE NULL COMMENT 'Fecha de devolución del vehículo',
    dateadded DATETIME NULL COMMENT 'Fecha en que se agregó al sistema',
    
    -- Detalles de la reserva
    reservedclass VARCHAR(50) NULL COMMENT 'Clase de vehículo reservado (Economy, SUV, etc.)',
    customer VARCHAR(255) NOT NULL COMMENT 'Nombre del cliente',
    
    -- Ubicaciones
    locationcodeout VARCHAR(10) NOT NULL COMMENT 'Código de ubicación de salida (PTY, MALEK, etc.)',
    locationcodein VARCHAR(10) NULL COMMENT 'Código de ubicación de devolución',
    
    -- Estados y códigos
    resstatus INT DEFAULT 20 COMMENT 'Estado de la reserva (20=Activa, etc.)',
    sourcecode VARCHAR(10) NULL COMMENT 'Código de fuente (210=VIP/Prioritario)',
    
    -- Auditoría
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última actualización',
    
    -- Índices para optimizar consultas
    INDEX idx_dateout (dateout),
    INDEX idx_locationcodeout (locationcodeout),
    INDEX idx_resstatus (resstatus),
    INDEX idx_customer (customer),
    INDEX idx_sourcecode (sourcecode),
    
    -- Índice compuesto para la consulta principal (pantallas de bienvenida)
    INDEX idx_pantalla_bienvenida (dateout, locationcodeout, resstatus, customer)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Reservas de vehículos para pantallas de bienvenida Automarket';

-- ====================================================
-- VERIFICAR TABLA CREADA
-- ====================================================

-- Mostrar estructura de la tabla
DESCRIBE reservas;

-- ====================================================
-- DATOS DE PRUEBA (OPCIONAL - descomentar para usar)
-- ====================================================

/*
-- Insertar datos de prueba para PTY (hoy)
INSERT INTO reservas (
    commonid, resnumber, ranumber, company, dateout, datein,
    reservedclass, customer, dateadded, locationcodeout,
    locationcodein, resstatus, sourcecode
) VALUES
(1001, 'RES-2025-001', 'RA-001', 'Empresa ABC', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 
 'Economy', 'JUAN PEREZ', NOW(), 'PTY', 'PTY', 20, '100'),

(1002, 'RES-2025-002', 'RA-002', 'Corporativo XYZ', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY),
 'SUV', 'MARIA RODRIGUEZ', NOW(), 'PTY', 'PTY', 20, '210'),

(1003, 'RES-2025-003', 'RA-003', 'Global Inc', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY),
 'Sedan', 'CARLOS SMITH', NOW(), 'PTY', 'PTY', 20, '100'),

(1004, 'RES-2025-004', 'RA-004', 'Tech Solutions', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY),
 'Luxury', 'ANA GARCIA', NOW(), 'PTY', 'PTY', 20, '210'),

(1005, 'RES-2025-005', 'RA-005', 'Services Ltd', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 4 DAY),
 'Van', 'PEDRO LOPEZ', NOW(), 'PTY', 'PTY', 20, '100');

-- Insertar datos de prueba para MALEK (hoy)
INSERT INTO reservas (
    commonid, resnumber, ranumber, company, dateout, datein,
    reservedclass, customer, dateadded, locationcodeout,
    locationcodein, resstatus, sourcecode
) VALUES
(2001, 'RES-2025-101', 'RA-101', 'Business Corp', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY),
 'Economy', 'SOFIA MARTINEZ', NOW(), 'MALEK', 'MALEK', 20, '100'),

(2002, 'RES-2025-102', 'RA-102', 'International SA', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 DAY),
 'SUV', 'DIEGO FERNANDEZ', NOW(), 'MALEK', 'MALEK', 20, '210'),

(2003, 'RES-2025-103', 'RA-103', 'Trade Group', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY),
 'Compact', 'LUCIA RAMIREZ', NOW(), 'MALEK', 'MALEK', 20, '100');
*/

-- ====================================================
-- CONSULTAS ÚTILES PARA TESTING
-- ====================================================

-- Ver todas las reservas de hoy
SELECT * FROM reservas WHERE dateout = CURDATE();

-- Ver reservas activas de PTY hoy
SELECT 
    UPPER(customer) AS customer,
    MAX(CASE WHEN sourcecode = '210' THEN '210' ELSE '0' END) AS sourcecode
FROM reservas
WHERE dateout = CURDATE()
  AND customer IS NOT NULL AND customer <> ''
  AND locationcodeout = 'PTY'
  AND resstatus = 20
GROUP BY UPPER(customer)
ORDER BY customer ASC;

-- Ver reservas activas de MALEK hoy
SELECT 
    UPPER(customer) AS customer,
    MAX(CASE WHEN sourcecode = '210' THEN '210' ELSE '0' END) AS sourcecode
FROM reservas
WHERE dateout = CURDATE()
  AND customer IS NOT NULL AND customer <> ''
  AND locationcodeout = 'MALEK'
  AND resstatus = 20
GROUP BY UPPER(customer)
ORDER BY customer ASC;

-- Contar reservas por ubicación hoy
SELECT 
    locationcodeout,
    COUNT(*) as total_reservas,
    SUM(CASE WHEN sourcecode = '210' THEN 1 ELSE 0 END) as vip_reservas
FROM reservas
WHERE dateout = CURDATE() AND resstatus = 20
GROUP BY locationcodeout;

-- ====================================================
-- MANTENIMIENTO
-- ====================================================

-- Limpiar reservas antiguas (más de 30 días)
-- DELETE FROM reservas WHERE dateout < DATE_SUB(CURDATE(), INTERVAL 30 DAY);

-- Limpiar todas las reservas (usar con precaución)
-- TRUNCATE TABLE reservas;

-- ====================================================
-- FIN DEL SCRIPT
-- ====================================================

