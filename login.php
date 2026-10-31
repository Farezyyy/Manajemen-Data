<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(base_url('dashboard.php'));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors[] = 'Sesi tidak valid, silakan coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $errors[] = 'Username dan password wajib diisi.';
        } elseif (attempt_login($username, $password)) {
            flash('success', 'Login berhasil.');
            redirect(base_url('dashboard.php'));
        } else {
            $errors[] = t('login_error');
        }
    }
}

$page_title = t('login_title');
require __DIR__ . '/includes/header.php';
?>

<div class="login-wrap">
    <div class="login-card">
        <h1 class="login-title"><?= e(t('login_title')) ?></h1>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" action="<?= e(base_url('login.php')) ?>" class="form">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username"><?= e(t('login_username')) ?></label>
                <input type="text" id="username" name="username" value="<?= e(old('username')) ?>"
                       required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password"><?= e(t('login_password')) ?></label>
                <input type="password" id="password" name="password"
                       required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><?= e(t('login_button')) ?></button>
        </form>
    </div>
</div>

<?php
clear_old();
require __DIR__ . '/includes/footer.php';
