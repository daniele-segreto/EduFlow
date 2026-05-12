<?php
/**
 * Connessione PDO MySQL con prepared statements.
 * Modificare credenziali secondo ambiente WAMP.
 */

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Credenziali database — adatta a phpMyAdmin / WAMP
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'teacher_dashboard';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/**
 * Singleton PDO per l'applicazione.
 */
function db(): PDO
{
    static $pdo = null;
    global $dbHost, $dbName, $dbUser, $dbPass, $dsn, $options;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    } catch (PDOException $e) {
        if (defined('APP_ENV') && APP_ENV === 'development') {
            throw $e;
        }
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Servizio temporaneamente non disponibile.');
    }

    return $pdo;
}
