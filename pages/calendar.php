<?php
/**
 * Calendario: viste mese / settimana / giorno, filtri, modale evento.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_auth();

$current_page = 'calendar';
$page_title = 'Calendario';
$page_heading = 'Calendario';
$page_subtitle = 'Viste mese, settimana e giorno con colori per categoria';

include dirname(__DIR__) . '/components/header.php';
include dirname(__DIR__) . '/components/shell_start.php';
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div class="btn-group shadow-sm" role="group" aria-label="Vista calendario">
        <button type="button" class="btn btn-light border active" data-cal-view="month">Mese</button>
        <button type="button" class="btn btn-light border" data-cal-view="week">Settimana</button>
        <button type="button" class="btn btn-light border" data-cal-view="day">Giorno</button>
    </div>
    <div class="btn-group shadow-sm">
        <button type="button" class="btn btn-light border" id="btnCalPrev" aria-label="Precedente"><i class="bi bi-chevron-left"></i></button>
        <button type="button" class="btn btn-light border" id="btnCalToday">Oggi</button>
        <button type="button" class="btn btn-light border" id="btnCalNext" aria-label="Successivo"><i class="bi bi-chevron-right"></i></button>
    </div>
    <div class="d-flex flex-wrap gap-2 filter-chip">
        <select class="form-select form-select-sm border rounded-pill" id="calFilterCategory" style="min-width: 11rem;">
            <option value="all">Tutte le categorie</option>
            <?php foreach (array_keys(EVENT_CATEGORIES) as $cat): ?>
                <option value="<?= h($cat) ?>"><?= h($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select form-select-sm border rounded-pill" id="calFilterType" style="min-width: 9rem;">
            <option value="all">Tutti i tipi</option>
            <option value="event">Solo eventi</option>
            <option value="lesson">Solo lezioni</option>
        </select>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btnCalNewEvent" data-bs-toggle="modal" data-bs-target="#modalEvent">
            <i class="bi bi-plus-lg me-1"></i> Evento
        </button>
    </div>
</div>

<div class="card-app mb-3">
    <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h2 class="h5 mb-0 fw-semibold" id="calTitle">Calendario</h2>
        <span class="small text-muted" id="calRangeHint"></span>
    </div>
</div>

<div id="calBody" class="calendar-root"></div>

<?php
$extra_scripts = '<script src="' . h(base_url('assets/js/calendar.js')) . '"></script>';
include dirname(__DIR__) . '/components/footer.php';
