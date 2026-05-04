<?php

require_once __DIR__ . '/../config/quiniela_paises_mundial.php';

class Quiniela
{
    public const F_GRUPOS = 'grupos';
    public const F_MEJORES_TERCEROS = 'mejores_terceros';
    public const F_DIECISEISAVOS = 'dieciseisavos';
    public const F_OCTAVOS = 'octavos';
    public const F_CUARTOS = 'cuartos';
    public const F_SEMIFINAL = 'semifinal';
    public const F_FINAL = 'final';

    private $pdo;

    /** @var array<int, array{id:int,nombre:string,iso:?string,flag_url:string}> */
    private $equipoCache = [];

    /** @var list<array>|null */
    private $cacheListarTodosEquipos = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return list<string> */
    public static function ordenFases(): array
    {
        return [
            self::F_GRUPOS,
            self::F_MEJORES_TERCEROS,
            self::F_DIECISEISAVOS,
            self::F_OCTAVOS,
            self::F_CUARTOS,
            self::F_SEMIFINAL,
            self::F_FINAL,
        ];
    }

    public static function etiquetaFase(string $f): string
    {
        $m = [
            self::F_GRUPOS => 'Fase de grupos',
            self::F_MEJORES_TERCEROS => 'Mejores terceros',
            self::F_DIECISEISAVOS => 'Dieciseisavos',
            self::F_OCTAVOS => 'Octavos de final',
            self::F_CUARTOS => 'Cuartos de final',
            self::F_SEMIFINAL => 'Semifinal',
            self::F_FINAL => 'Final — Campeón',
        ];
        return $m[$f] ?? $f;
    }

    /** Selecciones requeridas por fase (excepto grupos: 2 por grupo). */
    public static function cuentaEsperadaFase(string $fase): int
    {
        switch ($fase) {
            case self::F_MEJORES_TERCEROS:
                return 8;
            case self::F_DIECISEISAVOS:
                return 16;
            case self::F_OCTAVOS:
                return 8;
            case self::F_CUARTOS:
                return 4;
            case self::F_SEMIFINAL:
                return 2;
            case self::F_FINAL:
                return 1;
            default:
                return 0;
        }
    }

    public static function siguienteFase(?string $actual): ?string
    {
        $ord = self::ordenFases();
        $i = array_search($actual, $ord, true);
        if ($i === false) {
            return self::F_GRUPOS;
        }
        return isset($ord[(int) $i + 1]) ? $ord[(int) $i + 1] : null;
    }

    public function contarGrupos(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM quiniela_grupo')->fetchColumn();
    }

