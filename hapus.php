<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', t('not_found'));
    redirect(base_url('data.php'));
}

require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        flash('error', 'Sesi tidak valid, silakan coba lagi.');
        redirect(base_url('data.php'));
    }

    query("DELETE FROM produk WHERE id = ?", [$id]);
    flash('success', t('deleted_ok'));
    redirect(base_url('data.php'));
}

$produk = fetch_one("SELECT * FROM produk WHERE id = ? LIMIT 1", [$id]);
if (!$produk) {
    flash('error', t('not_found'));
    redirect(base_url('data.php'));
}

$page_title = t('delete');
require __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width:500px;margin:2rem auto;padding:2rem;text-align:center">
    <h2 style="color:var(--primary)"><?= e(t('delete')) ?></h2>
    <p style="margin:1rem 0;font-size:1.1rem">Yakin ingin menghapus data ini?</p>
    <p style="margin:0.5rem 0;color:var(--danger);font-weight:600">
        <?= e($produk['nama']) ?>
    </p>
    <p style="color:var(--danger)"><?= e(t('confirm_delete')) ?></p>
    <form method="post" action="<?= e(base_url('hapus.php?id=' . $id)) ?>" class="card" style="display:inline-block">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger"><?= e(t('delete')) ?></button>
        <a href="<?= e(base_url('data.php')) ?>" class="btn btn-outline"><?= e(t('cancel')) ?></a>
    </form>
</div>

<?php
require __DIR__ . '/includes/footer.php';
