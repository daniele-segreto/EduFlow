<?php
/**
 * Autenticazione basata su sessione.
 * Struttura estendibile: aggiungere token CSRF, remember-me, ruoli, ecc.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

/**
 * ID utente corrente o null se non autenticato.
 */
function current_user_id(): ?int
{
    if (!empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }
    return null;
}

/**
 * Imposta sessione dopo login valido.
 */
function auth_login(int $userId, string $name, string $email): void
{
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    session_regenerate_id(true);
}

/**
 * Distrugge sessione (logout).
 */
function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
    }
    session_destroy();
}

/**
 * Richiede utente autenticato; altrimenti redirect a login o JSON 401.
 */
function require_auth(): void
{
    if (current_user_id() !== null) {
        return;
    }
    if (is_ajax_request()) {
        json_response(['success' => false, 'message' => 'Non autenticato.'], 401);
    }
    header('Location: ' . base_url('login.php'));
    exit;
}

/**
 * Dati utente per saluto dashboard.
 *
 * @return array{name: string, email: string}|null
 */
function current_user(): ?array
{
    $id = current_user_id();
    if ($id === null) {
        return null;
    }
    return [
        'name'  => (string) ($_SESSION['user_name'] ?? 'Utente'),
        'email' => (string) ($_SESSION['user_email'] ?? ''),
    ];
}
