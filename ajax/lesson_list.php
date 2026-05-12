<?php
/**
 * Lista lezioni con join materia (GET: subject_id, status, q opzionali).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$subjectId = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
$status = isset($_GET['status']) ? sanitize_string((string) $_GET['status'], 20) : '';
$q = isset($_GET['q']) ? sanitize_string((string) $_GET['q'], 200) : '';

$sql = 'SELECT l.id, l.title, l.lesson_date, l.lesson_time, l.notes, l.status, l.subject_id,
        s.name AS subject_name, s.color AS subject_color
        FROM lessons l JOIN subjects s ON s.id = l.subject_id
        WHERE l.user_id = ?';
$params = [$uid];

if ($subjectId > 0) {
    $sql .= ' AND l.subject_id = ?';
    $params[] = $subjectId;
}

if ($status !== '' && $status !== 'all') {
    $sql .= ' AND l.status = ?';
    $params[] = validate_lesson_status($status);
}

if ($q !== '') {
    $sql .= ' AND (l.title LIKE ? OR l.notes LIKE ? OR s.name LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= ' ORDER BY l.lesson_date DESC, l.lesson_time DESC, l.id DESC LIMIT 300';

$stmt = db()->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'lessons' => $stmt->fetchAll()]);
