<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class MailService
{
    private static function remitente(): string
    {
        $nombre = defined('RESEND_FROM_NAME') ? RESEND_FROM_NAME : 'AM Gente notificaciones';
        $email = defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : 'notificaciones@automarket.com.pa';
        return $nombre . ' <' . $email . '>';
    }

    private static function apiKey(): string
    {
        if (!defined('RESEND_API_KEY') || RESEND_API_KEY === '') {
            throw new RuntimeException('RESEND_API_KEY no está configurada.');
        }
        return RESEND_API_KEY;
    }

    /**
     * @param list<string> $cc
     * @return true|string true si ok, string con error si falla
     */
    public static function enviar(string $to, array $cc, string $asunto, string $html)
    {
        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return 'Correo destino inválido.';
        }

        $cc = self::normalizarCorreos($cc);
        if ($to !== '' && in_array(strtolower($to), array_map('strtolower', $cc), true)) {
            $cc = array_values(array_filter($cc, fn ($c) => strcasecmp($c, $to) !== 0));
        }

        try {
            $payload = [
                'from' => self::remitente(),
                'to' => [$to],
                'subject' => $asunto,
                'html' => $html,
            ];
            if ($cc !== []) {
                $payload['cc'] = $cc;
            }

            $resend = \Resend::client(self::apiKey());
            $resend->emails->send($payload);
            return true;
        } catch (Throwable $e) {
            error_log('MailService::enviar Resend: ' . $e->getMessage());
            return 'Error al enviar el correo: ' . $e->getMessage();
        }
    }

    /**
     * @param list<string> $cc
     * @return true|string
     */
    public static function enviarConAdjunto(string $to, array $cc, string $asunto, string $html, string $rutaArchivo)
    {
        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return 'Correo destino inválido.';
        }
        if (!is_readable($rutaArchivo)) {
            return 'No se puede leer el archivo adjunto.';
        }

        $cc = self::normalizarCorreos($cc);

        try {
            $payload = [
                'from' => self::remitente(),
                'to' => [$to],
                'subject' => $asunto,
                'html' => $html,
                'attachments' => [[
                    'filename' => basename($rutaArchivo),
                    'content' => base64_encode((string) file_get_contents($rutaArchivo)),
                ]],
            ];
            if ($cc !== []) {
                $payload['cc'] = $cc;
            }

            $resend = \Resend::client(self::apiKey());
            $resend->emails->send($payload);
            return true;
        } catch (Throwable $e) {
            error_log('MailService::enviarConAdjunto Resend: ' . $e->getMessage());
            return 'Error al enviar el correo: ' . $e->getMessage();
        }
    }

    /**
     * @param list<string>|string|null $correos
     * @return list<string>
     */
    private static function normalizarCorreos($correos): array
    {
        if ($correos === null) {
            return [];
        }
        if (is_string($correos)) {
            $correos = [$correos];
        }
        $out = [];
        foreach ($correos as $correo) {
            $correo = trim((string) $correo);
            if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $out[] = $correo;
            }
        }
        return array_values(array_unique($out));
    }
}
