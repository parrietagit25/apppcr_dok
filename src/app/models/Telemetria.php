<?php

class Telemetria
{
    private PDO $pdo;

    /** @var bool|null */
    private static $tablaOk = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function tablaDisponible(PDO $pdo): bool
    {
        if (self::$tablaOk !== null) {
            return self::$tablaOk;
        }
        try {
            $pdo->query('SELECT 1 FROM telemetria_eventos LIMIT 1');
            self::$tablaOk = true;
        } catch (Throwable $e) {
            self::$tablaOk = false;
        }
        return self::$tablaOk;
    }

    /**
     * @param array<string, mixed> $extra
     */
    public static function registrar(PDO $pdo, string $evento, array $extra = []): void
    {
        if (!self::tablaDisponible($pdo)) {
            return;
        }

        try {
            $codigo = $extra['codigo_empleado'] ?? ($_SESSION['code'] ?? null);
            $typeUser = $extra['type_user'] ?? null;
            if ($typeUser === null && $codigo !== null && isset($_SESSION['code'])) {
                $typeUser = $_SESSION['type_user'] ?? null;
            }

            $meta = $extra['metadata'] ?? null;
            if (is_array($meta)) {
                $meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
            }

            $st = $pdo->prepare(
                'INSERT INTO telemetria_eventos
                (codigo_empleado, type_user, evento, modulo, ruta, accion, metadata, ip, user_agent)
                VALUES (:codigo, :type_user, :evento, :modulo, :ruta, :accion, :metadata, :ip, :user_agent)'
            );
            $st->execute([
                ':codigo' => $codigo !== null && $codigo !== '' ? (string) $codigo : null,
                ':type_user' => $typeUser !== null ? (int) $typeUser : null,
                ':evento' => substr($evento, 0, 50),
                ':modulo' => isset($extra['modulo']) ? substr((string) $extra['modulo'], 0, 100) : null,
                ':ruta' => isset($extra['ruta']) ? substr((string) $extra['ruta'], 0, 500) : null,
                ':accion' => isset($extra['accion']) ? substr((string) $extra['accion'], 0, 150) : null,
                ':metadata' => $meta,
                ':ip' => self::clientIp(),
                ':user_agent' => self::userAgent(),
            ]);
        } catch (Throwable $e) {
            error_log('Telemetria::registrar: ' . $e->getMessage());
        }
    }

    public static function trackPaginaActual(PDO $pdo): void
    {
        if (!isset($_SESSION['code']) || PHP_SAPI === 'cli') {
            return;
        }

        $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
        if ($script === 'TelemetriaController.php') {
            return;
        }

        $ruta = self::rutaActual();
        $dedupeKey = md5($ruta . '|' . ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $now = time();
        if (
            isset($_SESSION['_tel_key'], $_SESSION['_tel_ts'])
            && $_SESSION['_tel_key'] === $dedupeKey
            && ($now - (int) $_SESSION['_tel_ts']) < 3
        ) {
            return;
        }
        $_SESSION['_tel_key'] = $dedupeKey;
        $_SESSION['_tel_ts'] = $now;

        $modulo = self::resolverModulo($script, $_GET ?? []);
        $evento = (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') ? 'accion' : 'page_view';
        $accion = null;
        if ($evento === 'accion') {
            $accion = self::detectarAccionPost($_POST ?? []);
        }

        self::registrar($pdo, $evento, [
            'modulo' => $modulo,
            'ruta' => $ruta,
            'accion' => $accion,
        ]);
    }

    private static function clientIp(): ?string
    {
        $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', (string) $_SERVER[$key])[0]);
                return substr($ip, 0, 45);
            }
        }
        return null;
    }

