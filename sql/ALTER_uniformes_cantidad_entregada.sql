-- Piezas solicitadas = uniformes.cantidad (no editar desde RRHH)
-- Piezas entregadas = uniformes.cantidad_entregada (solo RRHH al marcar Entregado)

ALTER TABLE uniformes
ADD COLUMN cantidad_entregada INT NULL DEFAULT NULL
COMMENT 'Unidades entregadas al colaborador (registradas por RRHH)'
AFTER cantidad;

-- Opcional: alinear histórico ya marcado como entregado (copia solicitado = entregado)
-- UPDATE uniformes SET cantidad_entregada = cantidad WHERE stat = 3 AND cantidad_entregada IS NULL;
