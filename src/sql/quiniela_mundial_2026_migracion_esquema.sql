-- Si ya instaló la versión anterior (partidos solo round-robin), ejecute esto
-- y luego vuelva a ejecutar quiniela_mundial_2026.sql completo.

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS quiniela_prediccion;
DROP TABLE IF EXISTS quiniela_carta_cerrada;
DROP TABLE IF EXISTS quiniela_partido;
DROP TABLE IF EXISTS quiniela_equipo;
DROP TABLE IF EXISTS quiniela_grupo;
SET FOREIGN_KEY_CHECKS = 1;

-- Después corra: src/sql/quiniela_mundial_2026.sql
