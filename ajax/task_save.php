<?php
/**
 * Creazione / aggiornamento task (AJAX JSON).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Metodo non consentito'], 405);
}

$data = json_input();
$title = isset($data['title']) ? sanitize_string((string) $data['title'], 200) : '';
if ($title === '') {
    json_response(['success' => false, 'message' => 'Titolo obbligatorio'], 422);
}

$description = isset($data['description']) ? sanitize_string((string) $data['description'], 5000) : '';
$due = isset($data['due_date']) ? trim((string) $data['due_date']) : '';
$dueSql = null;
if ($due !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $due)) {
    $dueSql = $due;
}

$priority = validate_priority(isset($data['priority']) ? (string) $data['priority'] : 'medium');

$uid = current_user_id();
$pdo = db();
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare(
        'UPDATE tasks SET title = ?, description = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$title, $description, $dueSql, $priority, $id, $uid]);
    $chk = $pdo->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) {
        json_response(['success' => false, 'message' => 'Task non trovato'], 404);
    }
    json_response(['success' => true, 'message' => 'Task aggiornato', 'id' => $id]);
}

$stmt = $pdo->prepare(
    'INSERT INTO tasks (user_id, title, description, due_date, priority, is_completed) VALUES (?, ?, ?, ?, ?, 0)'
);
$stmt->execute([$uid, $title, $description, $dueSql, $priority]);
json_response(['success' => true, 'message' => 'Task creato', 'id' => (int) $pdo->lastInsertId()]);
