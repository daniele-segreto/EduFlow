<?php
/**
 * Creazione / aggiornamento lezione (AJAX JSON).
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

$subjectId = isset($data['subject_id']) ? (int) $data['subject_id'] : 0;
$lessonDate = isset($data['lesson_date']) ? sanitize_string((string) $data['lesson_date'], 10) : '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $lessonDate)) {
    json_response(['success' => false, 'message' => 'Data non valida'], 422);
}

$lessonTime = isset($data['lesson_time']) ? sanitize_string((string) $data['lesson_time'], 8) : '';
$timeSql = null;
if ($lessonTime !== '') {
    if (!preg_match('/^\d{2}:\d{2}$/', $lessonTime)) {
        json_response(['success' => false, 'message' => 'Orario non valido'], 422);
    }
    $timeSql = $lessonTime . ':00';
}

$notes = isset($data['notes']) ? sanitize_string((string) $data['notes'], 5000) : '';
$status = validate_lesson_status(isset($data['status']) ? (string) $data['status'] : 'scheduled');

$uid = current_user_id();
$pdo = db();

// Verifica che la materia appartenga all'utente
$chk = $pdo->prepare('SELECT id FROM subjects WHERE id = ? AND user_id = ?');
$chk->execute([$subjectId, $uid]);
if (!$chk->fetch()) {
    json_response(['success' => false, 'message' => 'Materia non valida'], 422);
}

$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare(
        'UPDATE lessons SET subject_id = ?, title = ?, lesson_date = ?, lesson_time = ?, notes = ?, status = ?
         WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$subjectId, $title, $lessonDate, $timeSql, $notes, $status, $id, $uid]);
    $chk = $pdo->prepare('SELECT id FROM lessons WHERE id = ? AND user_id = ?');
    $chk->execute([$id, $uid]);
    if (!$chk->fetch()) {
        json_response(['success' => false, 'message' => 'Lezione non trovata'], 404);
    }
    json_response(['success' => true, 'message' => 'Lezione aggiornata', 'id' => $id]);
}

$stmt = $pdo->prepare(
    'INSERT INTO lessons (user_id, subject_id, title, lesson_date, lesson_time, notes, status)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$uid, $subjectId, $title, $lessonDate, $timeSql, $notes, $status]);
json_response(['success' => true, 'message' => 'Lezione creata', 'id' => (int) $pdo->lastInsertId()]);
