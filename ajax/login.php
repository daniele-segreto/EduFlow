<?php
/**
 * Login AJAX: valida credenziali con password_hash e avvia sessione.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Metodo non consentito'], 405);
}

$data = json_input();
$email = isset($data['email']) ? sanitize_string((string) $data['email'], 190) : '';
$password = isset($data['password']) ? (string) $data['password'] : '';

if ($email === '' || !is_valid_email($email)) {
    json_response(['success' => false, 'message' => 'Email non valida'], 422);
}
if ($password === '') {
    json_response(['success' => false, 'message' => 'Password obbligatoria'], 422);
}

$stmt = db()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$row = $stmt->fetch();

if (!$row || empty($row['password_hash']) || !password_verify($password, (string) $row['password_hash'])) {
    json_response(['success' => false, 'message' => 'Credenziali non valide'], 401);
}

auth_login((int) $row['id'], (string) $row['name'], (string) $row['email']);

json_response([
    'success' => true,
    'message' => 'Accesso effettuato',
    'redirect' => base_url('pages/dashboard.php'),
]);
