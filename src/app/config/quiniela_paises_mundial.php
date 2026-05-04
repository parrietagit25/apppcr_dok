<?php

declare(strict_types=1);

/**
 * Lista de selecciones para Mundial 2026 (nombres en español + bandera Unicode).
 * Si falta alguna, el usuario puede escribirla (Tom Select create).
 *
 * @return list<array{nombre: string, bandera: string}>
 */
function quiniela_paises_mundial_lista(): array
{
    return [
        ['nombre' => 'Alemania', 'bandera' => '🇩🇪'],
        ['nombre' => 'Arabia Saudita', 'bandera' => '🇸🇦'],
        ['nombre' => 'Argentina', 'bandera' => '🇦🇷'],
        ['nombre' => 'Australia', 'bandera' => '🇦🇺'],
        ['nombre' => 'Austria', 'bandera' => '🇦🇹'],
        ['nombre' => 'Bélgica', 'bandera' => '🇧🇪'],
        ['nombre' => 'Bolivia', 'bandera' => '🇧🇴'],
        ['nombre' => 'Brasil', 'bandera' => '🇧🇷'],
        ['nombre' => 'Canadá', 'bandera' => '🇨🇦'],
        ['nombre' => 'Camerún', 'bandera' => '🇨🇲'],
        ['nombre' => 'Chile', 'bandera' => '🇨🇱'],
        ['nombre' => 'China', 'bandera' => '🇨🇳'],
        ['nombre' => 'Colombia', 'bandera' => '🇨🇴'],
        ['nombre' => 'Corea del Sur', 'bandera' => '🇰🇷'],
        ['nombre' => 'Costa de Marfil', 'bandera' => '🇨🇮'],
        ['nombre' => 'Costa Rica', 'bandera' => '🇨🇷'],
        ['nombre' => 'Croacia', 'bandera' => '🇭🇷'],
        ['nombre' => 'Cuba', 'bandera' => '🇨🇺'],
        ['nombre' => 'Curazao', 'bandera' => '🇨🇼'],
        ['nombre' => 'Dinamarca', 'bandera' => '🇩🇰'],
        ['nombre' => 'Ecuador', 'bandera' => '🇪🇨'],
        ['nombre' => 'Egipto', 'bandera' => '🇪🇬'],
        ['nombre' => 'El Salvador', 'bandera' => '🇸🇻'],
        ['nombre' => 'Escocia', 'bandera' => '🇬🇧'],
        ['nombre' => 'Eslovaquia', 'bandera' => '🇸🇰'],
        ['nombre' => 'Eslovenia', 'bandera' => '🇸🇮'],
        ['nombre' => 'España', 'bandera' => '🇪🇸'],
        ['nombre' => 'Estados Unidos', 'bandera' => '🇺🇸'],
        ['nombre' => 'Francia', 'bandera' => '🇫🇷'],
        ['nombre' => 'Gales', 'bandera' => '🇬🇧'],
        ['nombre' => 'Ghana', 'bandera' => '🇬🇭'],
        ['nombre' => 'Grecia', 'bandera' => '🇬🇷'],
        ['nombre' => 'Guatemala', 'bandera' => '🇬🇹'],
        ['nombre' => 'Haití', 'bandera' => '🇭🇹'],
        ['nombre' => 'Honduras', 'bandera' => '🇭🇳'],
        ['nombre' => 'Hungría', 'bandera' => '🇭🇺'],
        ['nombre' => 'Inglaterra', 'bandera' => '🇬🇧'],
        ['nombre' => 'Irán', 'bandera' => '🇮🇷'],
        ['nombre' => 'Irlanda', 'bandera' => '🇮🇪'],
        ['nombre' => 'Islandia', 'bandera' => '🇮🇸'],
        ['nombre' => 'Israel', 'bandera' => '🇮🇱'],
        ['nombre' => 'Italia', 'bandera' => '🇮🇹'],
        ['nombre' => 'Jamaica', 'bandera' => '🇯🇲'],
        ['nombre' => 'Japón', 'bandera' => '🇯🇵'],
        ['nombre' => 'Jordania', 'bandera' => '🇯🇴'],
        ['nombre' => 'Marruecos', 'bandera' => '🇲🇦'],
        ['nombre' => 'México', 'bandera' => '🇲🇽'],
        ['nombre' => 'Nigeria', 'bandera' => '🇳🇬'],
        ['nombre' => 'Noruega', 'bandera' => '🇳🇴'],
        ['nombre' => 'Nueva Zelanda', 'bandera' => '🇳🇿'],
        ['nombre' => 'Países Bajos', 'bandera' => '🇳🇱'],
        ['nombre' => 'Panamá', 'bandera' => '🇵🇦'],
        ['nombre' => 'Paraguay', 'bandera' => '🇵🇾'],
        ['nombre' => 'Perú', 'bandera' => '🇵🇪'],
        ['nombre' => 'Polonia', 'bandera' => '🇵🇱'],
        ['nombre' => 'Portugal', 'bandera' => '🇵🇹'],
        ['nombre' => 'Qatar', 'bandera' => '🇶🇦'],
        ['nombre' => 'República Checa', 'bandera' => '🇨🇿'],
        ['nombre' => 'República Dominicana', 'bandera' => '🇩🇴'],
        ['nombre' => 'Rumania', 'bandera' => '🇷🇴'],
        ['nombre' => 'Senegal', 'bandera' => '🇸🇳'],
        ['nombre' => 'Serbia', 'bandera' => '🇷🇸'],
        ['nombre' => 'Sudáfrica', 'bandera' => '🇿🇦'],
        ['nombre' => 'Suecia', 'bandera' => '🇸🇪'],
        ['nombre' => 'Suiza', 'bandera' => '🇨🇭'],
        ['nombre' => 'Túnez', 'bandera' => '🇹🇳'],
        ['nombre' => 'Turquía', 'bandera' => '🇹🇷'],
        ['nombre' => 'Ucrania', 'bandera' => '🇺🇦'],
        ['nombre' => 'Uruguay', 'bandera' => '🇺🇾'],
        ['nombre' => 'Venezuela', 'bandera' => '🇻🇪'],
        ['nombre' => 'Zambia', 'bandera' => '🇿🇲'],
    ];
}

/** Mapa nombre normalizado (minúsculas) => "🇵🇦 Panamá" */
function quiniela_paises_mapa_etiqueta(): array
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $map = [];
    foreach (quiniela_paises_mundial_lista() as $p) {
        $k = mb_strtolower(trim($p['nombre']), 'UTF-8');
        $map[$k] = $p['bandera'] . ' ' . $p['nombre'];
    }
    return $map;
}

/** Texto para <option> o listas a partir del nombre guardado en BD. */
function quiniela_paises_etiqueta_opcion(string $nombre): string
{
    $k = mb_strtolower(trim($nombre), 'UTF-8');
    $m = quiniela_paises_mapa_etiqueta();
    return $m[$k] ?? ('⚽ ' . $nombre);
}
