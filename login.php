<?php
/**
 * Pagina di accesso (layout minimale, login via AJAX).
 */
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

if (current_user_id() !== null) {
    header('Location: ' . base_url('pages/dashboard.php'));
    exit;
}

$page_title = 'Accedi';
$assetBase = base_url('assets');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($page_title) ?> · <?= h(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= h($assetBase) ?>/css/app.css">
</head>
<body class="app-body d-flex align-items-center py-5">
<div class="container" style="max-width: 420px;">
    <div class="card-app p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="brand-dot bg-primary d-inline-block mb-2"></span>
            <h1 class="h3 fw-bold mb-1"><?= h(APP_NAME) ?></h1>
            <p class="text-muted small mb-0">Dashboard per insegnanti e formatori</p>
        </div>
        <form id="formLogin" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="login_email" class="form-label small fw-medium">Email</label>
                <input type="email" class="form-control form-control-lg" id="login_email" name="email" required autocomplete="username" value="demo@eduflow.local">
            </div>
            <div class="mb-3">
                <label for="login_password" class="form-label small fw-medium">Password</label>
                <input type="password" class="form-control form-control-lg" id="login_password" name="password" required autocomplete="current-password" placeholder="demo123">
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3" id="btnLogin">Entra</button>
        </form>
        <p class="small text-muted text-center mt-4 mb-0">Dopo l'import di <code>database/schema.sql</code> usa l'utente demo indicato nel README.</p>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    window.APP_BASE = <?= json_encode(base_url(''), JSON_THROW_ON_ERROR) ?>;
</script>
<script src="<?= h($assetBase) ?>/js/app.js"></script>
<script>
(function ($) {
    $('#formLogin').on('submit', function (e) {
        e.preventDefault();
        var payload = { email: $('#login_email').val(), password: $('#login_password').val() };
        $('#btnLogin').prop('disabled', true);
        App.ajaxJson({ url: App.url('ajax/login.php'), data: payload })
            .done(function (res) {
                if (res.success && res.redirect) {
                    window.location.href = res.redirect;
                } else {
                    App.toast(res.message || 'Accesso negato', 'danger');
                }
            })
            .fail(function (xhr) {
                var msg = 'Errore di rete';
                try {
                    var j = JSON.parse(xhr.responseText);
                    if (j.message) msg = j.message;
                } catch (e) {}
                App.toast(msg, 'danger');
            })
            .always(function () {
                $('#btnLogin').prop('disabled', false);
            });
    });
})(jQuery);
</script>
</body>
</html>
