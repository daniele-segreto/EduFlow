<?php
/**
 * Barra superiore: titolo sezione, azioni rapide, menu utente.
 * Variabili: $page_heading (string), opzionale $page_subtitle
 */
declare(strict_types=1);
$page_heading = $page_heading ?? 'Dashboard';
$page_subtitle = $page_subtitle ?? '';
$user = current_user();
?>
<header class="app-navbar border-bottom bg-white shadow-sm">
    <div class="container-fluid py-2 py-md-3 px-3 px-lg-4 d-flex align-items-center gap-2">
        <button class="btn btn-light btn-icon d-lg-none border" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Apri menu">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="flex-grow-1 min-w-0">
            <h1 class="h5 mb-0 fw-semibold text-truncate"><?= h($page_heading) ?></h1>
            <?php if ($page_subtitle !== ''): ?>
                <p class="small text-muted mb-0 text-truncate"><?= h($page_subtitle) ?></p>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted d-none d-sm-inline"><?= h(date('d/m/Y')) ?></span>
            <div class="dropdown">
                <button class="btn btn-light border rounded-pill px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar-sm bg-primary-subtle text-primary fw-semibold rounded-circle d-inline-flex align-items-center justify-content-center">
                        <?= h(mb_strtoupper(mb_substr($user['name'] ?? 'U', 0, 1))) ?>
                    </span>
                    <span class="d-none d-md-inline small fw-medium text-truncate" style="max-width: 8rem;"><?= h($user['name'] ?? 'Utente') ?></span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><span class="dropdown-item-text small text-muted"><?= h($user['email'] ?? '') ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= h(base_url('ajax/logout.php')) ?>"><i class="bi bi-box-arrow-right me-2"></i>Esci</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
