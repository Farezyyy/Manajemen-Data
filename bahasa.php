<?php
require_once __DIR__ . '/includes/auth.php';
$lang = $_GET['lang'] ?? 'id';
if (isset($lang_options[$lang])) {
    $_SESSION['lang'] = $lang;
}
$ref = $_SERVER['HTTP_REFERER'] ?? base_url('dashboard.php');
redirect($ref);
