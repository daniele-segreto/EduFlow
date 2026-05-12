<?php
/**
 * Funzioni di utilità: sanitizzazione, validazione, risposte JSON.
 */

declare(strict_types=1);

/**
 * Escape HTML per output sicuro.
 */
function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Trim e limitazione lunghezza stringa.
 */
function sanitize_string(?string $value, int $max = 500): string
{
    $v = trim((string) $value);
    if (mb_strlen($v) > $max) {
        $v = mb_substr($v, 0, $max);
    }
    return $v;
}

/**
 * Validazione email base.
 */
function is_valid_email(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Invia risposta JSON e termina lo script.
 *
 * @param array<string, mixed> $payload
 */
function json_response(array $payload, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Legge JSON body per richieste AJAX POST.
 *
 * @return array<string, mixed>
 */
function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return is_array($data) ? $data : [];
    } catch (JsonException $e) {
        return [];
    }
}

/**
 * Percorso base per link e asset (rispetta APP_URL_BASE in config/app.php).
 */
function base_url(string $path = ''): string
{
    $base = defined('APP_URL_BASE') ? rtrim((string) APP_URL_BASE, '/') : '';
    $path = ltrim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base . '/') . $path;
}

/**
 * Verifica se la richiesta è AJAX (header comune jQuery).
 */
function is_ajax_request(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Valida categoria evento rispetto a EVENT_CATEGORIES.
 */
function validate_event_category(string $cat): string
{
    if (defined('EVENT_CATEGORIES') && is_array(EVENT_CATEGORIES) && array_key_exists($cat, EVENT_CATEGORIES)) {
        return $cat;
    }
    return 'Eventi';
}

/**
 * Priorità ammessa per eventi e task.
 */
function validate_priority(string $p): string
{
    return in_array($p, ['low', 'medium', 'high', 'urgent'], true) ? $p : 'medium';
}

/**
 * Stato lezione.
 */
function validate_lesson_status(string $s): string
{
    return in_array($s, ['scheduled', 'completed', 'cancelled'], true) ? $s : 'scheduled';
}
