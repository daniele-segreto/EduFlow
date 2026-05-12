<?php
/**
 * Navigazione principale. Desktop: sidebar fissa. Mobile: offcanvas.
 * Variabile: $current_page — identificativo voce attiva (es. dashboard, calendar).
 */
declare(strict_types=1);
$current_page = $current_page ?? '';
$nav = [
    'dashboard' => ['label' => 'Home', 'icon' => 'bi-speedometer2', 'href' => base_url('pages/dashboard.php')],
    'calendar'  => ['label' => 'Calendario', 'icon' => 'bi-calendar3', 'href' => base_url('pages/calendar.php')],
    'subjects'  => ['label' => 'Materie', 'icon' => 'bi-bookmarks', 'href' => base_url('pages/subjects.php')],
    'lessons'   => ['label' => 'Lezioni', 'icon' => 'bi-journal-text', 'href' => base_url('pages/lessons.php')],
    'events'    => ['label' => 'Eventi e impegni', 'icon' => 'bi-calendar-event', 'href' => base_url('pages/events.php')],
];
?>
<aside class="offcanvas-lg offcanvas-start app-sidebar border-end bg-white shadow-sm" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel">
    <div class="offcanvas-header border-bottom d-lg-none">
        <div class="d-flex align-items-center gap-2" id="appSidebarLabel">
            <span class="brand-dot rounded-circle bg-primary"></span>
            <span class="fw-semibold"><?= h(APP_NAME) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#appSidebar" aria-label="Chiudi"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="p-4 d-none d-lg-block border-bottom">
            <a href="<?= h(base_url('pages/dashboard.php')) ?>" class="text-decoration-none text-body d-flex align-items-center gap-2">
                <span class="brand-dot rounded-circle bg-primary"></span>
                <span class="fw-bold fs-5 tracking-tight"><?= h(APP_NAME) ?></span>
            </a>
            <p class="small text-muted mb-0 mt-2">Organizza lezioni, calendario e task in un unico posto.</p>
        </div>
        <nav class="nav flex-column gap-1 px-3 py-3 flex-grow-1">
            <?php foreach ($nav as $key => $item): ?>
                <?php
                $active = $current_page === $key;
                $cls = 'nav-link-app d-flex align-items-center gap-3 rounded-3 px-3 py-2' . ($active ? ' active' : '');
                ?>
                <a class="<?= h($cls) ?>" href="<?= h($item['href']) ?>">
                    <i class="bi <?= h($item['icon']) ?> fs-5"></i>
                    <span><?= h($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3 mt-auto border-top small text-muted">
            <i class="bi bi-shield-check me-1"></i> Sessione protetta · PHP + MySQL
        </div>
    </div>
</aside>
