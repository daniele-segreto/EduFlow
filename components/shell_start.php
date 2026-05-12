<?php
/**
 * Avvolgimento layout: sidebar + colonna principale + apertura <main>.
 * Includere subito dopo header.php; footer.php chiude i tag.
 */
declare(strict_types=1);
?>
<div class="app-wrapper d-flex min-vh-100 w-100">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="app-column flex-grow-1 d-flex flex-column min-vw-0">
        <?php include __DIR__ . '/navbar.php'; ?>
        <main class="app-main flex-grow-1 p-3 p-md-4">
