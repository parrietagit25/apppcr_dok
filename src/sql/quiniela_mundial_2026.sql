-- Quiniela Mundial 2026 — bracket / llave (admin define estructura).
-- Migración desde versión anterior: ejecute quiniela_mundial_2026_migracion_esquema.sql y luego este archivo.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS quiniela_grupo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  orden_grupo TINYINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orden_grupo (orden_grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_equipo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  grupo_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  slot TINYINT UNSIGNED NOT NULL COMMENT '1-4',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_grupo_slot (grupo_id, slot),
  CONSTRAINT fk_equipo_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_partido (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  grupo_id INT UNSIGNED NULL COMMENT 'NULL = llave / eliminatoria entre grupos',
  orden INT NOT NULL DEFAULT 0,
  fase VARCHAR(80) NULL COMMENT 'Ej. Grupos, Octavos, Semifinal, Final',
  tipo ENUM('fijo','ganadores') NOT NULL DEFAULT 'fijo',
  etiqueta VARCHAR(200) NULL,
  equipo_a_id INT UNSIGNED NULL,
  equipo_b_id INT UNSIGNED NULL,
  src_partido_a_id INT UNSIGNED NULL,
  src_partido_b_id INT UNSIGNED NULL,
  ganador_id INT UNSIGNED NULL COMMENT 'Resultado oficial',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_p_grupo (grupo_id),
  KEY idx_p_orden (orden),
  CONSTRAINT fk_p_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_eqa FOREIGN KEY (equipo_a_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_eqb FOREIGN KEY (equipo_b_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_srca FOREIGN KEY (src_partido_a_id) REFERENCES quiniela_partido (id) ON DELETE RESTRICT,
  CONSTRAINT fk_p_srcb FOREIGN KEY (src_partido_b_id) REFERENCES quiniela_partido (id) ON DELETE RESTRICT,
  CONSTRAINT fk_p_gan FOREIGN KEY (ganador_id) REFERENCES quiniela_equipo (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_prediccion (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_empleado VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci,
  partido_id INT UNSIGNED NOT NULL,
  ganador_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pred_codigo_partido (codigo_empleado, partido_id),
  KEY idx_pred_partido (partido_id),
  CONSTRAINT fk_pred_partido FOREIGN KEY (partido_id) REFERENCES quiniela_partido (id) ON DELETE CASCADE,
  CONSTRAINT fk_pred_equipo FOREIGN KEY (ganador_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_carta_cerrada (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  codigo_empleado VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci,
  cerrada TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = quiniela confirmada, no editable',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_carta_codigo (codigo_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
