<?php
/**
 * Eliminazione evento (AJAX JSON).
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

$uid = current_user_id();
$stmt = db()->prepare('DELETE FROM events WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $uid]);

if ($stmt->rowCount() === 0) {
    json_response(['success' => false, 'message' => 'Evento non trovato'], 404);
}

json_response(['success' => true, 'message' => 'Evento eliminato']);
