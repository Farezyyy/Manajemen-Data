<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$search = trim($_GET['search'] ?? '');
$kategoriId = $_GET['kategori'] ?? '';

$where = '1=1';
$params = [];

if ($search !== '') {
    $where .= ' AND (p.kode LIKE ? OR p.nama LIKE ? OR p.deskripsi LIKE ?)';
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
if ($kategoriId !== '') {
    $where .= ' AND p.kategori_id = ?';
    $params[] = (int)$kategoriId;
}

$total = (int) fetch_one("SELECT COUNT(*) AS c FROM produk p WHERE $where", $params)['c'];
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$produk = fetch_all(
    "SELECT p.*, k.nama AS kategori_nama FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id WHERE $where ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
);
$kategoriList = fetch_all("SELECT id, nama FROM kategori ORDER BY nama");

$page_title = t('nav_data');
require __DIR__ . '/includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem">
    <h2 style="font-size:1.25rem;color:var(--primary)"><?= e(t('list_title')) ?></h2>
    <a href="<?= e(base_url('tambah.php')) ?>" class="btn btn-primary"><?= e(t('nav_add')) ?></a>
</div>

<form method="get" class="card" style="margin-bottom:1.5rem;padding:1rem">
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
        <div style="flex:1;min-width:200px">
            <input type="search" name="search" placeholder="<?= e(t('search')) ?>" value="<?= e($search) ?>" style="width:100%;padding:.5rem .7rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem">
        </div>
        <div style="min-width:180px">
            <select name="kategori" style="width:100%;padding:.5rem .7rem;border:1px solid #cbd5e1;border-radius:6px;font-size:.9rem">
                <option value=""><?= e(t('filter_kategori')) ?></option>
                <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= e($k['id']) ?>" <?= $kategoriId == $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><?= e(t('filter')) ?></button>
        <?php if ($search || $kategoriId): ?>
            <a href="<?= e(base_url('data.php')) ?>" class="btn btn-outline"><?= e(t('reset')) ?></a>
        <?php endif; ?>
    </div>
</form>

<div class="card" style="overflow-x:auto">
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
                <th><?= e(t('th_aksi')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($produk): $i = $offset; foreach ($produk as $p): $i++; ?>
            <tr>
                <td><?= e($i) ?></td>
                <td><?= e($p['kode']) ?></td>
                <td><?= e($p['nama']) ?></td>
                <td><?= e($p['kategori_nama'] ?? t('no_kategori')) ?></td>
                <td>Rp <?= e(number_format($p['harga'], 0, ',', '.')) ?></td>
                <td><?= e(number_format($p['stok'], 0, '', '.')) ?></td>
                <td><?= e(date('d M Y', strtotime($p['created_at']))) ?></td>
                <td>
                    <a href="<?= e(base_url('edit.php?id=' . $p['id'])) ?>"><?= e(t('edit')) ?></a>
                    <a href="<?= e(base_url('hapus.php?id=' . $p['id'])) ?>" class="danger" onclick="return confirm('<?= e(t('confirm_delete')) ?>')"><?= e(t('delete')) ?></a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="8" style="text-align:center"><?= e(t('empty')) ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<nav style="display:flex;justify-content:center;gap:.5rem;margin-top:1rem">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&kategori=<?= urlencode($kategoriId) ?>" class="btn btn-sm btn-outline"><?= e(t('prev')) ?></a>
    <?php endif; ?>
    <span style="display:flex;align-items:center;padding:0 .5rem;color:#475569">
        <?= e(t('showing')) ?> <?= e($offset+1) ?>-<?= e(min($offset+$perPage, $total)) ?> <?= e(t('of')) ?> <?= e($total) ?> <?= e(t('data')) ?>
    </span>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&kategori=<?= urlencode($kategoriId) ?>" class="btn btn-sm btn-outline"><?= e(t('next')) ?></a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
