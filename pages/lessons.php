<?php
/**
 * Lezioni: tabella con filtri, CRUD e dettaglio in modale.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$current_page = 'lessons';
$page_title = 'Lezioni';
$page_heading = 'Lezioni';
$page_subtitle = 'Collega ogni lezione a una materia e monitora lo stato';

include dirname(__DIR__) . '/components/header.php';
include dirname(__DIR__) . '/components/shell_start.php';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="d-flex flex-wrap gap-2">
        <select class="form-select form-select-sm border rounded-pill shadow-sm" id="filterLessonSubject" style="min-width: 12rem;">
            <option value="0">Tutte le materie</option>
        </select>
        <select class="form-select form-select-sm border rounded-pill shadow-sm" id="filterLessonStatus" style="min-width: 10rem;">
            <option value="all">Tutti gli stati</option>
            <option value="scheduled">Programmate</option>
            <option value="completed">Completate</option>
            <option value="cancelled">Annullate</option>
        </select>
        <div class="input-group input-group-sm shadow-sm" style="min-width: 14rem;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0" id="lessonSearch" placeholder="Cerca..." autocomplete="off">
        </div>
    </div>
    <button type="button" class="btn btn-primary rounded-3 px-3" id="btnNewLesson" data-bs-toggle="modal" data-bs-target="#modalLesson">
        <i class="bi bi-plus-lg me-1"></i> Nuova lezione
    </button>
</div>

<div class="card-app">
    <div class="table-responsive">
        <table class="table table-hover table-app mb-0 align-middle">
            <thead>
            <tr>
                <th>Titolo</th>
                <th>Materia</th>
                <th>Data</th>
                <th>Orario</th>
                <th>Stato</th>
                <th class="text-end">Azioni</th>
            </tr>
            </thead>
            <tbody id="lessonTable"></tbody>
        </table>
    </div>
</div>

<?php
$extra_scripts = '<script src="' . h(base_url('assets/js/lessons.js')) . '"></script>';
include dirname(__DIR__) . '/components/footer.php';
