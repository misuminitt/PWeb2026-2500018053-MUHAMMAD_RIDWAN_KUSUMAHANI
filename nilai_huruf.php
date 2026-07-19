<?php
$nilai = 85;
$hasil = null;
$pesan = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $input = filter_input(INPUT_POST, 'nilai', FILTER_VALIDATE_FLOAT);
    if ($input === false || $input === null) {
        $pesan = 'Nilai harus berupa angka.';
    } elseif ($input < 0 || $input > 100) {
        $pesan = 'Nilai harus berada pada rentang 0 sampai 100.';
    } else {
        $nilai = $input;
    }
}

if ($pesan === '') {
    if ($nilai >= 80 && $nilai <= 100) {
        $hasil = 'A';
    } elseif ($nilai >= 65) {
        $hasil = 'B';
    } elseif ($nilai >= 50) {
        $hasil = 'C';
    } elseif ($nilai >= 25) {
        $hasil = 'D';
    } else {
        $hasil = 'E';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konversi Nilai Angka ke Huruf</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }
        .card {
            width: min(92%, 620px);
            background: #ffffff;
            border-radius: 18px;
            padding: 34px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
        }
        h1 { margin: 0 0 8px; font-size: 30px; }
        .subtitle { color: #64748b; margin-bottom: 26px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        .form-row { display: flex; gap: 10px; }
        input {
            flex: 1;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 16px;
        }
        button {
            border: 0;
            border-radius: 10px;
            padding: 13px 20px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .result {
            margin-top: 24px;
            padding: 20px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }
        .grade {
            display: inline-grid;
            place-items: center;
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: #1d4ed8;
            color: #fff;
            font-size: 32px;
            font-weight: 800;
            margin-right: 14px;
            vertical-align: middle;
        }
        .error { margin-top: 20px; color: #b91c1c; font-weight: 700; }
        table { width: 100%; margin-top: 24px; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; }
        .identity { margin-top: 20px; color: #64748b; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
<main class="card">
    <h1>Konversi Nilai Angka ke Huruf</h1>
    <div class="subtitle">Program PHP menggunakan percabangan if, elseif, dan else.</div>

    <form method="post">
        <label for="nilai">Masukkan nilai (0-100)</label>
        <div class="form-row">
            <input type="number" id="nilai" name="nilai" min="0" max="100" step="0.01" value="<?= htmlspecialchars((string)$nilai) ?>" required>
            <button type="submit">Proses</button>
        </div>
    </form>

    <?php if ($pesan !== ''): ?>
        <div class="error"><?= htmlspecialchars($pesan) ?></div>
    <?php else: ?>
        <section class="result">
            <span class="grade"><?= $hasil ?></span>
            <strong>Nilai <?= htmlspecialchars((string)$nilai) ?> memperoleh nilai huruf <?= $hasil ?>.</strong>
        </section>
    <?php endif; ?>

    <table>
        <thead><tr><th>Nilai Huruf</th><th>Rentang Nilai Angka</th></tr></thead>
        <tbody>
            <tr><td>A</td><td>80-100</td></tr>
            <tr><td>B</td><td>65-&lt;80</td></tr>
            <tr><td>C</td><td>50-&lt;65</td></tr>
            <tr><td>D</td><td>25-&lt;50</td></tr>
            <tr><td>E</td><td>0-&lt;25</td></tr>
        </tbody>
    </table>
    <div class="identity">Muhammad Ridwan Kusumahani · 2500018053 · Kelas A</div>
</main>
</body>
</html>
