-- Migración: quiniela por fases (elimina partidos/predicciones antiguos).
-- Ejecutar DESPUÉS de backup si necesita conservar predicciones viejas.
-- Conserva quiniela_grupo y quiniela_equipo intactos.

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS quiniela_prediccion;
DROP TABLE IF EXISTS quiniela_partido;
DROP TABLE IF EXISTS quiniela_carta_cerrada;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS quiniela_carta (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_empleado VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci,
  fase_actual VARCHAR(32) NOT NULL DEFAULT 'grupos',
  cerrada TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = quiniela terminada (campeón)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_quiniela_carta_codigo (codigo_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_seleccion (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  carta_id INT UNSIGNED NOT NULL,
  fase VARCHAR(32) NOT NULL,
  grupo_id INT UNSIGNED NULL COMMENT 'Origen en fases grupos/mejores_terceros',
  equipo_id INT UNSIGNED NOT NULL,
  posicion TINYINT UNSIGNED NULL COMMENT 'Orden opcional dentro de la fase',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_sel_carta_fase (carta_id, fase),
  KEY idx_sel_equipo (equipo_id),
  CONSTRAINT fk_sel_carta FOREIGN KEY (carta_id) REFERENCES quiniela_carta (id) ON DELETE CASCADE,
  CONSTRAINT fk_sel_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE SET NULL,
  CONSTRAINT fk_sel_equipo FOREIGN KEY (equipo_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_oficial (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  fase VARCHAR(32) NOT NULL,
  grupo_id INT UNSIGNED NULL,
  equipo_id INT UNSIGNED NOT NULL,
  posicion TINYINT UNSIGNED NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_of_fase (fase),
  KEY idx_of_equipo (equipo_id),
  CONSTRAINT fk_of_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE SET NULL,
  CONSTRAINT fk_of_equipo FOREIGN KEY (equipo_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
