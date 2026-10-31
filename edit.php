<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('error', t('not_found'));
    redirect(base_url('data.php'));
}

$produk = fetch_one("SELECT * FROM produk WHERE id = ? LIMIT 1", [$id]);
if (!$produk) {
    flash('error', t('not_found'));
    redirect(base_url('data.php'));
}

$errors = [];
$page_title = t('edit_title');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $errors[] = 'Sesi tidak valid, silakan coba lagi.';
    } else {
        $kode = trim($_POST['kode'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kategoriId = $_POST['kategori_id'] !== '' ? (int)$_POST['kategori_id'] : null;
        $harga = trim($_POST['harga'] ?? '');
        $stok = trim($_POST['stok'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        flash_old(['kode' => $kode, 'nama' => $nama, 'kategori_id' => $kategoriId ?? '', 'harga' => $harga, 'stok' => $stok, 'deskripsi' => $deskripsi]);

        if ($kode === '')        $errors[] = t('required') . ' (' . t('label_kode') . ')';
        if ($nama === '')        $errors[] = t('required') . ' (' . t('label_nama') . ')';
        if ($harga !== '' && !is_numeric($harga)) $errors[] = t('invalid_number') . ' (' . t('label_harga') . ')';
        if ($stok !== '' && !is_numeric($stok))   $errors[] = t('invalid_number') . ' (' . t('label_stok') . ')';

        // Cek duplikat kode (kecuali untuk product yang sedang diedit)
        if ($kode !== '' && fetch_one("SELECT id FROM produk WHERE kode = ? AND id <> ? LIMIT 1", [$kode, $id])) {
            $errors[] = t('kode_exists');
        }

        if (empty($errors)) {
            query(
                "UPDATE produk SET kode=?,nama=?,kategori_id=?,harga=?,stok=?,deskripsi=?,updated_at=NOW() WHERE id=?",
                [$kode, $nama, $kategoriId, (float)$harga, (int)($stok !== '' ? $stok : 0), $deskripsi, $id]
            );
            clear_old();
            flash('success', t('updated_ok'));
            redirect(base_url('data.php'));
        }
    }
}

$kategoriList = fetch_all("SELECT id, nama FROM kategori ORDER BY nama");
require __DIR__ . '/includes/header.php';
?>

<h2 style="color:var(--primary)"><?= e(t('edit_title')) ?></h2>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= e($err) ?></div>
<?php endforeach; ?>

<form method="post" action="<?= e(base_url('edit.php?id=' . $id)) ?>" class="card" style="padding:1.5rem">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="kode"><?= e(t('label_kode')) ?> *</label>
        <input type="text" id="kode" name="kode" value="<?= e($produk['kode']) ?>" required>
    </div>

    <div class="form-group">
        <label for="nama"><?= e(t('label_nama')) ?> *</label>
        <input type="text" id="nama" name="nama" value="<?= e($produk['nama']) ?>" required>
    </div>

    <div class="form-group">
        <label for="kategori_id"><?= e(t('label_kategori')) ?></label>
        <select id="kategori_id" name="kategori_id">
            <option value=""><?= e(t('no_kategori')) ?></option>
            <?php foreach ($kategoriList as $k): ?>
                <option value="<?= e($k['id']) ?>" <?= $produk['kategori_id'] == $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="harga"><?= e(t('label_harga')) ?> *</label>
        <input type="text" id="harga" name="harga" value="<?= e($produk['harga']) ?>" required>
    </div>

    <div class="form-group">
        <label for="stok"><?= e(t('label_stok')) ?></label>
        <input type="number" id="stok" name="stok" value="<?= e($produk['stok']) ?>">
    </div>

    <div class="form-group">
        <label for="deskripsi"><?= e(t('label_deskripsi')) ?></label>
        <textarea id="deskripsi" name="deskripsi" rows="3"><?= e($produk['deskripsi']) ?></textarea>
    </div>

    <div style="display:flex;gap:.5rem;justify-content:flex-end">
        <a href="<?= e(base_url('data.php')) ?>" class="btn btn-outline"><?= e(t('cancel')) ?></a>
        <button type="submit" class="btn btn-primary"><?= e(t('update')) ?></button>
    </div>
</form>

<?php
clear_old();
require __DIR__ . '/includes/footer.php';
