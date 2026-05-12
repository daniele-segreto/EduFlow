<?php
/**
 * Materie: cards responsive, CRUD e ricerca live via AJAX.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$current_page = 'subjects';
$page_title = 'Materie';
$page_heading = 'Materie';
$page_subtitle = 'Organizza insegnamenti e colori identificativi';

include dirname(__DIR__) . '/components/header.php';
include dirname(__DIR__) . '/components/shell_start.php';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="input-group shadow-sm" style="max-width: 22rem;">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0" id="subjectSearch" placeholder="Cerca per nome o descrizione..." autocomplete="off">
    </div>
    <button type="button" class="btn btn-primary rounded-3 px-3" id="btnNewSubject" data-bs-toggle="modal" data-bs-target="#modalSubject">
        <i class="bi bi-plus-lg me-1"></i> Nuova materia
    </button>
</div>

<div class="row g-3" id="subjectGrid"></div>

<?php
$extra_scripts = '<script src="' . h(base_url('assets/js/subjects.js')) . '"></script>';
include dirname(__DIR__) . '/components/footer.php';
