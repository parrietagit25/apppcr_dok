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

    /** @var bool|null */
    private static $columnasDispositivoOk = null;

    public static function columnasDispositivoDisponible(PDO $pdo): bool
    {
        if (self::$columnasDispositivoOk !== null) {
            return self::$columnasDispositivoOk;
        }
        if (!self::tablaDisponible($pdo)) {
            self::$columnasDispositivoOk = false;
            return false;
        }
        try {
            $st = $pdo->query("SHOW COLUMNS FROM telemetria_eventos LIKE 'dispositivo_tipo'");
            self::$columnasDispositivoOk = (bool) $st->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            self::$columnasDispositivoOk = false;
        }
        return self::$columnasDispositivoOk;
    }

    /**
     * Contexto del navegador enviado por JS (sesión + enriquecimiento).
     *
     * @param array<string, mixed> $data
     */
    public static function guardarContextoCliente(PDO $pdo, array $data): void
    {
        $ctx = self::normalizarContextoCliente($data);
        $uaRaw = !empty($data['user_agent_cliente']) ? (string) $data['user_agent_cliente'] : (self::userAgent() ?? '');
        $ua = self::parseUserAgent($uaRaw);
        foreach ($ua as $k => $v) {
            if (empty($ctx[$k]) && $v !== null && $v !== '') {
                $ctx[$k] = $v;
            }
        }
        // Ubicación solo por IP en servidor (sin permisos del navegador).
        $ip = self::clientIp();
        if ($ip) {
            $geo = self::resolverGeoIp($ip);
            if ($geo) {
                $ctx['ubicacion_texto'] = $geo['ubicacion_texto'] ?? null;
                $ctx['isp'] = $geo['isp'] ?? null;
                $ctx['latitud'] = $geo['latitud'] ?? null;
                $ctx['longitud'] = $geo['longitud'] ?? null;
            }
        }
        $_SESSION['telemetria_contexto'] = $ctx;
        if (self::tablaDisponible($pdo)) {
            self::registrar($pdo, 'device_info', [
                'modulo' => 'Dispositivo',
                'accion' => 'Contexto cliente',
                'metadata' => $data,
            ]);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function normalizarContextoCliente(array $data): array
    {
        return [
            'dispositivo_tipo' => substr((string) ($data['dispositivo_tipo'] ?? ''), 0, 24),
            'navegador' => substr((string) ($data['navegador'] ?? ''), 0, 80),
            'sistema_operativo' => substr((string) ($data['sistema_operativo'] ?? ''), 0, 80),
            'resolucion_pantalla' => substr((string) ($data['resolucion_pantalla'] ?? ''), 0, 24),
            'resolucion_viewport' => substr((string) ($data['resolucion_viewport'] ?? ''), 0, 24),
            'pixel_ratio' => isset($data['pixel_ratio']) ? (float) $data['pixel_ratio'] : null,
            'timezone' => substr((string) ($data['timezone'] ?? ''), 0, 64),
            'idioma' => substr((string) ($data['idioma'] ?? ''), 0, 16),
            'latitud' => null,
            'longitud' => null,
            'ubicacion_texto' => null,
            'tipo_conexion' => substr((string) ($data['tipo_conexion'] ?? ''), 0, 32),
            'plataforma' => substr((string) ($data['plataforma'] ?? ''), 0, 80),
            'isp' => substr((string) ($data['isp'] ?? ''), 0, 120),
            'referrer' => substr((string) ($data['referrer'] ?? ''), 0, 500),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private static function parseUserAgent(string $ua): array
    {
        if ($ua === '') {
            return ['navegador' => null, 'sistema_operativo' => null, 'dispositivo_tipo' => null];
        }
        $os = 'Desconocido';
        if (preg_match('/Windows NT 10/i', $ua)) {
            $os = 'Windows 10+';
        } elseif (preg_match('/Windows/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/Android ([0-9\.]+)/i', $ua, $m)) {
            $os = 'Android ' . $m[1];
        } elseif (preg_match('/iPhone OS ([0-9_]+)/i', $ua, $m)) {
            $os = 'iOS ' . str_replace('_', '.', $m[1]);
        } elseif (preg_match('/iPad/i', $ua)) {
            $os = 'iPadOS';
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux';
        }

        $browser = 'Desconocido';
        if (preg_match('/Edg\/([0-9\.]+)/i', $ua, $m)) {
            $browser = 'Edge ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Chrome\/([0-9\.]+)/i', $ua, $m) && stripos($ua, 'Edg') === false) {
            $browser = 'Chrome ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $ua, $m)) {
            $browser = 'Firefox ' . explode('.', $m[1])[0];
        } elseif (preg_match('/Version\/([0-9\.]+).*Safari/i', $ua, $m)) {
            $browser = 'Safari ' . explode('.', $m[1])[0];
        }

        $tipo = 'desktop';
        if (preg_match('/iPad|Tablet|PlayBook/i', $ua)) {
            $tipo = 'tablet';
        } elseif (preg_match('/Mobi|Android|iPhone|iPod/i', $ua)) {
            $tipo = 'mobile';
        }

        return [
            'navegador' => $browser,
            'sistema_operativo' => $os,
            'dispositivo_tipo' => $tipo,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function resolverGeoIp(string $ip): ?array
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }
        $url = 'http://ip-api.com/json/' . urlencode($ip) . '?fields=status,country,regionName,city,lat,lon,isp,mobile';
        $ctx = stream_context_create(['http' => ['timeout' => 2, 'ignore_errors' => true]]);
        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return null;
        }
        $j = json_decode($raw, true);
        if (!is_array($j) || ($j['status'] ?? '') !== 'success') {
            return null;
        }
        $partes = array_filter([$j['city'] ?? '', $j['regionName'] ?? '', $j['country'] ?? '']);
        return [
            'ubicacion_texto' => implode(', ', $partes),
            'latitud' => isset($j['lat']) ? (float) $j['lat'] : null,
            'longitud' => isset($j['lon']) ? (float) $j['lon'] : null,
            'isp' => $j['isp'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function contextoDispositivoParaInserto(PDO $pdo): array
    {
        $ctx = $_SESSION['telemetria_contexto'] ?? [];
        if (!is_array($ctx)) {
            $ctx = [];
        }
        if (empty($ctx['navegador']) || empty($ctx['sistema_operativo'])) {
            $ua = self::parseUserAgent(self::userAgent() ?? '');
            $ctx = array_merge($ua, $ctx);
        }
        if (empty($ctx['ubicacion_texto']) && self::columnasDispositivoDisponible($pdo)) {
            $ip = self::clientIp();
            if ($ip) {
                $geo = self::resolverGeoIp($ip);
                if ($geo) {
                    foreach ($geo as $k => $v) {
                        if ($v !== null && $v !== '') {
                            $ctx[$k] = $v;
                        }
                    }
                }
            }
        }
        return $ctx;
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

            $dev = self::contextoDispositivoParaInserto($pdo);
            $ip = self::clientIp();

            if (self::columnasDispositivoDisponible($pdo)) {
                $st = $pdo->prepare(
                    'INSERT INTO telemetria_eventos
                    (codigo_empleado, type_user, evento, modulo, ruta, accion, metadata, ip, user_agent,
                     dispositivo_tipo, navegador, sistema_operativo, resolucion_pantalla, resolucion_viewport,
                     pixel_ratio, timezone, idioma, latitud, longitud, ubicacion_texto, tipo_conexion,
                     plataforma, isp, referrer)
                    VALUES
                    (:codigo, :type_user, :evento, :modulo, :ruta, :accion, :metadata, :ip, :user_agent,
                     :dispositivo_tipo, :navegador, :sistema_operativo, :resolucion_pantalla, :resolucion_viewport,
                     :pixel_ratio, :timezone, :idioma, :latitud, :longitud, :ubicacion_texto, :tipo_conexion,
                     :plataforma, :isp, :referrer)'
                );
                $st->execute([
                    ':codigo' => $codigo !== null && $codigo !== '' ? (string) $codigo : null,
                    ':type_user' => $typeUser !== null ? (int) $typeUser : null,
                    ':evento' => substr($evento, 0, 50),
                    ':modulo' => isset($extra['modulo']) ? substr((string) $extra['modulo'], 0, 100) : null,
                    ':ruta' => isset($extra['ruta']) ? substr((string) $extra['ruta'], 0, 500) : null,
                    ':accion' => isset($extra['accion']) ? substr((string) $extra['accion'], 0, 150) : null,
                    ':metadata' => $meta,
                    ':ip' => $ip,
                    ':user_agent' => self::userAgent(),
                    ':dispositivo_tipo' => $dev['dispositivo_tipo'] ?? null,
                    ':navegador' => $dev['navegador'] ?? null,
                    ':sistema_operativo' => $dev['sistema_operativo'] ?? null,
                    ':resolucion_pantalla' => $dev['resolucion_pantalla'] ?? null,
                    ':resolucion_viewport' => $dev['resolucion_viewport'] ?? null,
                    ':pixel_ratio' => isset($dev['pixel_ratio']) ? (float) $dev['pixel_ratio'] : null,
                    ':timezone' => $dev['timezone'] ?? null,
                    ':idioma' => $dev['idioma'] ?? null,
                    ':latitud' => isset($dev['latitud']) ? (float) $dev['latitud'] : null,
                    ':longitud' => isset($dev['longitud']) ? (float) $dev['longitud'] : null,
                    ':ubicacion_texto' => $dev['ubicacion_texto'] ?? null,
                    ':tipo_conexion' => $dev['tipo_conexion'] ?? null,
                    ':plataforma' => $dev['plataforma'] ?? null,
                    ':isp' => $dev['isp'] ?? null,
                    ':referrer' => $dev['referrer'] ?? null,
                ]);
                return;
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
                ':ip' => $ip,
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
        $cols = 'id, codigo_empleado, evento, modulo, accion, ruta, ip, created_at';
        if (self::columnasDispositivoDisponible($this->pdo)) {
            $cols .= ', dispositivo_tipo, navegador, sistema_operativo, resolucion_pantalla, resolucion_viewport';
            $cols .= ', ubicacion_texto, tipo_conexion, plataforma, isp';
        }
        $st = $this->pdo->prepare(
            "SELECT {$cols}
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

    /** @return list<array{etiqueta:string,total:int}> */
    public function getAgrupadoPorCampo(string $campo, string $desde, string $hasta, int $limit = 10): array
    {
        if (!self::columnasDispositivoDisponible($this->pdo)) {
            return [];
        }
        $permitidos = [
            'dispositivo_tipo', 'navegador', 'sistema_operativo', 'resolucion_pantalla',
            'tipo_conexion', 'plataforma', 'idioma', 'timezone',
        ];
        if (!in_array($campo, $permitidos, true)) {
            return [];
        }
        $r = $this->rangoSql($desde, $hasta);
        $sql = "SELECT COALESCE(NULLIF(TRIM({$campo}), ''), 'Sin dato') AS etiqueta, COUNT(*) AS total
                FROM telemetria_eventos
                WHERE created_at BETWEEN :desde AND :hasta
                GROUP BY etiqueta
                ORDER BY total DESC
                LIMIT " . (int) $limit;
        $st = $this->pdo->prepare($sql);
        $st->execute($r);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function getUltimosDispositivosDistintos(string $desde, string $hasta, int $limit = 15): array
    {
        if (!self::columnasDispositivoDisponible($this->pdo)) {
            return [];
        }
        $r = $this->rangoSql($desde, $hasta);
        $st = $this->pdo->prepare(
            "SELECT codigo_empleado, ip, dispositivo_tipo, navegador, sistema_operativo,
                    resolucion_pantalla, resolucion_viewport, ubicacion_texto, tipo_conexion, isp,
                    MAX(created_at) AS ultimo_uso
             FROM telemetria_eventos
             WHERE created_at BETWEEN :desde AND :hasta
               AND codigo_empleado IS NOT NULL
             GROUP BY codigo_empleado, ip, dispositivo_tipo, navegador, sistema_operativo,
                      resolucion_pantalla, resolucion_viewport, ubicacion_texto, tipo_conexion, isp
             ORDER BY ultimo_uso DESC
             LIMIT " . (int) $limit
        );
        $st->execute($r);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['nombre'] = $this->nombreColaborador((string) $row['codigo_empleado']);
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
