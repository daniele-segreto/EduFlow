<?php
/**
 * Dettaglio singola lezione (GET id).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    json_response(['success' => false, 'message' => 'ID non valido'], 422);
}

$stmt = db()->prepare(
    'SELECT l.*, s.name AS subject_name FROM lessons l
     JOIN subjects s ON s.id = l.subject_id
     WHERE l.id = ? AND l.user_id = ?'
);
$stmt->execute([$id, current_user_id()]);
$row = $stmt->fetch();
if (!$row) {
    json_response(['success' => false, 'message' => 'Non trovata'], 404);
}

if (!empty($row['lesson_time'])) {
    $row['lesson_time'] = substr((string) $row['lesson_time'], 0, 5);
}

json_response(['success' => true, 'lesson' => $row]);
