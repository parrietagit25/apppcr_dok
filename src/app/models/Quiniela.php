<?php

class Quiniela
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function contarGrupos(): int
    {
        $n = (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_grupo')->fetchColumn();
        return $n;
    }

    public function listarGruposConEquipos(): array
    {
        $sql = 'SELECT g.id, g.nombre AS nombre_grupo, g.orden_grupo,
                       e.id AS equipo_id, e.nombre AS equipo_nombre, e.slot
                FROM quiniela_grupo g
                LEFT JOIN quiniela_equipo e ON e.grupo_id = g.id
                ORDER BY g.orden_grupo ASC, e.slot ASC';
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $gid = (int) $r['id'];
            if (!isset($out[$gid])) {
                $out[$gid] = [
                    'id' => $gid,
                    'nombre_grupo' => $r['nombre_grupo'],
                    'orden_grupo' => (int) $r['orden_grupo'],
                    'equipos' => [],
                ];
            }
            if ($r['equipo_id'] !== null) {
                $out[$gid]['equipos'][] = [
                    'id' => (int) $r['equipo_id'],
                    'nombre' => $r['equipo_nombre'],
                    'slot' => (int) $r['slot'],
                ];
            }
        }
        return array_values($out);
    }

    /** Crea grupo con 4 equipos y los 6 partidos de fase de grupos (todos contra todos). */
    public function crearGrupoConEquipos(int $ordenGrupo, string $nombreGrupo, array $nombresCuatroEquipos): bool
    {
        $nombresCuatroEquipos = array_values(array_map('trim', $nombresCuatroEquipos));
        if (count($nombresCuatroEquipos) !== 4) {
            return false;
        }
        foreach ($nombresCuatroEquipos as $n) {
            if ($n === '') {
                return false;
            }
        }
        if ($ordenGrupo < 1 || $ordenGrupo > 12) {
            return false;
        }

        $chk = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_grupo WHERE orden_grupo = ?');
        $chk->execute([$ordenGrupo]);
        if ((int) $chk->fetchColumn() > 0) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare('INSERT INTO quiniela_grupo (nombre, orden_grupo) VALUES (?, ?)');
            $st->execute([$nombreGrupo, $ordenGrupo]);
            $grupoId = (int) $this->pdo->lastInsertId();

            $insE = $this->pdo->prepare('INSERT INTO quiniela_equipo (grupo_id, nombre, slot) VALUES (?, ?, ?)');
            $ids = [];
            for ($s = 1; $s <= 4; $s++) {
                $insE->execute([$grupoId, $nombresCuatroEquipos[$s - 1], $s]);
                $ids[] = (int) $this->pdo->lastInsertId();
            }

            $pares = [
                [0, 1], [0, 2], [0, 3], [1, 2], [1, 3], [2, 3],
            ];
            $insP = $this->pdo->prepare(
                'INSERT INTO quiniela_partido (grupo_id, equipo_local_id, equipo_visitante_id) VALUES (?, ?, ?)'
            );
            foreach ($pares as [$i, $j]) {
                $insP->execute([$grupoId, $ids[$i], $ids[$j]]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::crearGrupoConEquipos: ' . $e->getMessage());
            return false;
        }
    }

    public function puedeEliminarGrupo(int $grupoId): bool
    {
        $sql = 'SELECT COUNT(*) FROM quiniela_prediccion p
                INNER JOIN quiniela_partido pt ON pt.id = p.partido_id
                WHERE pt.grupo_id = ?';
        $st = $this->pdo->prepare($sql);
        $st->execute([$grupoId]);
        return (int) $st->fetchColumn() === 0;
    }

    public function eliminarGrupo(int $grupoId): bool
    {
        if (!$this->puedeEliminarGrupo($grupoId)) {
            return false;
        }
        $st = $this->pdo->prepare('DELETE FROM quiniela_grupo WHERE id = ?');
        return $st->execute([$grupoId]);
    }

    public function totalPartidos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_partido')->fetchColumn();
    }

    public function listarPartidosParaResultados(): array
    {
        $sql = 'SELECT p.id, p.ganador_id,
                       el.nombre AS local_nombre, el.id AS local_id,
                       ev.nombre AS visita_nombre, ev.id AS visita_id,
                       g.nombre AS grupo_nombre, g.orden_grupo
                FROM quiniela_partido p
                INNER JOIN quiniela_equipo el ON el.id = p.equipo_local_id
                INNER JOIN quiniela_equipo ev ON ev.id = p.equipo_visitante_id
                INNER JOIN quiniela_grupo g ON g.id = p.grupo_id
                ORDER BY g.orden_grupo ASC, p.id ASC';
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarGanadorPartido(int $partidoId, ?int $ganadorId): bool
    {
        if ($ganadorId === null || $ganadorId === 0) {
            $st = $this->pdo->prepare('UPDATE quiniela_partido SET ganador_id = NULL WHERE id = ?');
            return $st->execute([$partidoId]);
        }
        $st = $this->pdo->prepare(
            'SELECT equipo_local_id, equipo_visitante_id FROM quiniela_partido WHERE id = ?'
        );
        $st->execute([$partidoId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $ok = in_array((int) $ganadorId, [(int) $row['equipo_local_id'], (int) $row['equipo_visitante_id']], true);
        if (!$ok) {
            return false;
        }
        $up = $this->pdo->prepare('UPDATE quiniela_partido SET ganador_id = ? WHERE id = ?');
        return $up->execute([$ganadorId, $partidoId]);
    }

    public function usuarioCartaCerrada(string $codigo): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM quiniela_carta_cerrada WHERE codigo_empleado = ? LIMIT 1');
        $st->execute([$codigo]);
        return (bool) $st->fetchColumn();
    }

    public function listarPartidosParaPrediccion(): array
    {
        return $this->listarPartidosParaResultados();
    }

    public function guardarQuinielaUsuario(string $codigo, array $mapaPartidoGanadorPredicho): bool
    {
        if ($this->usuarioCartaCerrada($codigo)) {
            return false;
        }
        $total = $this->totalPartidos();
        if ($total === 0 || count($mapaPartidoGanadorPredicho) !== $total) {
            return false;
        }

        $stVal = $this->pdo->prepare(
            'SELECT equipo_local_id, equipo_visitante_id FROM quiniela_partido WHERE id = ?'
        );
        foreach ($mapaPartidoGanadorPredicho as $pid => $eid) {
            $pid = (int) $pid;
            $eid = (int) $eid;
            $stVal->execute([$pid]);
            $row = $stVal->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false;
            }
            if (!in_array($eid, [(int) $row['equipo_local_id'], (int) $row['equipo_visitante_id']], true)) {
                return false;
            }
        }

        $this->pdo->beginTransaction();
        try {
            $ins = $this->pdo->prepare(
                'INSERT INTO quiniela_prediccion (codigo_empleado, partido_id, equipo_predicho_id) VALUES (?, ?, ?)'
            );
            foreach ($mapaPartidoGanadorPredicho as $pid => $eid) {
                $ins->execute([$codigo, (int) $pid, (int) $eid]);
            }
            $this->pdo->prepare(
                'INSERT INTO quiniela_carta_cerrada (codigo_empleado) VALUES (?)'
            )->execute([$codigo]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::guardarQuinielaUsuario: ' . $e->getMessage());
            return false;
        }
    }

    public function obtenerPrediccionesUsuarioDetalle(string $codigo): array
    {
        $sql = 'SELECT p.id AS partido_id,
                       el.nombre AS local_nombre, ev.nombre AS visita_nombre,
                       g.nombre AS grupo_nombre, g.orden_grupo,
                       pr.equipo_predicho_id AS predicho_id,
                       el2.nombre AS predicho_nombre,
                       p.ganador_id,
                       eg.nombre AS resultado_nombre
                FROM quiniela_prediccion pr
                INNER JOIN quiniela_partido p ON p.id = pr.partido_id
                INNER JOIN quiniela_equipo el ON el.id = p.equipo_local_id
                INNER JOIN quiniela_equipo ev ON ev.id = p.equipo_visitante_id
                INNER JOIN quiniela_grupo g ON g.id = p.grupo_id
                INNER JOIN quiniela_equipo el2 ON el2.id = pr.equipo_predicho_id
                LEFT JOIN quiniela_equipo eg ON eg.id = p.ganador_id
                WHERE pr.codigo_empleado = ?
                ORDER BY g.orden_grupo ASC, p.id ASC';
        $st = $this->pdo->prepare($sql);
        $st->execute([$codigo]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estadoQuinielaUsuario(string $codigo): string
    {
        if (!$this->usuarioCartaCerrada($codigo)) {
            return 'Sin quiniela';
        }
        $sql = 'SELECT p.ganador_id, pr.equipo_predicho_id
                FROM quiniela_prediccion pr
                INNER JOIN quiniela_partido p ON p.id = pr.partido_id
                WHERE pr.codigo_empleado = ? AND p.ganador_id IS NOT NULL';
        $st = $this->pdo->prepare($sql);
        $st->execute([$codigo]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            if ((int) $r['ganador_id'] !== (int) $r['equipo_predicho_id']) {
                return 'Perdio';
            }
        }
        return 'En juego';
    }

    public function listarColaboradoresConQuiniela(PDO $pdoEmpleados): array
    {
        $sql = 'SELECT c.codigo_empleado, c.cerrada_at FROM quiniela_carta_cerrada c ORDER BY c.cerrada_at DESC';
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $code = $r['codigo_empleado'];
            $nombre = $this->resolverNombreColaborador($pdoEmpleados, $code);
            $out[] = [
                'codigo_empleado' => $code,
                'nombre' => $nombre,
                'status' => $this->estadoQuinielaUsuario($code),
                'cerrada_at' => $r['cerrada_at'],
            ];
        }
        return $out;
    }

    public function detalleJsonColaborador(string $codigo): array
    {
        return [
            'codigo' => $codigo,
            'predicciones' => $this->obtenerPrediccionesUsuarioDetalle($codigo),
        ];
    }

    private function resolverNombreColaborador(PDO $pdo, string $code): string
    {
        $st = $pdo->prepare('SELECT nombre, apellido FROM empleados WHERE codigo_empleado = ? LIMIT 1');
        $st->execute([$code]);
        if ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            return trim($row['nombre'] . ' ' . ($row['apellido'] ?? ''));
        }
        $st2 = $pdo->prepare('SELECT nombre, apellido FROM colaboradores_externos WHERE codigo_empleado = ? LIMIT 1');
        $st2->execute([$code]);
        if ($row2 = $st2->fetch(PDO::FETCH_ASSOC)) {
            return trim($row2['nombre'] . ' ' . ($row2['apellido'] ?? ''));
        }
        return $code;
    }
}
