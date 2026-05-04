<?php

declare(strict_types=1);

/**
 * Lista para Mundial 2026: nombre en español + código ISO 3166-1 alpha-2 (minúsculas)
 * para banderas vía https://flagcdn.com (se ve bien en Windows; los emoji suelen fallar).
 *
 * @return list<array{nombre: string, iso: string}>
 */
function quiniela_paises_mundial_lista(): array
{
    return [
        ['nombre' => 'Alemania', 'iso' => 'de'],
        ['nombre' => 'Arabia Saudita', 'iso' => 'sa'],
        ['nombre' => 'Argentina', 'iso' => 'ar'],
        ['nombre' => 'Australia', 'iso' => 'au'],
        ['nombre' => 'Austria', 'iso' => 'at'],
        ['nombre' => 'Bélgica', 'iso' => 'be'],
        ['nombre' => 'Bolivia', 'iso' => 'bo'],
        ['nombre' => 'Brasil', 'iso' => 'br'],
        ['nombre' => 'Canadá', 'iso' => 'ca'],
        ['nombre' => 'Camerún', 'iso' => 'cm'],
        ['nombre' => 'Chile', 'iso' => 'cl'],
        ['nombre' => 'China', 'iso' => 'cn'],
        ['nombre' => 'Colombia', 'iso' => 'co'],
        ['nombre' => 'Corea del Sur', 'iso' => 'kr'],
        ['nombre' => 'Costa de Marfil', 'iso' => 'ci'],
        ['nombre' => 'Costa Rica', 'iso' => 'cr'],
        ['nombre' => 'Croacia', 'iso' => 'hr'],
        ['nombre' => 'Cuba', 'iso' => 'cu'],
        ['nombre' => 'Curazao', 'iso' => 'cw'],
        ['nombre' => 'Dinamarca', 'iso' => 'dk'],
        ['nombre' => 'Ecuador', 'iso' => 'ec'],
        ['nombre' => 'Egipto', 'iso' => 'eg'],
        ['nombre' => 'El Salvador', 'iso' => 'sv'],
        ['nombre' => 'Escocia', 'iso' => 'gb'],
        ['nombre' => 'Eslovaquia', 'iso' => 'sk'],
        ['nombre' => 'Eslovenia', 'iso' => 'si'],
        ['nombre' => 'España', 'iso' => 'es'],
        ['nombre' => 'Estados Unidos', 'iso' => 'us'],
        ['nombre' => 'Francia', 'iso' => 'fr'],
        ['nombre' => 'Gales', 'iso' => 'gb'],
        ['nombre' => 'Ghana', 'iso' => 'gh'],
        ['nombre' => 'Grecia', 'iso' => 'gr'],
        ['nombre' => 'Guatemala', 'iso' => 'gt'],
        ['nombre' => 'Haití', 'iso' => 'ht'],
        ['nombre' => 'Honduras', 'iso' => 'hn'],
        ['nombre' => 'Hungría', 'iso' => 'hu'],
        ['nombre' => 'Inglaterra', 'iso' => 'gb'],
        ['nombre' => 'Irán', 'iso' => 'ir'],
        ['nombre' => 'Irlanda', 'iso' => 'ie'],
        ['nombre' => 'Islandia', 'iso' => 'is'],
        ['nombre' => 'Israel', 'iso' => 'il'],
        ['nombre' => 'Italia', 'iso' => 'it'],
        ['nombre' => 'Jamaica', 'iso' => 'jm'],
        ['nombre' => 'Japón', 'iso' => 'jp'],
        ['nombre' => 'Jordania', 'iso' => 'jo'],
        ['nombre' => 'Marruecos', 'iso' => 'ma'],
        ['nombre' => 'México', 'iso' => 'mx'],
        ['nombre' => 'Nigeria', 'iso' => 'ng'],
        ['nombre' => 'Noruega', 'iso' => 'no'],
        ['nombre' => 'Nueva Zelanda', 'iso' => 'nz'],
        ['nombre' => 'Países Bajos', 'iso' => 'nl'],
        ['nombre' => 'Panamá', 'iso' => 'pa'],
        ['nombre' => 'Paraguay', 'iso' => 'py'],
        ['nombre' => 'Perú', 'iso' => 'pe'],
        ['nombre' => 'Polonia', 'iso' => 'pl'],
        ['nombre' => 'Portugal', 'iso' => 'pt'],
        ['nombre' => 'Qatar', 'iso' => 'qa'],
        ['nombre' => 'República Checa', 'iso' => 'cz'],
        ['nombre' => 'República Dominicana', 'iso' => 'do'],
        ['nombre' => 'Rumania', 'iso' => 'ro'],
        ['nombre' => 'Senegal', 'iso' => 'sn'],
        ['nombre' => 'Serbia', 'iso' => 'rs'],
        ['nombre' => 'Sudáfrica', 'iso' => 'za'],
        ['nombre' => 'Suecia', 'iso' => 'se'],
        ['nombre' => 'Suiza', 'iso' => 'ch'],
        ['nombre' => 'Túnez', 'iso' => 'tn'],
        ['nombre' => 'Turquía', 'iso' => 'tr'],
        ['nombre' => 'Ucrania', 'iso' => 'ua'],
        ['nombre' => 'Uruguay', 'iso' => 'uy'],
        ['nombre' => 'Venezuela', 'iso' => 've'],
        ['nombre' => 'Zambia', 'iso' => 'zm'],
    ];
}

