-- Script para crear la tabla encargados_colab si no existe
-- Esta tabla maneja empleados fuera de planilla con tipo de usuario 6

CREATE TABLE IF NOT EXISTS `encargados_colab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_empleado` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `gerente_area` varchar(150) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_empleado` (`code_empleado`),
  KEY `idx_departamento` (`departamento`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentarios sobre la tabla
-- Esta tabla almacena información de empleados que están fuera de la planilla principal
-- pero que necesitan acceso al sistema APP PCR
-- Se relaciona con empleado_log mediante el campo code_empleado
-- Los usuarios de esta tabla tienen type_user = 6 en empleado_log
