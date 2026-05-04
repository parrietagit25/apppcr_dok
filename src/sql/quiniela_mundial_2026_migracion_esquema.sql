-- Borra tablas antiguas de quiniela (cualquier versión) para recrear con quiniela_mundial_2026.sql

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS quiniela_seleccion;
DROP TABLE IF EXISTS quiniela_oficial;
DROP TABLE IF EXISTS quiniela_carta;
DROP TABLE IF EXISTS quiniela_prediccion;
DROP TABLE IF EXISTS quiniela_carta_cerrada;
DROP TABLE IF EXISTS quiniela_partido;
DROP TABLE IF EXISTS quiniela_equipo;
DROP TABLE IF EXISTS quiniela_grupo;
SET FOREIGN_KEY_CHECKS = 1;
