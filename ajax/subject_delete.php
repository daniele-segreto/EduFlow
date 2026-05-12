<?php
/**
 * Eliminazione materia (AJAX JSON). Le lezioni collegate vanno in CASCADE dal DB.
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

$stmt = db()->prepare('DELETE FROM subjects WHERE id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);

if ($stmt->rowCount() === 0) {
    json_response(['success' => false, 'message' => 'Materia non trovata'], 404);
}

json_response(['success' => true, 'message' => 'Materia eliminata']);
