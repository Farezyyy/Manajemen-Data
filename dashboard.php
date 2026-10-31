<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$totalProduk = (int) fetch_one("SELECT COUNT(*) AS c FROM produk")['c'];
$totalKategori = (int) fetch_one("SELECT COUNT(*) AS c FROM kategori")['c'];
$totalUser = (int) fetch_one("SELECT COUNT(*) AS c FROM users")['c'];
$totalStok = (int) fetch_one("SELECT COALESCE(SUM(stok),0) AS c FROM produk")['c'];
$nilaiTotal = (float) fetch_one("SELECT COALESCE(SUM(harga*stok),0) AS c FROM produk")['c'];
$terbaru = fetch_all("SELECT p.*, k.nama AS kategori_nama FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id ORDER BY p.created_at DESC LIMIT 5");

$page_title = t('nav_dashboard');
require __DIR__ . '/includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= e(number_format($totalProduk)) ?></div>
        <div class="stat-label"><?= e(t('total_data')) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= e(number_format($totalKategori)) ?></div>
        <div class="stat-label"><?= e(t('total_kategori')) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= e(number_format($totalUser)) ?></div>
        <div class="stat-label"><?= e(t('total_user')) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= e(number_format($totalStok)) ?></div>
        <div class="stat-label"><?= e(t('total_stok')) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-number">Rp <?= e(number_format($nilaiTotal, 0, ',', '.')) ?></div>
        <div class="stat-label"><?= e(t('nilai_total')) ?></div>
    </div>
</div>

<div class="card">
    <h2><?= e(t('terbaru')) ?></h2>
    <div style="overflow-x:auto">
        <table class="table">
            <thead>
                <tr>
                    <th><?= e(t('th_no')) ?></th>
                    <th><?= e(t('th_kode')) ?></th>
                    <th><?= e(t('th_nama')) ?></th>
                    <th><?= e(t('th_kategori')) ?></th>
                    <th><?= e(t('th_harga')) ?></th>
                    <th><?= e(t('th_stok')) ?></th>
                    <th><?= e(t('th_tanggal')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($terbaru): $i=0; foreach ($terbaru as $p): $i++; ?>
                <tr>
                    <td><?= e($i) ?></td>
                    <td><?= e($p['kode']) ?></td>
                    <td><?= e($p['nama']) ?></td>
                    <td><?= e($p['kategori_nama'] ?? t('no_kategori')) ?></td>
                    <td>Rp <?= e(number_format($p['harga'], 0, ',', '.')) ?></td>
                    <td><?= e(number_format($p['stok'], 0, '', '.')) ?></td>
                    <td><?= e(date('d M Y', strtotime($p['created_at']))) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="7" style="text-align:center"><?= e(t('empty')) ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align:right;margin-top:.5rem">
        <a href="<?= e(base_url('data.php')) ?>" class="btn btn-sm btn-primary"><?= e(t('nav_data')) ?></a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
