<?php
/**
 * Chiusura layout, modali condivise, script JS (Bootstrap + jQuery + pagina).
 * Opzionale: $extra_scripts (string HTML) per script inline aggiuntivi.
 */
declare(strict_types=1);
$assetBase = base_url('assets');
$extra_scripts = $extra_scripts ?? '';
?>
        </main>
    </div>
</div>

<?php include __DIR__ . '/modals.php'; ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="appToastContainer"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    window.APP_BASE = <?= json_encode(base_url(''), JSON_THROW_ON_ERROR) ?>;
</script>
<script src="<?= h($assetBase) ?>/js/app.js"></script>
<?= $extra_scripts ?>
</body>
</html>
