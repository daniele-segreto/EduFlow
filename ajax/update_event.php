<?php
/**
 * Aggiornamento evento (AJAX JSON, richiede id).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Metodo non consentito'], 405);
}

$data = json_input();
$id = isset($data['id']) ? (int) $data['id'] : 0;
if ($id <= 0) {
    json_response(['success' => false, 'message' => 'ID mancante'], 422);
}

$title = isset($data['title']) ? sanitize_string((string) $data['title'], 200) : '';
if ($title === '') {
    json_response(['success' => false, 'message' => 'Titolo obbligatorio'], 422);
}

$startRaw = isset($data['start_datetime']) ? (string) $data['start_datetime'] : '';
$endRaw = isset($data['end_datetime']) ? trim((string) $data['end_datetime']) : '';
$remRaw = isset($data['reminder_datetime']) ? trim((string) $data['reminder_datetime']) : '';

$startTs = strtotime(str_replace('T', ' ', $startRaw));
if ($startTs === false) {
    json_response(['success' => false, 'message' => 'Data/ora inizio non valida'], 422);
}
$startSql = date('Y-m-d H:i:s', $startTs);

$endSql = null;
if ($endRaw !== '') {
    $endTs = strtotime(str_replace('T', ' ', $endRaw));
    if ($endTs !== false) {
        $endSql = date('Y-m-d H:i:s', $endTs);
    }
}

$remSql = null;
if ($remRaw !== '') {
    $remTs = strtotime(str_replace('T', ' ', $remRaw));
    if ($remTs !== false) {
        $remSql = date('Y-m-d H:i:s', $remTs);
    }
}

$category = validate_event_category(isset($data['category']) ? (string) $data['category'] : 'Eventi');
$priority = validate_priority(isset($data['priority']) ? (string) $data['priority'] : 'medium');
$description = isset($data['description']) ? sanitize_string((string) $data['description'], 5000) : '';
$isCompleted = !empty($data['is_completed']) ? 1 : 0;

$uid = current_user_id();
$pdo = db();
$stmt = $pdo->prepare(
    'UPDATE events SET title = ?, description = ?, start_datetime = ?, end_datetime = ?, category = ?, priority = ?, reminder_datetime = ?, is_completed = ?
     WHERE id = ? AND user_id = ?'
);
$stmt->execute([$title, $description, $startSql, $endSql, $category, $priority, $remSql, $isCompleted, $id, $uid]);

$chk = $pdo->prepare('SELECT id FROM events WHERE id = ? AND user_id = ?');
$chk->execute([$id, $uid]);
if (!$chk->fetch()) {
    json_response(['success' => false, 'message' => 'Evento non trovato o non autorizzato'], 404);
}

json_response(['success' => true, 'message' => 'Evento aggiornato']);
