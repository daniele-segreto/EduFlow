<?php
/**
 * Logout: distrugge sessione e reindirizza al login.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';

auth_logout();
header('Location: ' . base_url('login.php'));
exit;
