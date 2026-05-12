<?php
/**
 * Dati aggregati per la dashboard home (JSON).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$uid = current_user_id();
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

$pdo = db();

// Lezioni di oggi
$st = $pdo->prepare("SELECT l.id, l.title, l.lesson_date, l.lesson_time, l.status, s.name AS subject_name, s.color
    FROM lessons l JOIN subjects s ON s.id = l.subject_id
    WHERE l.user_id = ? AND l.lesson_date = ? ORDER BY l.lesson_time ASC, l.id ASC");
$st->execute([$uid, $today]);
$lessons_today = $st->fetchAll();

// Prossime lezioni (da domani)
$st = $pdo->prepare("SELECT l.id, l.title, l.lesson_date, l.lesson_time, l.status, s.name AS subject_name, s.color
    FROM lessons l JOIN subjects s ON s.id = l.subject_id
    WHERE l.user_id = ? AND l.lesson_date > ? AND l.status = 'scheduled'
    ORDER BY l.lesson_date ASC, l.lesson_time ASC LIMIT 8");
$st->execute([$uid, $today]);
$lessons_upcoming = $st->fetchAll();

// Eventi oggi e prossimi
$st = $pdo->prepare("SELECT id, title, start_datetime, end_datetime, category, priority, is_completed
    FROM events WHERE user_id = ? AND is_completed = 0 AND start_datetime >= ?
    ORDER BY start_datetime ASC LIMIT 12");
$st->execute([$uid, $now]);
$events_upcoming = $st->fetchAll();

$st = $pdo->prepare("SELECT id, title, start_datetime, category, priority FROM events
    WHERE user_id = ? AND DATE(start_datetime) = ? AND is_completed = 0 ORDER BY start_datetime ASC");
$st->execute([$uid, $today]);
$events_today = $st->fetchAll();

// Task aperti urgenti / in scadenza
$st = $pdo->prepare("SELECT id, title, due_date, priority, is_completed FROM tasks
    WHERE user_id = ? AND is_completed = 0
    ORDER BY 
        CASE priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
        due_date IS NULL, due_date ASC LIMIT 10");
$st->execute([$uid]);
$tasks_open = $st->fetchAll();

// Statistiche rapide
$st = $pdo->prepare('SELECT COUNT(*) FROM subjects WHERE user_id = ?');
$st->execute([$uid]);
$count_subjects = (int) $st->fetchColumn();

$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));
$st = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE user_id = ? AND lesson_date BETWEEN ? AND ?");
$st->execute([$uid, $weekStart, $weekEnd]);
$lessons_week = (int) $st->fetchColumn();

$st = $pdo->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_completed = 0');
$st->execute([$uid]);
$tasks_open_count = (int) $st->fetchColumn();

$st = $pdo->prepare("SELECT COUNT(*) FROM events WHERE user_id = ? AND is_completed = 0 AND start_datetime >= ?");
$st->execute([$uid, $now]);
$events_future_count = (int) $st->fetchColumn();

// Mini calendario: eventi + lezioni prossimi 14 giorni
$endMini = date('Y-m-d', strtotime($today . ' +14 days'));
$st = $pdo->prepare("SELECT lesson_date AS d, COUNT(*) AS c FROM lessons WHERE user_id = ? AND lesson_date BETWEEN ? AND ? GROUP BY lesson_date");
$st->execute([$uid, $today, $endMini]);
$lesson_days = $st->fetchAll(PDO::FETCH_KEY_PAIR);

$st = $pdo->prepare("SELECT DATE(start_datetime) AS d, COUNT(*) AS c FROM events WHERE user_id = ? AND DATE(start_datetime) BETWEEN ? AND ? GROUP BY DATE(start_datetime)");
$st->execute([$uid, $today, $endMini]);
$event_days = $st->fetchAll(PDO::FETCH_KEY_PAIR);

json_response([
    'success' => true,
    'today' => $today,
    'lessons_today' => $lessons_today,
    'lessons_upcoming' => $lessons_upcoming,
    'events_today' => $events_today,
    'events_upcoming' => $events_upcoming,
    'tasks_open' => $tasks_open,
    'stats' => [
        'subjects' => $count_subjects,
        'lessons_week' => $lessons_week,
        'tasks_open' => $tasks_open_count,
        'events_upcoming' => $events_future_count,
    ],
    'calendar_mini' => [
        'lesson_days' => $lesson_days ?: new stdClass(),
        'event_days' => $event_days ?: new stdClass(),
    ],
]);
