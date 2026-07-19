<?php
$errors = [];
$data = [
    'kode' => '',
    'nama' => '',
    'kategori' => '',
    'kondisi' => '',
    'supplier' => '',
    'jumlah' => '',
    'tanggal' => '',
    'lokasi' => '',
    'keterangan' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $value) {
        $data[$key] = trim($_POST[$key] ?? '');
    }

    if ($data['kode'] === '') $errors[] = 'Kode barang wajib diisi.';
    if ($data['nama'] === '') $errors[] = 'Nama barang wajib diisi.';
    if ($data['kategori'] === '') $errors[] = 'Kategori wajib dipilih.';
    if ($data['kondisi'] === '') $errors[] = 'Kondisi barang wajib dipilih.';
    if ($data['supplier'] === '') $errors[] = 'Supplier wajib diisi.';
    if ($data['jumlah'] === '' || !ctype_digit($data['jumlah']) || (int)$data['jumlah'] < 1) {
        $errors[] = 'Jumlah harus berupa bilangan bulat minimal 1.';
    }
    if ($data['tanggal'] === '') $errors[] = 'Tanggal masuk wajib diisi.';
    if ($data['lokasi'] === '') $errors[] = 'Lokasi penyimpanan wajib dipilih.';
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendataan Barang Gudang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="page-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">GK</div>
            <div>
                <h1>GudangKu</h1>
                <p>Inventory System</p>
            </div>
        </div>
        <div class="identity">
            <strong>Muhammad Ridwan Kusumahani</strong>
            <span>2500018053 · Kelas A</span>
        </div>
        <nav>
            <a class="active" href="#">Form Barang</a>
            <a href="#hasil">Hasil Input</a>
        </nav>
    </aside>

    <main class="content">
        <section class="heading">
            <span class="eyebrow">PWeb-12.2 · Penanganan FORM di PHP</span>
            <h2>Form Pendataan Barang Gudang</h2>
            <p>Isi data barang secara lengkap. Data akan diproses menggunakan metode POST pada PHP.</p>
        </section>

        <?php if ($errors): ?>
            <div class="alert error">
                <strong>Data belum dapat diproses:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="card">
            <form method="post" action="">
                <div class="form-grid">
                    <div class="field">
                        <label for="kode">Kode Barang</label>
                        <input type="text" id="kode" name="kode" placeholder="Contoh: BRG-001" value="<?= e($data['kode']) ?>">
                    </div>

                    <div class="field">
                        <label for="nama">Nama Barang</label>
                        <input type="text" id="nama" name="nama" placeholder="Contoh: Mouse Wireless" value="<?= e($data['nama']) ?>">
                    </div>

                    <div class="field">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori">
                            <option value="">-- Pilih kategori --</option>
                            <?php foreach (['Elektronik', 'Alat Tulis', 'Perlengkapan Packing', 'Perabot', 'Lainnya'] as $item): ?>
                                <option value="<?= e($item) ?>" <?= $data['kategori'] === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="supplier">Supplier</label>
                        <input type="text" id="supplier" name="supplier" placeholder="Nama supplier" value="<?= e($data['supplier']) ?>">
                    </div>

                    <div class="field">
                        <label for="jumlah">Jumlah Barang</label>
                        <input type="number" min="1" id="jumlah" name="jumlah" placeholder="Contoh: 20" value="<?= e($data['jumlah']) ?>">
                    </div>

                    <div class="field">
                        <label for="tanggal">Tanggal Masuk</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= e($data['tanggal']) ?>">
                    </div>

                    <div class="field full">
                        <label>Kondisi Barang</label>
                        <div class="radio-group">
                            <?php foreach (['Baru', 'Baik', 'Perlu Pemeriksaan'] as $item): ?>
                                <label class="radio-option">
                                    <input type="radio" name="kondisi" value="<?= e($item) ?>" <?= $data['kondisi'] === $item ? 'checked' : '' ?>>
                                    <span><?= e($item) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="field">
                        <label for="lokasi">Lokasi Penyimpanan</label>
                        <select id="lokasi" name="lokasi">
                            <option value="">-- Pilih lokasi --</option>
                            <?php foreach (['Rak A', 'Rak B', 'Rak C', 'Gudang Utama'] as $item): ?>
                                <option value="<?= e($item) ?>" <?= $data['lokasi'] === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" placeholder="Tambahkan catatan bila diperlukan"><?= e($data['keterangan']) ?></textarea>
                    </div>
                </div>

                <div class="actions">
                    <button type="reset" class="button secondary">Reset</button>
                    <button type="submit" class="button primary">Simpan Data</button>
                </div>
            </form>
        </section>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errors): ?>
            <section class="card result" id="hasil">
                <div class="result-header">
                    <div>
                        <span class="eyebrow">Data berhasil diproses</span>
                        <h3>Hasil Input Barang</h3>
                    </div>
                    <span class="status">Berhasil</span>
                </div>
                <table>
                    <tbody>
                        <tr><th>Kode Barang</th><td><?= e($data['kode']) ?></td></tr>
                        <tr><th>Nama Barang</th><td><?= e($data['nama']) ?></td></tr>
                        <tr><th>Kategori</th><td><?= e($data['kategori']) ?></td></tr>
                        <tr><th>Kondisi</th><td><?= e($data['kondisi']) ?></td></tr>
                        <tr><th>Supplier</th><td><?= e($data['supplier']) ?></td></tr>
                        <tr><th>Jumlah</th><td><?= e($data['jumlah']) ?> unit</td></tr>
                        <tr><th>Tanggal Masuk</th><td><?= e($data['tanggal']) ?></td></tr>
                        <tr><th>Lokasi</th><td><?= e($data['lokasi']) ?></td></tr>
                        <tr><th>Keterangan</th><td><?= e($data['keterangan'] !== '' ? $data['keterangan'] : '-') ?></td></tr>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
