<?php
/**
 * Singolo evento per modale (GET id).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    json_response(['success' => false, 'message' => 'ID non valido'], 422);
}

$stmt = db()->prepare('SELECT * FROM events WHERE id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);
$row = $stmt->fetch();
if (!$row) {
    json_response(['success' => false, 'message' => 'Non trovato'], 404);
}

// Formato datetime-local
$fmt = static function (?string $sql): string {
    if ($sql === null || $sql === '') {
        return '';
    }
    $d = date_create($sql);
    return $d ? $d->format('Y-m-d\TH:i') : '';
};

$row['start_local'] = $fmt($row['start_datetime'] ?? null);
$row['end_local'] = $fmt($row['end_datetime'] ?? null);
$row['reminder_local'] = $fmt($row['reminder_datetime'] ?? null);

json_response(['success' => true, 'event' => $row]);
