<?php
/**
 * Entry point: reindirizza alla dashboard o al login.
 */
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

if (current_user_id() !== null) {
    header('Location: ' . base_url('pages/dashboard.php'));
} else {
    header('Location: ' . base_url('login.php'));
}
exit;
