<?php
require_once __DIR__ . '/auth.php';
$page_title = $page_title ?? t('app_title');
$user = current_user();
$langs = $lang_options;
?>
<!DOCTYPE html>
<html lang="<?= e($current_lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> - <?= e(t('app_title')) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('assets/style.css')) ?>">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <div class="brand">
            <span class="brand-mark">SM</span>
            <span class="brand-text"><?= e(t('app_title')) ?></span>
        </div>
        <nav class="nav">
            <?php if (is_logged_in()): ?>
                <a href="<?= e(base_url('dashboard.php')) ?>"><?= e(t('nav_dashboard')) ?></a>
                <a href="<?= e(base_url('data.php')) ?>"><?= e(t('nav_data')) ?></a>
                <a href="<?= e(base_url('tambah.php')) ?>"><?= e(t('nav_add')) ?></a>
            <?php endif; ?>
        </nav>
        <div class="topbar-right">
            <form action="<?= e(base_url('bahasa.php')) ?>" method="get" class="lang-form">
                <label class="sr-only" for="lang"><?= e(t('nav_lang')) ?></label>
                <select name="lang" id="lang" onchange="this.form.submit()">
                    <?php foreach ($langs as $code => $label): ?>
                        <option value="<?= e($code) ?>" <?= $current_lang === $code ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <noscript><button type="submit">OK</button></noscript>
            </form>
            <?php if (is_logged_in()): ?>
                <span class="user-chip"><?= e($_SESSION['nama'] ?? $_SESSION['username']) ?></span>
                <a class="btn btn-sm btn-outline" href="<?= e(base_url('logout.php')) ?>"><?= e(t('nav_logout')) ?></a>
            <?php else: ?>
                <a class="btn btn-sm btn-primary" href="<?= e(base_url('login.php')) ?>"><?= e(t('nav_login')) ?></a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="container main">
<?php if ($error = flash('error')): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>
