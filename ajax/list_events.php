<?php
/**
 * Lista eventi con filtri opzionali (categoria, completati, ricerca).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$q = isset($_GET['q']) ? sanitize_string((string) $_GET['q'], 200) : '';
$cat = isset($_GET['category']) ? sanitize_string((string) $_GET['category'], 50) : '';
$show = isset($_GET['show']) ? (string) $_GET['show'] : 'active';

$sql = 'SELECT id, title, description, start_datetime, end_datetime, category, priority, reminder_datetime, is_completed
        FROM events WHERE user_id = ?';
$params = [$uid];

if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}

if ($cat !== '' && $cat !== 'all') {
    $sql .= ' AND category = ?';
    $params[] = $cat;
}

if ($show === 'active') {
    $sql .= ' AND is_completed = 0';
} elseif ($show === 'done') {
    $sql .= ' AND is_completed = 1';
}

$sql .= ' ORDER BY start_datetime DESC LIMIT 200';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'events' => $stmt->fetchAll()]);
