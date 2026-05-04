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
               hijo.src_partido_a_id = p.id OR hijo.src_partido_b_id = p.id
             )
             WHERE p.grupo_id = ?'
        );
        $st2->execute([$grupoId]);
        if ((int) $st2->fetchColumn() > 0) {
            return false;
        }
        $st3 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_partido hijo
             WHERE hijo.src_partido_a_id IN (SELECT id FROM quiniela_partido WHERE grupo_id = ?)
                OR hijo.src_partido_b_id IN (SELECT id FROM quiniela_partido WHERE grupo_id = ?)'
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

    public function agregarPartidoFijo(?int $grupoId, int $equipoAId, int $equipoBId, ?string $etiqueta = null, ?string $fase = null): bool
    {
        if ($equipoAId === $equipoBId) {
            return false;
        }
        if (!$this->equiposExistenYGrupo($grupoId, $equipoAId, $equipoBId)) {
            return false;
        }
        $ord = $this->siguienteOrdenPartido();
        $st = $this->pdo->prepare(
            'INSERT INTO quiniela_partido (grupo_id, orden, fase, tipo, etiqueta, equipo_a_id, equipo_b_id)
             VALUES (?, ?, ?, \'fijo\', ?, ?, ?)'
        );
        return $st->execute([$grupoId, $ord, $fase, $etiqueta, $equipoAId, $equipoBId]);
    }

    public function agregarPartidoGanadores(?int $grupoId, int $srcPartidoA, int $srcPartidoB, ?string $etiqueta = null, ?string $fase = null): bool
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
            'INSERT INTO quiniela_partido (grupo_id, orden, fase, tipo, etiqueta, src_partido_a_id, src_partido_b_id)
             VALUES (?, ?, ?, \'ganadores\', ?, ?, ?)'
        );
        return $ins->execute([$grupoId, $ord, $fase, $etiqueta, $srcPartidoA, $srcPartidoB]);
    }

    public function actualizarPartidoMeta(int $partidoId, int $orden, ?string $fase, ?string $etiqueta): bool
    {
        $fase = $fase === null || trim($fase) === '' ? null : trim($fase);
        $etq = $etiqueta === null || trim($etiqueta) === '' ? null : trim($etiqueta);
        $st = $this->pdo->prepare(
            'UPDATE quiniela_partido SET orden = ?, fase = ?, etiqueta = ? WHERE id = ?'
        );
        return $st->execute([$orden, $fase, $etq, $partidoId]);
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

    private function selectPartidoCampos(): string
    {
        return 'p.id, p.grupo_id, p.orden, p.fase, p.tipo, p.etiqueta, p.ganador_id,
                p.equipo_a_id, p.equipo_b_id, p.src_partido_a_id, p.src_partido_b_id,
                ea.nombre AS eq_a_nom, eb.nombre AS eq_b_nom,
                g.nombre AS grupo_nom, g.orden_grupo';
    }

    public function listarPartidosPorGrupo(?int $grupoId): array
    {
        $cols = $this->selectPartidoCampos();
        if ($grupoId === null) {
            $sql = "SELECT {$cols}
                    FROM quiniela_partido p
                    LEFT JOIN quiniela_equipo ea ON ea.id = p.equipo_a_id
                    LEFT JOIN quiniela_equipo eb ON eb.id = p.equipo_b_id
                    LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
                    WHERE p.grupo_id IS NULL
                    ORDER BY p.orden ASC, p.id ASC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        $st = $this->pdo->prepare(
            "SELECT {$cols}
             FROM quiniela_partido p
             LEFT JOIN quiniela_equipo ea ON ea.id = p.equipo_a_id
             LEFT JOIN quiniela_equipo eb ON eb.id = p.equipo_b_id
             LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
             WHERE p.grupo_id = ?
             ORDER BY p.orden ASC, p.id ASC"
        );
        $st->execute([$grupoId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosPartidosOrdenados(): array
    {
        $cols = $this->selectPartidoCampos();
        $sql = "SELECT {$cols}
                FROM quiniela_partido p
                LEFT JOIN quiniela_equipo ea ON ea.id = p.equipo_a_id
                LEFT JOIN quiniela_equipo eb ON eb.id = p.equipo_b_id
                LEFT JOIN quiniela_grupo g ON g.id = p.grupo_id
                ORDER BY p.orden ASC, p.id ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalPartidos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_partido')->fetchColumn();
    }

    public function totalPartidosConResultadoOficial(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_partido WHERE ganador_id IS NOT NULL')->fetchColumn();
    }

    public function puedeEliminarPartido(int $partidoId): bool
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_prediccion WHERE partido_id = ?');
        $st->execute([$partidoId]);
        if ((int) $st->fetchColumn() > 0) {
            return false;
        }
        $st2 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_partido WHERE src_partido_a_id = ? OR src_partido_b_id = ?'
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

    public function candidatosOficialesGanador(int $partidoId): array
    {
        $row = $this->obtenerPartidoRaw($partidoId);
        if (!$row) {
            return [];
        }
        if ($row['tipo'] === 'fijo') {
            return [(int) $row['equipo_a_id'], (int) $row['equipo_b_id']];
        }
        $g1 = $this->ganadorOficialPartido((int) $row['src_partido_a_id']);
        $g2 = $this->ganadorOficialPartido((int) $row['src_partido_b_id']);
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

    public function candidatosPrediccionGanador(int $partidoId, array $mapaPredicho): array
    {
        $row = $this->obtenerPartidoRaw($partidoId);
        if (!$row) {
            return [];
        }
        if ($row['tipo'] === 'fijo') {
            return [(int) $row['equipo_a_id'], (int) $row['equipo_b_id']];
        }
        $sa = (int) $row['src_partido_a_id'];
        $sb = (int) $row['src_partido_b_id'];
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
        $st = $this->pdo->prepare(
            'SELECT cerrada FROM quiniela_carta_cerrada WHERE codigo_empleado = ? LIMIT 1'
        );
        $st->execute([$codigo]);
        $v = $st->fetchColumn();
        return (int) $v === 1;
    }

    public function obtenerMapaPredicciones(string $codigo): array
    {
        $st = $this->pdo->prepare(
            'SELECT partido_id, ganador_id FROM quiniela_prediccion WHERE codigo_empleado = ?'
        );
        $st->execute([$codigo]);
        $m = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $m[(int) $r['partido_id']] = (int) $r['ganador_id'];
        }
        return $m;
    }

    /**
     * Construye mapa partido_id => ganador_id válido según orden y reglas (solo lo enviado en POST que encadena bien).
     *
     * @param array<int,int|string> $postPred partido_id => ganador_id
     * @return array<int,int>
     */
    public function construirMapaPredichoValido(array $postPred): array
    {
        $postPred = array_map('intval', $postPred);
        $ordenados = $this->listarTodosPartidosOrdenados();
        $valid = [];
        foreach ($ordenados as $p) {
            $pid = (int) $p['id'];
            if ($p['tipo'] === 'fijo') {
                if (!isset($postPred[$pid])) {
                    continue;
                }
                $g = (int) $postPred[$pid];
                $a = (int) $p['equipo_a_id'];
                $b = (int) $p['equipo_b_id'];
                if (in_array($g, [$a, $b], true)) {
                    $valid[$pid] = $g;
                }
                continue;
            }
            $sa = (int) $p['src_partido_a_id'];
            $sb = (int) $p['src_partido_b_id'];
            if (!isset($valid[$sa], $valid[$sb])) {
                continue;
            }
            if (!isset($postPred[$pid])) {
                continue;
            }
            $g = (int) $postPred[$pid];
            $c1 = $valid[$sa];
            $c2 = $valid[$sb];
            if (in_array($g, [$c1, $c2], true)) {
                $valid[$pid] = $g;
            }
        }
        return $valid;
    }

    public function guardarProgresoPredicciones(string $codigo, array $postPred): bool
    {
        if ($this->usuarioCartaCerrada($codigo)) {
            return false;
        }
        $valid = $this->construirMapaPredichoValido($postPred);
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM quiniela_prediccion WHERE codigo_empleado = ?')->execute([$codigo]);
            if (count($valid) > 0) {
                $ins = $this->pdo->prepare(
                    'INSERT INTO quiniela_prediccion (codigo_empleado, partido_id, ganador_id) VALUES (?,?,?)'
                );
                foreach ($valid as $pid => $gid) {
                    $ins->execute([$codigo, (int) $pid, (int) $gid]);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::guardarProgresoPredicciones: ' . $e->getMessage());
            return false;
        }
    }

    public function confirmarCartaColaborador(string $codigo): bool
    {
        if ($this->usuarioCartaCerrada($codigo)) {
            return false;
        }
        $map = $this->obtenerMapaPredicciones($codigo);
        $total = $this->totalPartidos();
        if ($total === 0 || count($map) !== $total) {
            return false;
        }
        $ordenados = $this->listarTodosPartidosOrdenados();
        foreach ($ordenados as $p) {
            $pid = (int) $p['id'];
            if (!isset($map[$pid])) {
                return false;
            }
            $opts = $this->candidatosPrediccionGanador($pid, $map);
            if (count($opts) !== 2 || !in_array($map[$pid], $opts, true)) {
                return false;
            }
        }
        $st = $this->pdo->prepare(
            'INSERT INTO quiniela_carta_cerrada (codigo_empleado, cerrada) VALUES (?, 1)
             ON DUPLICATE KEY UPDATE cerrada = 1, updated_at = CURRENT_TIMESTAMP'
        );
        return $st->execute([$codigo]);
    }

    public function obtenerPrediccionesUsuarioDetalle(string $codigo): array
    {
        $partidos = $this->listarTodosPartidosOrdenados();
        $pred = $this->obtenerMapaPredicciones($codigo);
        $out = [];
        foreach ($partidos as $p) {
            $pid = (int) $p['id'];
            if (!isset($pred[$pid])) {
                continue;
            }
            $desc = $this->etiquetaPartidoVista($p);
            $nomPred = $this->nombreEquipo($pred[$pid]);
            $gidOf = $p['ganador_id'] ?? null;
            $resOf = ($gidOf !== null && $gidOf !== '' && (int) $gidOf > 0)
                ? $this->nombreEquipo((int) $gidOf)
                : null;
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
            $a = $p['eq_a_nom'] ?? '?';
            $b = $p['eq_b_nom'] ?? '?';
            $base = $a . ' vs ' . $b;
        } else {
            $base = 'Ganador partido #' . (int) $p['src_partido_a_id']
                . ' vs Ganador partido #' . (int) $p['src_partido_b_id'];
        }
        $fase = !empty($p['fase']) ? '[' . $p['fase'] . '] ' : '';
        if (!empty($p['etiqueta'])) {
            return $fase . $p['etiqueta'] . ' — ' . $base;
        }
        return $fase . $base;
    }

    public function textoDependenciaPartido(array $p): string
    {
        if ($p['tipo'] !== 'ganadores') {
            return '—';
        }
        return '#' . (int) $p['src_partido_a_id'] . ' y #' . (int) $p['src_partido_b_id'];
    }

    public function nombreEquipo(int $id): string
    {
        $st = $this->pdo->prepare('SELECT nombre FROM quiniela_equipo WHERE id = ?');
        $st->execute([$id]);
        $n = $st->fetchColumn();
        return $n ? (string) $n : (string) $id;
    }

    /** Pendiente | En juego | Perdió | Completada */
    public function estadoColaboradorQuiniela(string $codigo): string
    {
        if (!$this->usuarioCartaCerrada($codigo)) {
            return 'Pendiente';
        }
        $sql = 'SELECT p.ganador_id, pr.ganador_id AS pred_ganador
                FROM quiniela_prediccion pr
                INNER JOIN quiniela_partido p ON p.id = pr.partido_id
                WHERE pr.codigo_empleado = ? AND p.ganador_id IS NOT NULL';
        $st = $this->pdo->prepare($sql);
        $st->execute([$codigo]);
        $fallo = false;
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            if ((int) $r['ganador_id'] !== (int) $r['pred_ganador']) {
                $fallo = true;
                break;
            }
        }
        if ($fallo) {
            return 'Perdió';
        }
        $total = $this->totalPartidos();
        $conOf = $this->totalPartidosConResultadoOficial();
        if ($total > 0 && $conOf === $total) {
            return 'Completada';
        }
        return 'En juego';
    }

    /** @return list<array{codigo_empleado: string, nombre: string, status: string}> */
    public function listarResumenColaboradoresQuiniela(PDO $pdoEmpleados): array
    {
        $sql = 'SELECT codigo_empleado FROM (
                    SELECT DISTINCT codigo_empleado FROM quiniela_prediccion
                    UNION
                    SELECT codigo_empleado FROM quiniela_carta_cerrada WHERE cerrada = 1
                ) t ORDER BY codigo_empleado ASC';
        $codes = $this->pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        $out = [];
        foreach ($codes as $code) {
            $code = (string) $code;
            $out[] = [
                'codigo_empleado' => $code,
                'nombre' => $this->resolverNombreColaborador($pdoEmpleados, $code),
                'status' => $this->estadoColaboradorQuiniela($code),
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
