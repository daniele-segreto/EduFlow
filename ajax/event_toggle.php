<?php
/**
 * Toggle rapido completamento evento (AJAX JSON).
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

$done = !empty($data['is_completed']) ? 1 : 0;

$stmt = db()->prepare('UPDATE events SET is_completed = ? WHERE id = ? AND user_id = ?');
$stmt->execute([$done, $id, current_user_id()]);

$chk = db()->prepare('SELECT is_completed FROM events WHERE id = ? AND user_id = ?');
$chk->execute([$id, current_user_id()]);
$row = $chk->fetch();
if (!$row) {
    json_response(['success' => false, 'message' => 'Evento non trovato'], 404);
}

json_response(['success' => true, 'is_completed' => (int) $row['is_completed']]);