/** Nombre exacto del país (como en la lista) => código ISO minúsculas */
function quiniela_paises_mapa_nombre_a_iso(): array
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $map = [];
    foreach (quiniela_paises_mundial_lista() as $p) {
        $map[$p['nombre']] = $p['iso'];
    }
    return $map;
}

/** ISO por nombre (coincidencia sin distinguir mayúsculas) o null */
function quiniela_paises_iso_por_nombre(string $nombre): ?string
{
    $k = mb_strtolower(trim($nombre), 'UTF-8');
    foreach (quiniela_paises_mundial_lista() as $p) {
        if (mb_strtolower(trim($p['nombre']), 'UTF-8') === $k) {
            return $p['iso'];
        }
    }
    return null;
}

/**
 * URL PNG flagcdn (w40 por defecto). ISO inválido o vacío → ONU (un) como neutro.
 *
 * @see https://flagcdn.com
 */
function quiniela_get_flag_url(?string $iso, int $w = 40): string
{
    $norm = strtolower(preg_replace('/[^a-z]/', '', (string) $iso));
    if ($norm === '' || strlen($norm) !== 2) {
        $norm = 'un';
    }
    return 'https://flagcdn.com/w' . $w . '/' . $norm . '.png';
}

/** @deprecated Use quiniela_get_flag_url() */
function quiniela_paises_url_bandera(string $iso, int $size = 40): string
{
    return quiniela_get_flag_url($iso, $size);
}

/**
 * HTML: imagen bandera + nombre (nombre escapado).
 * La clase .flag-icon debe estar definida en las vistas del módulo.
 */
function quiniela_flag_icon_html(?string $iso, string $nombre = '', bool $conNombre = true): string
{
    $url = quiniela_get_flag_url($iso, 40);
    $img = '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
        . '" alt="" class="flag-icon" width="20" height="15" loading="lazy" decoding="async" referrerpolicy="no-referrer">';
    if (!$conNombre || $nombre === '') {
        return $img;
    }
    return '<span class="d-inline-flex align-items-center">' . $img
        . '<span class="align-middle">' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</span></span>';
}

/** Texto plano (sin HTML) para etiquetas donde no hay Tom Select */
function quiniela_paises_etiqueta_opcion(string $nombre): string
{
    $iso = quiniela_paises_iso_por_nombre($nombre);
    if ($iso !== null) {
        return strtoupper($iso) . ' ' . $nombre;
    }
    return $nombre;
}
