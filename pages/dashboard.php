<?php
/**
 * Home dashboard: saluto, riepilogo, widget caricati via AJAX.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$current_page = 'dashboard';
$user = current_user();
$page_title = 'Dashboard';
$page_heading = 'Home';
$page_subtitle = 'Panoramica della tua giornata';

include dirname(__DIR__) . '/components/header.php';
include dirname(__DIR__) . '/components/shell_start.php';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <p class="text-muted small mb-1" id="dashGreetingWrap">Ciao, <span id="dashGreetingName" class="fw-semibold text-body"><?= h($user['name'] ?? 'Utente') ?></span></p>
        <h2 class="h4 mb-0 fw-semibold" id="dashHeadline">Ecco il tuo riepilogo</h2>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary rounded-3 px-3" id="btnNewTaskDash" data-bs-toggle="modal" data-bs-target="#modalTask">
            <i class="bi bi-plus-lg me-1"></i> Task
        </button>
        <button type="button" class="btn btn-light border rounded-3 px-3" id="btnDashRefresh" title="Aggiorna">
            <i class="bi bi-arrow-clockwise"></i>
        </button>
    </div>
</div>

<div class="row g-3 mb-3" id="dashStatsRow">
    <div class="col-6 col-xl-3">
        <div class="stat-tile h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <p class="small text-muted mb-1">Materie</p>
                    <p class="h3 mb-0 fw-bold" id="statSubjects">—</p>
                </div>
                <span class="icon-wrap bg-primary-subtle text-primary"><i class="bi bi-bookmarks"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <p class="small text-muted mb-1">Lezioni (settimana)</p>
                    <p class="h3 mb-0 fw-bold" id="statLessonsWeek">—</p>
                </div>
                <span class="icon-wrap bg-info-subtle text-info"><i class="bi bi-journal-text"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <p class="small text-muted mb-1">Task aperti</p>
                    <p class="h3 mb-0 fw-bold" id="statTasksOpen">—</p>
                </div>
                <span class="icon-wrap bg-warning-subtle text-warning"><i class="bi bi-check2-square"></i></span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-tile h-100">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <p class="small text-muted mb-1">Eventi futuri</p>
                    <p class="h3 mb-0 fw-bold" id="statEventsUp">—</p>
                </div>
                <span class="icon-wrap bg-success-subtle text-success"><i class="bi bi-calendar2-week"></i></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card-app">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-sun me-2 text-warning"></i>Riepilogo di oggi</span>
                <span class="badge text-bg-light border" id="dashTodayBadge"></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h3 class="h6 text-muted text-uppercase small">Lezioni</h3>
                        <ul class="list-unstyled mb-0 small" id="dashLessonsToday"></ul>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6 text-muted text-uppercase small">Eventi</h3>
                        <ul class="list-unstyled mb-0 small" id="dashEventsToday"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-app h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-exclamation-octagon me-2 text-danger"></i>Task urgenti</span>
            </div>
            <div class="card-body" id="dashTasksUrgent"></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card-app h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-journal-bookmark me-2 text-primary"></i>Prossime lezioni</span>
                <a href="<?= h(base_url('pages/lessons.php')) ?>" class="small text-decoration-none">Vedi tutte</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-app mb-0 small align-middle">
                        <thead><tr><th>Titolo</th><th>Materia</th><th>Data</th><th>Stato</th></tr></thead>
                        <tbody id="dashLessonsUp"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-app h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-calendar-event me-2 text-info"></i>Prossimi eventi</span>
                <a href="<?= h(base_url('pages/events.php')) ?>" class="small text-decoration-none">Vedi tutti</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-app mb-0 small align-middle">
                        <thead><tr><th>Titolo</th><th>Categoria</th><th>Quando</th></tr></thead>
                        <tbody id="dashEventsUp"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-app mb-2">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <span class="fw-semibold"><i class="bi bi-calendar3 me-2"></i>Calendario (14 giorni)</span>
        <a href="<?= h(base_url('pages/calendar.php')) ?>" class="btn btn-sm btn-light border rounded-pill">Apri calendario</a>
    </div>
    <div class="card-body" id="dashMiniCal"></div>
</div>

<?php
$extra_scripts = '<script src="' . h(base_url('assets/js/dashboard.js')) . '"></script>';
include dirname(__DIR__) . '/components/footer.php';
