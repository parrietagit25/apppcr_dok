-- Quiniela Mundial 2026 — esquema con partidos definidos por el admin (fijo / entre ganadores).
-- Si ya tenía tablas de la versión anterior, ejecute antes:
--   DROP TABLE IF EXISTS quiniela_prediccion; DROP TABLE IF EXISTS quiniela_carta_cerrada;
--   DROP TABLE IF EXISTS quiniela_partido; DROP TABLE IF EXISTS quiniela_equipo; DROP TABLE IF EXISTS quiniela_grupo;

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS quiniela_grupo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(120) NOT NULL,
  orden_grupo TINYINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orden_grupo (orden_grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_equipo (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  grupo_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  slot TINYINT UNSIGNED NOT NULL COMMENT '1-4',
  PRIMARY KEY (id),
  UNIQUE KEY uq_grupo_slot (grupo_id, slot),
  CONSTRAINT fk_equipo_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_partido (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  grupo_id INT UNSIGNED NULL COMMENT 'NULL = llave entre grupos / camino al campeón',
  orden INT NOT NULL DEFAULT 0 COMMENT 'Orden de captura (menor = antes)',
  tipo ENUM('fijo','ganadores') NOT NULL DEFAULT 'fijo',
  etiqueta VARCHAR(200) NULL,
  equipo_local_id INT UNSIGNED NULL,
  equipo_visitante_id INT UNSIGNED NULL,
  src_partido_local_id INT UNSIGNED NULL COMMENT 'Ganador de este partido vs ...',
  src_partido_der_id INT UNSIGNED NULL,
  ganador_id INT UNSIGNED NULL,
  PRIMARY KEY (id),
  KEY idx_p_grupo (grupo_id),
  KEY idx_p_orden (orden),
  CONSTRAINT fk_p_grupo FOREIGN KEY (grupo_id) REFERENCES quiniela_grupo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_el FOREIGN KEY (equipo_local_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_ev FOREIGN KEY (equipo_visitante_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE,
  CONSTRAINT fk_p_sl FOREIGN KEY (src_partido_local_id) REFERENCES quiniela_partido (id) ON DELETE RESTRICT,
  CONSTRAINT fk_p_sd FOREIGN KEY (src_partido_der_id) REFERENCES quiniela_partido (id) ON DELETE RESTRICT,
  CONSTRAINT fk_p_gan FOREIGN KEY (ganador_id) REFERENCES quiniela_equipo (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_prediccion (
  codigo_empleado VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci,
  partido_id INT UNSIGNED NOT NULL,
  equipo_predicho_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (codigo_empleado, partido_id),
  KEY idx_pred_partido (partido_id),
  CONSTRAINT fk_pred_partido FOREIGN KEY (partido_id) REFERENCES quiniela_partido (id) ON DELETE CASCADE,
  CONSTRAINT fk_pred_equipo FOREIGN KEY (equipo_predicho_id) REFERENCES quiniela_equipo (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiniela_carta_cerrada (
  codigo_empleado VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci,
  cerrada_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (codigo_empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
