-- Asignación de instructivos restringidos por colaborador
CREATE TABLE IF NOT EXISTS instructivos_asignacion (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    documento_codigo VARCHAR(64) NOT NULL,
    codigo_empleado VARCHAR(32) NOT NULL,
    asignado_por VARCHAR(32) DEFAULT NULL,
    fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_instructivo_empleado (documento_codigo, codigo_empleado),
    KEY idx_codigo_empleado (codigo_empleado),
    KEY idx_documento (documento_codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
