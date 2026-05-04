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
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_grupo')->fetchColumn();
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

    /** Solo grupo + 4 equipos (sin partidos). */
    public function crearGrupoSoloEquipos(int $ordenGrupo, string $nombreGrupo, array $nombresCuatroEquipos): bool
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
            for ($s = 1; $s <= 4; $s++) {
                $insE->execute([$grupoId, $nombresCuatroEquipos[$s - 1], $s]);
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::crearGrupoSoloEquipos: ' . $e->getMessage());
            return false;
        }
    }

    public function puedeEliminarGrupo(int $grupoId): bool
    {
        $st = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_prediccion pr
             INNER JOIN quiniela_partido pt ON pt.id = pr.partido_id
             WHERE pt.grupo_id = ?'
        );
        $st->execute([$grupoId]);
        if ((int) $st->fetchColumn() > 0) {
            return false;
        }
        $st2 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_partido p
             INNER JOIN quiniela_partido hijo ON (
               hijo.src_partido_local_id = p.id OR hijo.src_partido_der_id = p.id
             )
             WHERE p.grupo_id = ?'
        );
        $st2->execute([$grupoId]);
        if ((int) $st2->fetchColumn() > 0) {
            return false;
        }
        $st3 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_partido hijo
             WHERE hijo.src_partido_local_id IN (SELECT id FROM quiniela_partido WHERE grupo_id = ?)
                OR hijo.src_partido_der_id IN (SELECT id FROM quiniela_partido WHERE grupo_id = ?)'
        );
        $st3->execute([$grupoId, $grupoId]);
        if ((int) $st3->fetchColumn() > 0) {
            return false;
        }
        return true;
    }

    public function eliminarGrupo(int $grupoId): bool
    {
        if (!$this->puedeEliminarGrupo($grupoId)) {
            return false;
        }
        $st = $this->pdo->prepare('DELETE FROM quiniela_grupo WHERE id = ?');
        return $st->execute([$grupoId]);
    }

    public function siguienteOrdenPartido(): int
    {
        $n = (int) $this->pdo->query('SELECT COALESCE(MAX(orden), 0) FROM quiniela_partido')->fetchColumn();
        return $n + 1;
    }

    public function agregarPartidoFijo(?int $grupoId, int $equipoLocalId, int $equipoVisitanteId, ?string $etiqueta = null): bool
    {
        if ($equipoLocalId === $equipoVisitanteId) {
            return false;
        }
        if (!$this->equiposExistenYGrupo($grupoId, $equipoLocalId, $equipoVisitanteId)) {
            return false;
        }
        $ord = $this->siguienteOrdenPartido();
        $st = $this->pdo->prepare(
            'INSERT INTO quiniela_partido (grupo_id, orden, tipo, etiqueta, equipo_local_id, equipo_visitante_id)
             VALUES (?, ?, \'fijo\', ?, ?, ?)'
        );
        return $st->execute([$grupoId, $ord, $etiqueta, $equipoLocalId, $equipoVisitanteId]);
    }

    public function agregarPartidoGanadores(?int $grupoId, int $srcPartidoA, int $srcPartidoB, ?string $etiqueta = null): bool
    {
        if ($srcPartidoA === $srcPartidoB) {
            return false;
        }
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_partido WHERE id IN (?,?)');
        $st->execute([$srcPartidoA, $srcPartidoB]);
        if ((int) $st->fetchColumn() !== 2) {
            return false;
        }
        if ($grupoId !== null) {
            $chk = $this->pdo->prepare(
                'SELECT COUNT(*) FROM quiniela_partido WHERE id IN (?,?) AND (grupo_id IS NULL OR grupo_id <> ?)'
            );
            $chk->execute([$srcPartidoA, $srcPartidoB, $grupoId]);
            if ((int) $chk->fetchColumn() > 0) {
                return false;
            }
        }
        $ord = $this->siguienteOrdenPartido();
        $ins = $this->pdo->prepare(
            'INSERT INTO quiniela_partido (grupo_id, orden, tipo, etiqueta, src_partido_local_id, src_partido_der_id)
             VALUES (?, ?, \'ganadores\', ?, ?, ?)'
        );
        return $ins->execute([$grupoId, $ord, $etiqueta, $srcPartidoA, $srcPartidoB]);
    }

    private function equiposExistenYGrupo(?int $grupoId, int $e1, int $e2): bool
    {
        if ($grupoId === null) {
            $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_equipo WHERE id IN (?,?)');
            $st->execute([$e1, $e2]);
            return (int) $st->fetchColumn() === 2;
        }
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_equipo WHERE id IN (?,?) AND grupo_id = ?');
        $st->execute([$e1, $e2, $grupoId]);
        return (int) $st->fetchColumn() === 2;
    }

    public function listarPartidosPorGrupo(?int $grupoId): array
    {
        if ($grupoId === null) {
            $sql = 'SELECT p.id, p.grupo_id, p.orden, p.tipo, p.etiqueta, p.ganador_id,
                           p.equipo_local_id, p.equipo_visitante_id, p.src_partido_local_id, p.src_partido_der_id,
                           el.nombre AS eq_loc_nom, ev.nombre AS eq_vis_nom,
                           g.nombre AS grupo_nom, g.orden_grupo
                    FROM quiniela_partido p
                    LEFT JOIN quiniela_equipo el ON el.id = p.equipo_local_id
                    LEFT JOIN quiniela_equipo ev ON ev.id = p.equipo_visitante_id
                    LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
                    WHERE p.grupo_id IS NULL
                    ORDER BY p.orden ASC, p.id ASC';
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        $st = $this->pdo->prepare(
            'SELECT p.id, p.grupo_id, p.orden, p.tipo, p.etiqueta, p.ganador_id,
                    p.equipo_local_id, p.equipo_visitante_id, p.src_partido_local_id, p.src_partido_der_id,
                    el.nombre AS eq_loc_nom, ev.nombre AS eq_vis_nom,
                    g.nombre AS grupo_nom, g.orden_grupo
             FROM quiniela_partido p
             LEFT JOIN quiniela_equipo el ON el.id = p.equipo_local_id
             LEFT JOIN quiniela_equipo ev ON ev.id = p.equipo_visitante_id
             LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
             WHERE p.grupo_id = ?
             ORDER BY p.orden ASC, p.id ASC'
        );
        $st->execute([$grupoId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosPartidosOrdenados(): array
    {
        $sql = 'SELECT p.id, p.grupo_id, p.orden, p.tipo, p.etiqueta, p.ganador_id,
                       p.equipo_local_id, p.equipo_visitante_id, p.src_partido_local_id, p.src_partido_der_id,
                       el.nombre AS eq_loc_nom, ev.nombre AS eq_vis_nom,
                       g.nombre AS grupo_nom, g.orden_grupo
                FROM quiniela_partido p
                LEFT JOIN quiniela_equipo el ON el.id = p.equipo_local_id
                LEFT JOIN quiniela_equipo ev ON ev.id = p.equipo_visitante_id
                LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
                ORDER BY p.orden ASC, p.id ASC';
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalPartidos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_partido')->fetchColumn();
    }

    public function puedeEliminarPartido(int $partidoId): bool
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_prediccion WHERE partido_id = ?');
        $st->execute([$partidoId]);
        if ((int) $st->fetchColumn() > 0) {
            return false;
        }
        $st2 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_partido WHERE src_partido_local_id = ? OR src_partido_der_id = ?'
        );
        $st2->execute([$partidoId, $partidoId]);
        return (int) $st2->fetchColumn() === 0;
    }

    public function eliminarPartido(int $partidoId): bool
    {
        if (!$this->puedeEliminarPartido($partidoId)) {
            return false;
        }
        $st = $this->pdo->prepare('DELETE FROM quiniela_partido WHERE id = ?');
        return $st->execute([$partidoId]);
    }

    /** Oficiales: ids de equipos que pueden ganar (vacío si aún no aplica). */
    public function candidatosOficialesGanador(int $partidoId): array
    {
        $row = $this->obtenerPartidoRaw($partidoId);
        if (!$row) {
            return [];
        }
        if ($row['tipo'] === 'fijo') {
            return [(int) $row['equipo_local_id'], (int) $row['equipo_visitante_id']];
        }
        $g1 = $this->ganadorOficialPartido((int) $row['src_partido_local_id']);
        $g2 = $this->ganadorOficialPartido((int) $row['src_partido_der_id']);
        if ($g1 === null || $g2 === null) {
            return [];
        }
        return [$g1, $g2];
    }

    public function ganadorOficialPartido(int $partidoId): ?int
    {
        $st = $this->pdo->prepare('SELECT ganador_id FROM quiniela_partido WHERE id = ?');
        $st->execute([$partidoId]);
        $v = $st->fetchColumn();
        return $v !== null && $v !== false ? (int) $v : null;
    }

    /** Con mapa partido_id => equipo_predicho (solo hojas y nodos ya resueltos en el mapa). */
    public function candidatosPrediccionGanador(int $partidoId, array $mapaPredicho): array
    {
        $row = $this->obtenerPartidoRaw($partidoId);
        if (!$row) {
            return [];
        }
        if ($row['tipo'] === 'fijo') {
            return [(int) $row['equipo_local_id'], (int) $row['equipo_visitante_id']];
        }
        $sa = (int) $row['src_partido_local_id'];
        $sb = (int) $row['src_partido_der_id'];
        if (!isset($mapaPredicho[$sa], $mapaPredicho[$sb])) {
            return [];
        }
        return [(int) $mapaPredicho[$sa], (int) $mapaPredicho[$sb]];
    }

    private function obtenerPartidoRaw(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM quiniela_partido WHERE id = ?');
        $st->execute([$id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function guardarGanadorPartido(int $partidoId, ?int $ganadorId): bool
    {
        if ($ganadorId === null || $ganadorId === 0) {
            $st = $this->pdo->prepare('UPDATE quiniela_partido SET ganador_id = NULL WHERE id = ?');
            return $st->execute([$partidoId]);
        }
        $opts = $this->candidatosOficialesGanador($partidoId);
        if (count($opts) !== 2 || !in_array((int) $ganadorId, $opts, true)) {
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

    public function guardarQuinielaUsuario(string $codigo, array $mapaPartidoGanadorPredicho): bool
    {
        if ($this->usuarioCartaCerrada($codigo)) {
            return false;
        }
        $total = $this->totalPartidos();
        if ($total === 0 || count($mapaPartidoGanadorPredicho) !== $total) {
            return false;
        }
        $stAll = $this->pdo->query('SELECT id FROM quiniela_partido');
        $todos = array_map('intval', $stAll->fetchAll(PDO::FETCH_COLUMN));
        $idsMap = array_map('intval', array_keys($mapaPartidoGanadorPredicho));
        sort($todos);
        sort($idsMap);
        if ($todos !== $idsMap) {
            return false;
        }
        $ordenados = $this->listarTodosPartidosOrdenados();
        foreach ($ordenados as $p) {
            $pid = (int) $p['id'];
            $eid = (int) $mapaPartidoGanadorPredicho[$pid];
            $opts = $this->candidatosPrediccionGanador($pid, $mapaPartidoGanadorPredicho);
            if (count($opts) !== 2 || !in_array($eid, $opts, true)) {
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
            $this->pdo->prepare('INSERT INTO quiniela_carta_cerrada (codigo_empleado) VALUES (?)')->execute([$codigo]);
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
        $partidos = $this->listarTodosPartidosOrdenados();
        $st = $this->pdo->prepare(
            'SELECT partido_id, equipo_predicho_id FROM quiniela_prediccion WHERE codigo_empleado = ?'
        );
        $st->execute([$codigo]);
        $pred = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $pred[(int) $r['partido_id']] = (int) $r['equipo_predicho_id'];
        }
        $out = [];
        foreach ($partidos as $p) {
            $pid = (int) $p['id'];
            if (!isset($pred[$pid])) {
                continue;
            }
            $desc = $this->etiquetaPartidoVista($p);
            $nomPred = $this->nombreEquipo($pred[$pid]);
            $resOf = $p['ganador_id'] ? $this->nombreEquipo((int) $p['ganador_id']) : null;
            $out[] = [
                'partido_id' => $pid,
                'descripcion' => $desc,
                'grupo_nombre' => $p['grupo_nom'] ?? 'Llave / final',
                'orden_grupo' => $p['orden_grupo'] ?? 0,
                'predicho_nombre' => $nomPred,
                'resultado_nombre' => $resOf,
                'tipo' => $p['tipo'],
            ];
        }
        return $out;
    }

    public function etiquetaPartidoVista(array $p): string
    {
        if ($p['tipo'] === 'fijo') {
            $a = $p['eq_loc_nom'] ?? '?';
            $b = $p['eq_vis_nom'] ?? '?';
            $base = $a . ' vs ' . $b;
        } else {
            $base = 'Ganador partido #' . (int) $p['src_partido_local_id']
                . ' vs Ganador partido #' . (int) $p['src_partido_der_id'];
        }
        if (!empty($p['etiqueta'])) {
            return $p['etiqueta'] . ' — ' . $base;
        }
        return $base;
    }

    public function nombreEquipo(int $id): string
    {
        $st = $this->pdo->prepare('SELECT nombre FROM quiniela_equipo WHERE id = ?');
        $st->execute([$id]);
        $n = $st->fetchColumn();
        return $n ? (string) $n : (string) $id;
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
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
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
            $out[] = [
                'codigo_empleado' => $code,
                'nombre' => $this->resolverNombreColaborador($pdoEmpleados, $code),
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

    public function listarTodosEquiposParaSelector(): array
    {
        $sql = 'SELECT e.id, e.nombre, g.nombre AS grupo_nom, g.orden_grupo
                FROM quiniela_equipo e
                INNER JOIN quiniela_grupo g ON g.id = e.grupo_id
                ORDER BY g.orden_grupo ASC, e.slot ASC';
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
