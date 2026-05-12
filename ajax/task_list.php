<?php
/**
 * Lista task (GET: show=open|all).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$show = isset($_GET['show']) ? (string) $_GET['show'] : 'open';

$sql = 'SELECT id, title, description, due_date, priority, is_completed, created_at FROM tasks WHERE user_id = ?';
$params = [$uid];

if ($show === 'open') {
    $sql .= ' AND is_completed = 0';
}

$sql .= ' ORDER BY is_completed ASC,
    CASE priority WHEN \'urgent\' THEN 0 WHEN \'high\' THEN 1 WHEN \'medium\' THEN 2 ELSE 3 END,
    due_date IS NULL, due_date ASC, id DESC LIMIT 200';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'tasks' => $stmt->fetchAll()]);
