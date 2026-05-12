<?php
/**
 * Eventi e impegni: lista unificata con priorità, reminder e completamento.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$current_page = 'events';
$page_title = 'Eventi';
$page_heading = 'Eventi e impegni';
$page_subtitle = 'Professionali, personali e scadenze in un\'unica vista';

include dirname(__DIR__) . '/components/header.php';
include dirname(__DIR__) . '/components/shell_start.php';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="d-flex flex-wrap gap-2 filter-chip">
        <select class="form-select form-select-sm border rounded-pill shadow-sm" id="filterEventShow" style="min-width: 10rem;">
            <option value="active">Solo attivi</option>
            <option value="done">Completati</option>
            <option value="all">Tutti</option>
        </select>
        <select class="form-select form-select-sm border rounded-pill shadow-sm" id="filterEventCategory" style="min-width: 12rem;">
            <option value="all">Tutte le categorie</option>
            <?php foreach (array_keys(EVENT_CATEGORIES) as $cat): ?>
                <option value="<?= h($cat) ?>"><?= h($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <div class="input-group input-group-sm shadow-sm" style="min-width: 14rem;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0" id="eventSearch" placeholder="Cerca titolo o descrizione..." autocomplete="off">
        </div>
    </div>
    <button type="button" class="btn btn-primary rounded-3 px-3" id="btnNewEventPage" data-bs-toggle="modal" data-bs-target="#modalEvent">
        <i class="bi bi-plus-lg me-1"></i> Nuovo evento
    </button>
</div>

<div class="card-app">
    <div class="table-responsive">
        <table class="table table-hover table-app mb-0 align-middle">
            <thead>
            <tr>
                <th style="width: 3rem;"></th>
                <th>Titolo</th>
                <th>Quando</th>
                <th>Categoria</th>
                <th>Priorità</th>
                <th>Reminder</th>
                <th class="text-end">Azioni</th>
            </tr>
            </thead>
            <tbody id="tblEvents"></tbody>
        </table>
    </div>
</div>

<?php
$extra_scripts = '<script src="' . h(base_url('assets/js/events.js')) . '"></script>';
include dirname(__DIR__) . '/components/footer.php';
