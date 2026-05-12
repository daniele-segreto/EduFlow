<?php
/**
 * Lista materie con conteggio lezioni e ricerca live (GET q opzionale).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$q = isset($_GET['q']) ? sanitize_string((string) $_GET['q'], 200) : '';

$sql = 'SELECT s.id, s.name, s.description, s.color, s.created_at,
        (SELECT COUNT(*) FROM lessons l WHERE l.subject_id = s.id) AS lessons_count
        FROM subjects s WHERE s.user_id = ?';
$params = [$uid];

if ($q !== '') {
    $sql .= ' AND (s.name LIKE ? OR s.description LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}

$sql .= ' ORDER BY s.name ASC';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'subjects' => $stmt->fetchAll()]);
