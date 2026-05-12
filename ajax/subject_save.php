<?php
/**
 * Creazione / aggiornamento materia (AJAX JSON).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Metodo non consentito'], 405);
}

$data = json_input();
$name = isset($data['name']) ? sanitize_string((string) $data['name'], 150) : '';
if ($name === '') {
    json_response(['success' => false, 'message' => 'Nome obbligatorio'], 422);
}

$description = isset($data['description']) ? sanitize_string((string) $data['description'], 5000) : '';
$color = isset($data['color']) ? sanitize_string((string) $data['color'], 7) : '#0ea5e9';
if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
    $color = '#0ea5e9';
}

$uid = current_user_id();
$pdo = db();
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE subjects SET name = ?, description = ?, color = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$name, $description, $color, $id, $uid]);
    $chk = $pdo->prepare('SELECT id FROM subjects WHERE id = ? AND user_id = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) {
        json_response(['success' => false, 'message' => 'Materia non trovata'], 404);
    }
    json_response(['success' => true, 'message' => 'Materia aggiornata', 'id' => $id]);
}

$stmt = $pdo->prepare('INSERT INTO subjects (user_id, name, description, color) VALUES (?, ?, ?, ?)');
$stmt->execute([$uid, $name, $description, $color]);
json_response(['success' => true, 'message' => 'Materia creata', 'id' => (int) $pdo->lastInsertId()]);
