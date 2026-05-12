<?php
/**
 * Configurazione applicativa: percorsi, costanti, categorie eventi.
 * In produzione impostare display_errors su false e gestire logging.
 */

declare(strict_types=1);

/**
 * Percorso URL base sotto il dominio (es. /cursor-dashboard).
 * Stringa vuota se il progetto è servito dalla root del virtual host.
 */
if (!defined('APP_URL_BASE')) {
    define('APP_URL_BASE', '/cursor-dashboard');
}

// Percorso filesystem della root del progetto (directory parent di /config)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Nome applicazione
if (!defined('APP_NAME')) {
    define('APP_NAME', 'EduFlow');
}

// Categorie evento predefinite (palette coerente con UI)
if (!defined('EVENT_CATEGORIES')) {
    define('EVENT_CATEGORIES', [
        'Lezioni' => '#0ea5e9',
        'Famiglia' => '#8b5cf6',
        'Visite mediche' => '#f43f5e',
        'CAF/Burocrazia' => '#64748b',
        'Pagamenti' => '#eab308',
        'Spesa' => '#22c55e',
        'Eventi' => '#3b82f6',
        'Tempo libero' => '#06b6d4',
    ]);
}

// Ambiente (development | production)
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

// Timezone Europa
date_default_timezone_set('Europe/Rome');

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
