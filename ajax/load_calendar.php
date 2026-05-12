<?php
/**
 * Carica eventi e lezioni in un intervallo per viste calendario (JSON).
 * GET: start=Y-m-d, end=Y-m-d (end inclusiva).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$start = isset($_GET['start']) ? sanitize_string((string) $_GET['start'], 10) : '';
$end = isset($_GET['end']) ? sanitize_string((string) $_GET['end'], 10) : '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
    json_response(['success' => false, 'message' => 'Intervallo date non valido'], 422);
}

$rangeStart = $start . ' 00:00:00';
$rangeEndExcl = date('Y-m-d', strtotime($end . ' +1 day')) . ' 00:00:00';

$pdo = db();

$st = $pdo->prepare(
    "SELECT e.id, e.title, e.start_datetime, e.end_datetime, e.category, e.priority, e.is_completed
     FROM events e
     WHERE e.user_id = ?
       AND e.start_datetime < ?
       AND (e.end_datetime IS NULL OR e.end_datetime >= ?)"
);
$st->execute([$uid, $rangeEndExcl, $rangeStart]);
$events = $st->fetchAll();

$st = $pdo->prepare(
    "SELECT l.id, l.title, l.lesson_date, l.lesson_time, l.status, s.name AS subject_name, s.color
     FROM lessons l
     JOIN subjects s ON s.id = l.subject_id
     WHERE l.user_id = ? AND l.lesson_date >= ? AND l.lesson_date <= ?"
);
$st->execute([$uid, $start, $end]);
$lessons = $st->fetchAll();

$items = [];
$cats = defined('EVENT_CATEGORIES') ? EVENT_CATEGORIES : [];

foreach ($events as $e) {
    $cat = $e['category'] ?? 'Eventi';
    $items[] = [
        'type' => 'event',
        'id' => (int) $e['id'],
        'title' => $e['title'],
        'start' => $e['start_datetime'],
        'end' => $e['end_datetime'],
        'category' => $cat,
        'color' => $cats[$cat] ?? '#3b82f6',
        'priority' => $e['priority'],
        'is_completed' => (int) $e['is_completed'],
    ];
}

foreach ($lessons as $l) {
    $time = $l['lesson_time'] ? substr((string) $l['lesson_time'], 0, 5) : '09:00';
    $startDt = $l['lesson_date'] . ' ' . $time . ':00';
    $items[] = [
        'type' => 'lesson',
        'id' => (int) $l['id'],
        'title' => $l['title'],
        'start' => $startDt,
        'end' => null,
        'category' => 'Lezioni',
        'color' => $l['color'] ?: '#0ea5e9',
        'priority' => 'medium',
        'status' => $l['status'],
        'subject_name' => $l['subject_name'],
    ];
}

usort($items, static function (array $a, array $b): int {
    return strcmp((string) $a['start'], (string) $b['start']);
});

json_response(['success' => true, 'items' => $items, 'range' => ['start' => $start, 'end' => $end]]);