    public function listarGruposConEquipos(): array
    {
        $sql = 'SELECT g.id, g.nombre AS nombre_grupo, g.orden_grupo,
                       e.id AS equipo_id, e.nombre AS equipo_nombre, e.slot, e.iso AS equipo_iso
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
                $iso = $r['equipo_iso'] ?? null;
                $iso = $iso !== null && $iso !== '' ? strtolower(substr((string) $iso, 0, 2)) : null;
                $out[$gid]['equipos'][] = [
                    'id' => (int) $r['equipo_id'],
                    'nombre' => $r['equipo_nombre'],
                    'slot' => (int) $r['slot'],
                    'iso' => $iso,
                    'flag_url' => quiniela_get_flag_url($iso),
                ];
            }
        }
        return array_values($out);
    }

    /**
     * @param list<array{nombre: string, iso?: ?string}> $equiposCuatro
     */
    public function crearGrupoSoloEquipos(int $ordenGrupo, string $nombreGrupo, array $equiposCuatro): bool
    {
        $equiposCuatro = array_values($equiposCuatro);
        if (count($equiposCuatro) !== 4) {
            return false;
        }
        foreach ($equiposCuatro as $row) {
            $n = trim((string) ($row['nombre'] ?? ''));
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

            $insE = $this->pdo->prepare(
                'INSERT INTO quiniela_equipo (grupo_id, nombre, slot, iso) VALUES (?, ?, ?, ?)'
            );
            for ($s = 1; $s <= 4; $s++) {
                $row = $equiposCuatro[$s - 1];
                $nom = trim((string) $row['nombre']);
                $isoRaw = $row['iso'] ?? null;
                $iso = null;
                if ($isoRaw !== null && $isoRaw !== '') {
                    $iso = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', (string) $isoRaw), 0, 2));
                    if (strlen($iso) !== 2) {
                        $iso = null;
                    }
                }
                if ($iso === null) {
                    $iso = quiniela_paises_iso_por_nombre($nom);
                }
                $insE->execute([$grupoId, $nom, $s, $iso]);
            }

            $this->pdo->commit();
            $this->cacheListarTodosEquipos = null;
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
            'SELECT COUNT(*) FROM quiniela_seleccion s
             INNER JOIN quiniela_equipo e ON e.id = s.equipo_id
             WHERE e.grupo_id = ?'
        );
        $st->execute([$grupoId]);
        if ((int) $st->fetchColumn() > 0) {
            return false;
        }
        $st2 = $this->pdo->prepare(
            'SELECT COUNT(*) FROM quiniela_oficial o
             INNER JOIN quiniela_equipo e ON e.id = o.equipo_id
             WHERE e.grupo_id = ?'
        );
        $st2->execute([$grupoId]);
        return (int) $st2->fetchColumn() === 0;
    }

    public function eliminarGrupo(int $grupoId): bool
    {
        if (!$this->puedeEliminarGrupo($grupoId)) {
            return false;
        }
        $st = $this->pdo->prepare('DELETE FROM quiniela_grupo WHERE id = ?');
        return $st->execute([$grupoId]);
    }

    // ——— Carta y fases (colaborador) ———

    public function obtenerCartaPorCodigo(string $codigo): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM quiniela_carta WHERE codigo_empleado = ? LIMIT 1');
        $st->execute([$codigo]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function crearCartaSiNoExiste(string $codigo): int
    {
        $row = $this->obtenerCartaPorCodigo($codigo);
        if ($row) {
            return (int) $row['id'];
        }
        $st = $this->pdo->prepare(
            'INSERT INTO quiniela_carta (codigo_empleado, fase_actual, cerrada) VALUES (?, ?, 0)'
        );
        $st->execute([$codigo, self::F_GRUPOS]);
        return (int) $this->pdo->lastInsertId();
    }

    public function quinielaEstaCerrada(string $codigo): bool
    {
        $c = $this->obtenerCartaPorCodigo($codigo);
        return $c ? (int) $c['cerrada'] === 1 : false;
    }

    /** Alias compatibilidad */
    public function usuarioCartaCerrada(string $codigo): bool
    {
        return $this->quinielaEstaCerrada($codigo);
    }

    public function obtenerFaseActual(string $codigo): string
    {
        $c = $this->obtenerCartaPorCodigo($codigo);
        if (!$c) {
            return self::F_GRUPOS;
        }
        return (string) $c['fase_actual'];
    }

    private function eliminarSeleccionesFase(int $cartaId, string $fase): void
    {
        $this->pdo->prepare('DELETE FROM quiniela_seleccion WHERE carta_id = ? AND fase = ?')->execute([$cartaId, $fase]);
    }

    /**
     * @return list<int>
     */
    public function obtenerIdsSeleccionFase(int $cartaId, string $fase): array
    {
        $st = $this->pdo->prepare(
            'SELECT equipo_id FROM quiniela_seleccion WHERE carta_id = ? AND fase = ? ORDER BY id ASC'
        );
        $st->execute([$cartaId, $fase]);
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Mapa grupo_id => [equipo_id, equipo_id] para fase grupos.
     *
     * @return array<int, array{0:int,1:int}>
     */
    public function seleccionGruposPorGrupo(int $cartaId): array
    {
        $st = $this->pdo->prepare(
            'SELECT grupo_id, equipo_id FROM quiniela_seleccion
             WHERE carta_id = ? AND fase = ? AND grupo_id IS NOT NULL
             ORDER BY grupo_id, id ASC'
        );
        $st->execute([$cartaId, self::F_GRUPOS]);
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $g = (int) $r['grupo_id'];
            if (!isset($out[$g])) {
                $out[$g] = [];
            }
            $out[$g][] = (int) $r['equipo_id'];
        }
        return $out;
    }

    /**
     * Equipos no clasificados en 1er y 2º lugar (pool para mejores terceros): 2 por grupo.
     *
     * @return list<int>
     */
    public function poolIdsMejoresTerceros(int $cartaId): array
    {
        $grupos = $this->listarGruposConEquipos();
        $selPorGrupo = $this->seleccionGruposPorGrupo($cartaId);
        $pool = [];
        foreach ($grupos as $g) {
            $gid = (int) $g['id'];
            $eids = array_column($g['equipos'], 'id');
            $pick = $selPorGrupo[$gid] ?? [];
            $pick = array_values(array_unique(array_map('intval', $pick)));
            foreach ($eids as $eid) {
                if (!in_array($eid, $pick, true)) {
                    $pool[] = (int) $eid;
                }
            }
        }
        return $pool;
    }

    /**
     * 24 clasificados directos + 8 mejores terceros = 32.
     *
     * @return list<int>|null null si faltan fases previas
     */
    public function poolIdsDieciseisavos(int $cartaId): ?array
    {
        $g1 = $this->obtenerIdsSeleccionFase($cartaId, self::F_GRUPOS);
        if (count($g1) < 24) {
            return null;
        }
        $mt = $this->obtenerIdsSeleccionFase($cartaId, self::F_MEJORES_TERCEROS);
        if (count($mt) !== 8) {
            return null;
        }
        return array_values(array_unique(array_merge($g1, $mt)));
    }

    /**
     * @param list<int> $idsPermitidos
     * @param list<int> $elegidos
     */
    private function validarSubconjunto(array $idsPermitidos, array $elegidos, int $cuenta, bool $exacto = true): bool
    {
        $perm = array_flip(array_map('intval', $idsPermitidos));
        $elegidos = array_map('intval', $elegidos);
        if ($exacto && count($elegidos) !== $cuenta) {
            return false;
        }
        if (count($elegidos) !== count(array_unique($elegidos))) {
            return false;
        }
        foreach ($elegidos as $id) {
            if (!isset($perm[$id])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array<int, array<int>> $porGrupo grupo_id => [eq1, eq2]
     */
    public function guardarSeleccionGrupos(string $codigo, array $porGrupo): bool
    {
        if ($this->quinielaEstaCerrada($codigo)) {
            return false;
        }
        $cartaId = $this->crearCartaSiNoExiste($codigo);
        $carta = $this->obtenerCartaPorCodigo($codigo);
        if (!$carta || (string) $carta['fase_actual'] !== self::F_GRUPOS) {
            return false;
        }

        $grupos = $this->listarGruposConEquipos();
        foreach ($grupos as $g) {
            $gid = (int) $g['id'];
            if (!isset($porGrupo[$gid])) {
                return false;
            }
            $pair = array_values(array_unique(array_map('intval', $porGrupo[$gid])));
            if (count($pair) !== 2) {
                return false;
            }
            $valid = array_column($g['equipos'], 'id');
            foreach ($pair as $eid) {
                if (!in_array($eid, $valid, true)) {
                    return false;
                }
            }
        }

        $this->pdo->beginTransaction();
        try {
            $this->eliminarSeleccionesFase($cartaId, self::F_GRUPOS);
            $ins = $this->pdo->prepare(
                'INSERT INTO quiniela_seleccion (carta_id, fase, grupo_id, equipo_id) VALUES (?,?,?,?)'
            );
            foreach ($grupos as $g) {
                $gid = (int) $g['id'];
                foreach ($porGrupo[$gid] as $eid) {
                    $ins->execute([$cartaId, self::F_GRUPOS, $gid, (int) $eid]);
                }
            }
            $sig = self::siguienteFase(self::F_GRUPOS);
            $this->pdo->prepare('UPDATE quiniela_carta SET fase_actual = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$sig, $cartaId]);
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::guardarSeleccionGrupos: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param list<int> $equipoIds
     */
    public function guardarSeleccionFaseLista(string $codigo, string $fase, array $equipoIds): bool
    {
        if ($this->quinielaEstaCerrada($codigo)) {
            return false;
        }
        $carta = $this->obtenerCartaPorCodigo($codigo);
        if (!$carta || (string) $carta['fase_actual'] !== $fase) {
            return false;
        }
        $cartaId = (int) $carta['id'];
        $n = self::cuentaEsperadaFase($fase);
        if ($n <= 0 || count($equipoIds) !== $n) {
            return false;
        }

        $equipoIds = array_map('intval', $equipoIds);
        if (count($equipoIds) !== count(array_unique($equipoIds))) {
            return false;
        }

        $pool = [];
        switch ($fase) {
            case self::F_MEJORES_TERCEROS:
                $pool = $this->poolIdsMejoresTerceros($cartaId);
                if (!$this->validarSubconjunto($pool, $equipoIds, 8)) {
                    return false;
                }
                break;
            case self::F_DIECISEISAVOS:
                $pool32 = $this->poolIdsDieciseisavos($cartaId);
                if ($pool32 === null || !$this->validarSubconjunto($pool32, $equipoIds, 16)) {
                    return false;
                }
                break;
            case self::F_OCTAVOS:
                $prev = $this->obtenerIdsSeleccionFase($cartaId, self::F_DIECISEISAVOS);
                if (!$this->validarSubconjunto($prev, $equipoIds, 8)) {
                    return false;
                }
                break;
            case self::F_CUARTOS:
                $prev = $this->obtenerIdsSeleccionFase($cartaId, self::F_OCTAVOS);
                if (!$this->validarSubconjunto($prev, $equipoIds, 4)) {
                    return false;
                }
                break;
            case self::F_SEMIFINAL:
                $prev = $this->obtenerIdsSeleccionFase($cartaId, self::F_CUARTOS);
                if (!$this->validarSubconjunto($prev, $equipoIds, 2)) {
                    return false;
                }
                break;
            case self::F_FINAL:
                $prev = $this->obtenerIdsSeleccionFase($cartaId, self::F_SEMIFINAL);
                if (!$this->validarSubconjunto($prev, $equipoIds, 1)) {
                    return false;
                }
                break;
            default:
                return false;
        }

        $this->pdo->beginTransaction();
        try {
            $this->eliminarSeleccionesFase($cartaId, $fase);
            $ins = $this->pdo->prepare(
                'INSERT INTO quiniela_seleccion (carta_id, fase, grupo_id, equipo_id) VALUES (?,?,?,?)'
            );
            foreach ($equipoIds as $eid) {
                $grupoId = null;
                if ($fase === self::F_MEJORES_TERCEROS) {
                    $grupoId = $this->grupoIdDeEquipo((int) $eid);
                }
                $ins->execute([$cartaId, $fase, $grupoId, (int) $eid]);
            }

            if ($fase === self::F_FINAL) {
                $this->pdo->prepare(
                    'UPDATE quiniela_carta SET cerrada = 1, fase_actual = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
                )->execute([self::F_FINAL, $cartaId]);
            } else {
                $sig = self::siguienteFase($fase);
                $this->pdo->prepare(
                    'UPDATE quiniela_carta SET fase_actual = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
                )->execute([$sig, $cartaId]);
            }
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::guardarSeleccionFaseLista: ' . $e->getMessage());
            return false;
        }
    }

    private function grupoIdDeEquipo(int $equipoId): ?int
    {
        $st = $this->pdo->prepare('SELECT grupo_id FROM quiniela_equipo WHERE id = ?');
        $st->execute([$equipoId]);
        $v = $st->fetchColumn();
        return $v !== false && $v !== null ? (int) $v : null;
    }

    /**
     * Equipos disponibles para la fase actual (ids).
     *
     * @return list<int>|null
     */
    public function obtenerEquiposDisponiblesPorFase(string $codigo, string $fase): ?array
    {
        $carta = $this->obtenerCartaPorCodigo($codigo);
        if (!$carta) {
            return [];
        }
        $cartaId = (int) $carta['id'];
        switch ($fase) {
            case self::F_GRUPOS:
                return [];
            case self::F_MEJORES_TERCEROS:
                return $this->poolIdsMejoresTerceros($cartaId);
            case self::F_DIECISEISAVOS:
                return $this->poolIdsDieciseisavos($cartaId);
            case self::F_OCTAVOS:
                return $this->obtenerIdsSeleccionFase($cartaId, self::F_DIECISEISAVOS);
            case self::F_CUARTOS:
                return $this->obtenerIdsSeleccionFase($cartaId, self::F_OCTAVOS);
            case self::F_SEMIFINAL:
                return $this->obtenerIdsSeleccionFase($cartaId, self::F_CUARTOS);
            case self::F_FINAL:
                return $this->obtenerIdsSeleccionFase($cartaId, self::F_SEMIFINAL);
            default:
                return [];
        }
    }

    // ——— Oficial (admin) ———

    /**
     * Reemplaza todas las filas oficiales de una fase.
     *
     * @param list<array{equipo_id:int, grupo_id?:?int}> $filas
     */
    public function guardarOficialFase(string $fase, array $filas): bool
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM quiniela_oficial WHERE fase = ?')->execute([$fase]);
            $ins = $this->pdo->prepare(
                'INSERT INTO quiniela_oficial (fase, grupo_id, equipo_id) VALUES (?,?,?)'
            );
            foreach ($filas as $row) {
                $gid = null;
                if (array_key_exists('grupo_id', $row) && $row['grupo_id'] !== null && $row['grupo_id'] !== '') {
                    $gid = (int) $row['grupo_id'];
                }
                $ins->execute([$fase, $gid, (int) $row['equipo_id']]);
            }
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('Quiniela::guardarOficialFase: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @return list<array{fase:string, grupo_id:?int, equipo_id:int}>
     */
    public function listarOficialPorFase(string $fase): array
    {
        $st = $this->pdo->prepare('SELECT fase, grupo_id, equipo_id FROM quiniela_oficial WHERE fase = ? ORDER BY id ASC');
        $st->execute([$fase]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIdsOficialFase(string $fase): array
    {
        $st = $this->pdo->prepare('SELECT equipo_id FROM quiniela_oficial WHERE fase = ? ORDER BY id ASC');
        $st->execute([$fase]);
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    public function oficialFaseTieneDatos(string $fase): bool
    {
        $st = $this->pdo->prepare('SELECT COUNT(*) FROM quiniela_oficial WHERE fase = ?');
        $st->execute([$fase]);
        return (int) $st->fetchColumn() > 0;
    }

    // ——— Comparación y estados ———

    /**
     * Comparación por conjuntos (orden irrelevante), excepto final (1 equipo).
     */
    public function faseCoincideConOficial(int $cartaId, string $fase): ?bool
    {
        if (!$this->oficialFaseTieneDatos($fase)) {
            return null;
        }
        $usr = $this->obtenerIdsSeleccionFase($cartaId, $fase);
        $of = $this->obtenerIdsOficialFase($fase);
        if ($fase === self::F_GRUPOS) {
            $mapU = $this->seleccionGruposPorGrupo($cartaId);
            $st = $this->pdo->prepare(
                'SELECT grupo_id, equipo_id FROM quiniela_oficial WHERE fase = ? AND grupo_id IS NOT NULL ORDER BY grupo_id, id'
            );
            $st->execute([$fase]);
            $ofPorG = [];
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $g = (int) $r['grupo_id'];
                if (!isset($ofPorG[$g])) {
                    $ofPorG[$g] = [];
                }
                $ofPorG[$g][] = (int) $r['equipo_id'];
            }
            $grupos = $this->listarGruposConEquipos();
            foreach ($grupos as $g) {
                $gid = (int) $g['id'];
                $a = isset($mapU[$gid]) ? $mapU[$gid] : [];
                $b = isset($ofPorG[$gid]) ? $ofPorG[$gid] : [];
                sort($a);
                sort($b);
                if ($a !== $b) {
                    return false;
                }
            }
            return true;
        }
        sort($usr);
        sort($of);
        return $usr === $of;
    }

    /** Pendiente | En juego | Perdió | Completada */
    public function estadoColaboradorQuiniela(string $codigo): string
    {
        if (!$this->quinielaEstaCerrada($codigo)) {
            return 'Pendiente';
        }
        $carta = $this->obtenerCartaPorCodigo($codigo);
        if (!$carta) {
            return 'Pendiente';
        }
        $cartaId = (int) $carta['id'];
        foreach (self::ordenFases() as $f) {
            if (!$this->oficialFaseTieneDatos($f)) {
                return 'En juego';
            }
            if ($this->faseCoincideConOficial($cartaId, $f) !== true) {
                return 'Perdió';
            }
        }
        return 'Completada';
    }

    /** @return list<array{codigo_empleado: string, nombre: string, status: string}> */
    public function listarResumenColaboradoresQuiniela(PDO $pdoEmpleados): array
    {
        $codes = $this->pdo->query(
            'SELECT codigo_empleado FROM quiniela_carta ORDER BY codigo_empleado ASC'
        )->fetchAll(PDO::FETCH_COLUMN);
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

    /** Resumen para modal / lectura */
    public function obtenerResumenQuiniela(string $codigo): array
    {
        $carta = $this->obtenerCartaPorCodigo($codigo);
        if (!$carta) {
            return ['fases' => [], 'cerrada' => false];
        }
        $cid = (int) $carta['id'];
        $out = [];
        foreach (self::ordenFases() as $f) {
            if ($f === self::F_GRUPOS) {
                $map = $this->seleccionGruposPorGrupo($cid);
                $gruposList = $this->listarGruposConEquipos();
                $bloques = [];
                foreach ($gruposList as $g) {
                    $gid = (int) $g['id'];
                    $ids = $map[$gid] ?? [];
                    $items = [];
                    foreach ($ids as $eid) {
                        $d = $this->datosEquipo((int) $eid);
                        $items[] = [
                            'equipo_id' => (int) $eid,
                            'nombre' => $d['nombre'] ?? '',
                            'html' => $d ? quiniela_flag_icon_html($d['iso'], $d['nombre'], true) : '',
                        ];
                    }
                    $bloques[] = [
                        'grupo_id' => $gid,
                        'nombre_grupo' => $g['nombre_grupo'],
                        'orden_grupo' => (int) $g['orden_grupo'],
                        'equipos' => $items,
                    ];
                }
                $out[] = [
                    'fase' => $f,
                    'etiqueta' => self::etiquetaFase($f),
                    'grupos' => $bloques,
                    'equipos' => [],
                ];
                continue;
            }
            $ids = $this->obtenerIdsSeleccionFase($cid, $f);
            $items = [];
            foreach ($ids as $eid) {
                $d = $this->datosEquipo((int) $eid);
                $items[] = [
                    'equipo_id' => (int) $eid,
                    'nombre' => $d['nombre'] ?? '',
                    'html' => $d ? quiniela_flag_icon_html($d['iso'], $d['nombre'], true) : '',
                ];
            }
            $out[] = [
                'fase' => $f,
                'etiqueta' => self::etiquetaFase($f),
                'equipos' => $items,
            ];
        }
        return [
            'fases' => $out,
            'cerrada' => (int) $carta['cerrada'] === 1,
            'fase_actual' => (string) $carta['fase_actual'],
        ];
    }

    /**
     * Resultados oficiales para vista pública (misma forma que resumen colaborador).
     *
     * @return array{fases: list<array>}
     */
    public function obtenerResumenOficial(): array
    {
        $out = [];
        foreach (self::ordenFases() as $f) {
            if ($f === self::F_GRUPOS) {
                $st = $this->pdo->prepare(
                    'SELECT grupo_id, equipo_id FROM quiniela_oficial WHERE fase = ? AND grupo_id IS NOT NULL ORDER BY grupo_id, id ASC'
                );
                $st->execute([$f]);
                $porG = [];
                foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                    $gid = (int) $r['grupo_id'];
                    if (!isset($porG[$gid])) {
                        $porG[$gid] = [];
                    }
                    $porG[$gid][] = (int) $r['equipo_id'];
                }
                $gruposList = $this->listarGruposConEquipos();
                $bloques = [];
                foreach ($gruposList as $g) {
                    $gid = (int) $g['id'];
                    $ids = $porG[$gid] ?? [];
                    $items = [];
                    foreach ($ids as $eid) {
                        $d = $this->datosEquipo((int) $eid);
                        $items[] = [
                            'equipo_id' => (int) $eid,
                            'nombre' => $d['nombre'] ?? '',
                            'html' => $d ? quiniela_flag_icon_html($d['iso'], $d['nombre'], true) : '',
                        ];
                    }
                    $bloques[] = [
                        'grupo_id' => $gid,
                        'nombre_grupo' => $g['nombre_grupo'],
                        'orden_grupo' => (int) $g['orden_grupo'],
                        'equipos' => $items,
                    ];
                }
                $nOk = 0;
                foreach ($gruposList as $g) {
                    $gid = (int) $g['id'];
                    if (isset($porG[$gid]) && count($porG[$gid]) === 2) {
                        $nOk++;
                    }
                }
                $def = count($gruposList) === 12 && $nOk === 12;
                $out[] = [
                    'fase' => $f,
                    'etiqueta' => self::etiquetaFase($f),
                    'grupos' => $bloques,
                    'equipos' => [],
                    'definida' => $def,
                ];
                continue;
            }
            $ids = $this->obtenerIdsOficialFase($f);
            $nEsp = self::cuentaEsperadaFase($f);
            $def = $nEsp > 0 && count($ids) === $nEsp;
            $items = [];
            foreach ($ids as $eid) {
                $d = $this->datosEquipo((int) $eid);
                $items[] = [
                    'equipo_id' => (int) $eid,
                    'nombre' => $d['nombre'] ?? '',
                    'html' => $d ? quiniela_flag_icon_html($d['iso'], $d['nombre'], true) : '',
                ];
            }
            $out[] = [
                'fase' => $f,
                'etiqueta' => self::etiquetaFase($f),
                'equipos' => $items,
                'definida' => $def,
            ];
        }
        return ['fases' => $out];
    }

    /**
     * Pool de terceros para admin (equipos no en 1.º y 2.º oficial por grupo).
     *
     * @return list<int>
     */
    /** @return array<int, list<int>> */
    public function oficialGruposPorGrupo(): array
    {
        $st = $this->pdo->prepare(
            'SELECT grupo_id, equipo_id FROM quiniela_oficial WHERE fase = ? AND grupo_id IS NOT NULL ORDER BY grupo_id, id ASC'
        );
        $st->execute([self::F_GRUPOS]);
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $g = (int) $r['grupo_id'];
            if (!isset($out[$g])) {
                $out[$g] = [];
            }
            $out[$g][] = (int) $r['equipo_id'];
        }
        return $out;
    }

    public function poolTercerosDesdeOficialGrupos(): array
    {
        $st = $this->pdo->prepare(
            'SELECT grupo_id, equipo_id FROM quiniela_oficial WHERE fase = ? AND grupo_id IS NOT NULL ORDER BY grupo_id, id ASC'
        );
        $st->execute([self::F_GRUPOS]);
        $porG = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $gid = (int) $r['grupo_id'];
            if (!isset($porG[$gid])) {
                $porG[$gid] = [];
            }
            $porG[$gid][] = (int) $r['equipo_id'];
        }
        $grupos = $this->listarGruposConEquipos();
        $pool = [];
        foreach ($grupos as $g) {
            $gid = (int) $g['id'];
            $top2 = $porG[$gid] ?? [];
            if (count($top2) !== 2) {
                return [];
            }
            foreach ($g['equipos'] as $eq) {
                $eid = (int) $eq['id'];
                if (!in_array($eid, $top2, true)) {
                    $pool[] = $eid;
                }
            }
        }
        return $pool;
    }

    /**
     * @return list<int>|null 32 equipos o null si faltan grupos o mejores terceros oficiales
     */
    public function poolDieciseisavosOficial(): ?array
    {
        $g = $this->obtenerIdsOficialFase(self::F_GRUPOS);
        if (count($g) < 24) {
            return null;
        }
        $mt = $this->obtenerIdsOficialFase(self::F_MEJORES_TERCEROS);
        if (count($mt) !== 8) {
            return null;
        }
        return array_values(array_unique(array_merge($g, $mt)));
    }

    public function detalleJsonColaborador(string $codigo): array
    {
        return array_merge($this->obtenerResumenQuiniela($codigo), ['codigo' => $codigo]);
    }

    /**
     * @return array<int, array{id:int,nombre:string,iso:?string,flag_url:string}>|null
     */
    public function datosEquipo(int $id): ?array
    {
        if (isset($this->equipoCache[$id])) {
            return $this->equipoCache[$id];
        }
        $st = $this->pdo->prepare('SELECT id, nombre, iso FROM quiniela_equipo WHERE id = ?');
        $st->execute([$id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (!$r) {
            return null;
        }
        $iso = $r['iso'] ?? null;
        if ($iso === null || $iso === '') {
            $iso = null;
        } else {
            $iso = strtolower(substr((string) $iso, 0, 2));
            if (strlen($iso) !== 2) {
                $iso = null;
            }
        }
        $out = [
            'id' => (int) $r['id'],
            'nombre' => (string) $r['nombre'],
            'iso' => $iso,
            'flag_url' => quiniela_get_flag_url($iso),
        ];
        $this->equipoCache[$id] = $out;
        return $out;
    }

    public function nombreEquipo(int $id): string
    {
        $d = $this->datosEquipo($id);
        return $d ? $d['nombre'] : (string) $id;
    }

    public function nombreEquipoHtml(int $id): string
    {
        $d = $this->datosEquipo($id);
        if (!$d) {
            return htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8');
        }
        return quiniela_flag_icon_html($d['iso'], $d['nombre'], true);
    }

    public function listarTodosEquiposParaSelector(): array
    {
        if ($this->cacheListarTodosEquipos !== null) {
            return $this->cacheListarTodosEquipos;
        }
        $sql = 'SELECT e.id, e.nombre, e.iso, g.nombre AS grupo_nom, g.orden_grupo
                FROM quiniela_equipo e
                INNER JOIN quiniela_grupo g ON g.id = e.grupo_id
                ORDER BY g.orden_grupo ASC, e.slot ASC';
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $iso = $r['iso'] ?? null;
            $iso = $iso !== null && $iso !== '' ? strtolower(substr((string) $iso, 0, 2)) : null;
            if ($iso !== null && strlen($iso) !== 2) {
                $iso = null;
            }
            $r['iso'] = $iso;
            $r['flag_url'] = quiniela_get_flag_url($iso);
        }
        unset($r);
        $this->cacheListarTodosEquipos = $rows;
        return $rows;
    }

    /** @return array<string, string> */
    public function mapaIsoPorEquipoId(): array
    {
        $out = [];
        foreach ($this->listarTodosEquiposParaSelector() as $eq) {
            $id = (string) (int) $eq['id'];
            $iso = $eq['iso'] ?? null;
            if ($iso === null || $iso === '') {
                $iso = quiniela_paises_iso_por_nombre((string) $eq['nombre']);
            }
            $out[$id] = $iso !== null ? strtolower((string) $iso) : '';
        }
        return $out;
    }

    /** @return array<int, array{iso: string, nombre: string}> */
    public function mapaEquipoMetaPorId(): array
    {
        $out = [];
        foreach ($this->listarTodosEquiposParaSelector() as $eq) {
            $i = (int) $eq['id'];
            $iso = $eq['iso'] ?? '';
            $iso = $iso !== null && $iso !== '' ? strtolower(substr((string) $iso, 0, 2)) : '';
            if ($iso === '' || strlen($iso) !== 2) {
                $iso = (string) (quiniela_paises_iso_por_nombre((string) $eq['nombre']) ?? '');
            }
            $out[$i] = ['iso' => $iso, 'nombre' => (string) $eq['nombre']];
        }
        return $out;
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
