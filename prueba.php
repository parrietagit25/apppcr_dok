<?php 

        SELECT 
        el.pass
        FROM   empleado_log      el
        JOIN   empleados         e   ON e.codigo_empleado = el.codigo
        WHERE  el.codigo = '2475'
          AND  el.stat  = 1
          AND (e.estatus_empleado IN ('A','V')
                  OR EXISTS (
                        SELECT 1
                        FROM   usuarios_fuera_planilla ufp
                        WHERE  ufp.codigo_empleado = e.codigo_empleado
                          AND  ufp.stat = 1
                  )
              )



SELECT el.pass
FROM   empleado_log el
JOIN   empleados    e   ON e.codigo_empleado = el.codigo
WHERE  el.codigo = '002490'
  AND  el.stat  = 1
  AND (
        e.estatus_empleado IN ('A','V')
        OR EXISTS (
              SELECT 1
              FROM   usuarios_fuera_planilla ufp
              WHERE  ufp.codigo_empleado COLLATE utf8mb4_unicode_ci
                        = e.codigo_empleado COLLATE utf8mb4_unicode_ci
                AND  ufp.stat = 1
        )
      )