    private static function userAgent(): ?string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return $ua !== '' ? substr($ua, 0, 512) : null;
    }

    private static function rutaActual(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if ($uri === '') {
            return basename($_SERVER['SCRIPT_NAME'] ?? '');
        }
        return substr($uri, 0, 500);
    }

    /**
     * @param array<string, mixed> $get
     */
    private static function resolverModulo(string $script, array $get): string
    {
        $mapMain = [
            'cumple' => 'Cumpleaños',
            'mantenimineto' => 'Mantenimiento',
            'mantenimiento_usuarios' => 'Mant. Usuarios',
            'mantenimiento_encargados' => 'Mant. Encargados',
            'mantenimiento_r_encargados' => 'Mant. R-Encargados',
            'mantenimiento_usuarios_no_listados' => 'Mant. No listados',
            'mantenimiento_permisos' => 'Mant. Permisos',
            'mantenimiento_vacaciones' => 'Mant. Vacaciones',
            'mantenimiento_cumple' => 'Mant. Cumple',
            'manual_colaborador' => 'Manual Colaborador',
            'manual_supervisor' => 'Manual Supervisor',
            'manual_mantenimiento' => 'Manual Mantenimiento',
            'n_poliza' => 'Mi Póliza',
            'telemetria' => 'Telemetría',
        ];
        foreach ($mapMain as $key => $label) {
            if (isset($get[$key])) {
                return $label;
            }
        }

        if ($script === 'MainController.php' && $get === []) {
            return 'Inicio';
        }

        $mapRrhh = [
            'mis_datos' => 'Mis Datos',
            'carta_trabajo' => 'Carta Trabajo',
            'calamidad' => 'Calamidad',
            'uniforme' => 'Uniforme',
            'mis_vacaciones' => 'Mis Vacaciones',
            'solicitud_r_permiso' => 'Solicitar Permiso',
            'solicitud_permiso' => 'Solicitar Permiso',
            'incapacidad' => 'Incapacidad',
            'administrar_permiso_admin' => 'Administrar Permisos',
            'mi_personal' => 'Mi Personal',
            'solicitud_permiso_admin' => 'V-Permisos',
            'carta_trabajo_aprobar' => 'V-Carta Trabajo',
            'incapacidad_vrrhh' => 'V-Incapacidades',
            'uniforme_vrrhh' => 'V-Uniformes',
            'calamidad_vrrhh' => 'V-Calamidades',
        ];
        foreach ($mapRrhh as $key => $label) {
            if (isset($get[$key])) {
                return $label;
            }
        }

        if ($script === 'RRHHController.php' && $get === []) {
            return 'Mi Espacio';
        }

        $otros = [
            'BeneficiosController.php' => 'Beneficios',
            'CarnetController.php' => 'Mi Carnet',
            'QuinielaController.php' => 'Quiniela',
            'MetricasController.php' => 'Métricas RRHH',
            'RegcolaController.php' => 'Registro',
            'AuthController.php' => 'Login',
        ];
        return $otros[$script] ?? pathinfo($script, PATHINFO_FILENAME);
    }

    /**
     * @param array<string, mixed> $post
     */
    private static function detectarAccionPost(array $post): ?string
    {
        $prioridad = [
            'registro_colaborador' => 'Registro colaborador',
            'boton_frase_semana' => 'Actualizar frase semana',
            'registrar_usuario' => 'Registrar usuario no listado',
            'asignar_encargado' => 'Asignar supervisor',
            'actualizar_estatus_empleado' => 'Actualizar estatus empleado',
            'accion_cumple' => 'Mant. cumpleaños',
            'administrar_permiso_admin' => 'Gestionar permiso',
        ];
        foreach ($prioridad as $key => $label) {
            if (isset($post[$key])) {
                return $label;
            }
        }
        foreach ($post as $key => $_) {
            if (is_string($key) && $key !== '') {
                return substr($key, 0, 150);
            }
        }
        return 'POST';
    }

    private function rangoSql(string $desde, string $hasta): array
    {
        return [
            'desde' => $desde . ' 00:00:00',
            'hasta' => $hasta . ' 23:59:59',
        ];
    }

    /** @return array<string, int|float> */
    public function getKpis(string $desde, string $hasta): array
    {
        if (!self::tablaDisponible($this->pdo)) {
            return [
                'total_eventos' => 0,
                'logins' => 0,
                'page_views' => 0,
                'acciones' => 0,
                'usuarios_unicos' => 0,
                'modulos_activos' => 0,
            ];
        }
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT
                COUNT(*) AS total_eventos,
                SUM(evento = 'login') AS logins,
                SUM(evento = 'page_view') AS page_views,
                SUM(evento IN ('accion', 'registro')) AS acciones,
                COUNT(DISTINCT codigo_empleado) AS usuarios_unicos,
                COUNT(DISTINCT modulo) AS modulos_activos
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta"
        );
        $st->execute($r);
        $row = $st->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'total_eventos' => (int) ($row['total_eventos'] ?? 0),
            'logins' => (int) ($row['logins'] ?? 0),
            'page_views' => (int) ($row['page_views'] ?? 0),
            'acciones' => (int) ($row['acciones'] ?? 0),
            'usuarios_unicos' => (int) ($row['usuarios_unicos'] ?? 0),
            'modulos_activos' => (int) ($row['modulos_activos'] ?? 0),
        ];
    }

    /** @return list<array{fecha:string,logins:int,vistas:int,acciones:int,total:int}> */
    public function getActividadDiaria(string $desde, string $hasta): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT DATE(created_at) AS fecha,
                    SUM(evento = 'login') AS logins,
                    SUM(evento = 'page_view') AS vistas,
                    SUM(evento IN ('accion','registro')) AS acciones,
                    COUNT(*) AS total
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
             GROUP BY DATE(created_at)
             ORDER BY fecha ASC"
        );
        $st->execute($r);
        return array_map(function ($row) {
            return [
                'fecha' => (string) $row['fecha'],
                'logins' => (int) $row['logins'],
                'vistas' => (int) $row['vistas'],
                'acciones' => (int) $row['acciones'],
                'total' => (int) $row['total'],
            ];
        }, $st->fetchAll(PDO::FETCH_ASSOC));
    }

    /** @return list<array{modulo:string,total:int}> */
    public function getTopModulos(string $desde, string $hasta, int $limit = 12): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT COALESCE(NULLIF(modulo,''), 'Sin módulo') AS modulo, COUNT(*) AS total
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
             GROUP BY modulo
             ORDER BY total DESC
             LIMIT " . (int) $limit
        );
        $st->execute($r);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array{codigo_empleado:string,nombre:string,total:int,logins:int}> */
    public function getTopUsuarios(string $desde, string $hasta, int $limit = 10): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT t.codigo_empleado,
                    COUNT(*) AS total,
                    SUM(t.evento = 'login') AS logins
             FROM telemetria_eventos t
             WHERE t.created_at BETWEEN :desde AND :hasta
               AND t.codigo_empleado IS NOT NULL
             GROUP BY t.codigo_empleado
             ORDER BY total DESC
             LIMIT " . (int) $limit
        );
        $st->execute($r);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['total'] = (int) $row['total'];
            $row['logins'] = (int) $row['logins'];
            $row['nombre'] = $this->nombreColaborador((string) $row['codigo_empleado']);
        }
        unset($row);
        return $rows;
    }

    /** @return list<array{evento:string,total:int}> */
    public function getEventosPorTipo(string $desde, string $hasta): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT evento, COUNT(*) AS total
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
             GROUP BY evento
             ORDER BY total DESC"
        );
        $st->execute($r);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array{hora:int,total:int}> */
    public function getActividadPorHora(string $desde, string $hasta): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT HOUR(created_at) AS hora, COUNT(*) AS total
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
             GROUP BY HOUR(created_at)
             ORDER BY hora ASC"
        );
        $st->execute($r);
        $map = array_fill(0, 24, 0);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map[(int) $row['hora']] = (int) $row['total'];
        }
        $out = [];
        for ($h = 0; $h < 24; $h++) {
            $out[] = ['hora' => $h, 'total' => $map[$h]];
        }
        return $out;
    }

    /** @return list<array{fecha:string,usuarios:int}> */
    public function getUsuariosActivosPorDia(string $desde, string $hasta): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT DATE(created_at) AS fecha,
                    COUNT(DISTINCT codigo_empleado) AS usuarios
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
               AND codigo_empleado IS NOT NULL
             GROUP BY DATE(created_at)
             ORDER BY fecha ASC"
        );
        $st->execute($r);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function getEventosRecientes(string $desde, string $hasta, int $limit = 40): array
    {
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT id, codigo_empleado, evento, modulo, accion, ruta, created_at
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
             ORDER BY created_at DESC
             LIMIT " . (int) $limit
        );
        $st->execute($r);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['nombre'] = $row['codigo_empleado']
                ? $this->nombreColaborador((string) $row['codigo_empleado'])
                : '—';
        }
        unset($row);
        return $rows;
    }

    private function nombreColaborador(string $code): string
    {
        static $cache = [];
        if (isset($cache[$code])) {
            return $cache[$code];
        }
        $st = $this->pdo->prepare(
            'SELECT nombre, apellido FROM empleados WHERE codigo_empleado = ? LIMIT 1'
        );
        $st->execute([$code]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $cache[$code] = trim($row['nombre'] . ' ' . ($row['apellido'] ?? ''));
            return $cache[$code];
        }
        $cache[$code] = $code;
        return $code;
    }
}
