<?php
declare(strict_types=1);
session_start();

/** Membersihkan input teks dari karakter berbahaya. */
function cleanText(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/** Menghitung nilai akhir berdasarkan bobot tugas, UTS, dan UAS. */
function calculateFinalScore(float $assignment, float $midterm, float $finalExam): float
{
    return round(($assignment * 0.30) + ($midterm * 0.30) + ($finalExam * 0.40), 2);
}

/** Menentukan nilai huruf dari nilai akhir. */
function determineGrade(float $score): string
{
    return match (true) {
        $score >= 85 => 'A',
        $score >= 75 => 'B',
        $score >= 65 => 'C',
        $score >= 50 => 'D',
        default => 'E',
    };
}

/** Menentukan status kelulusan. */
function determineStatus(float $score): string
{
    return $score >= 65 ? 'Lulus' : 'Tidak Lulus';
}

/** Menghitung ringkasan data mahasiswa. */
function buildSummary(array $students): array
{
    if ($students === []) {
        return ['count' => 0, 'average' => 0, 'highest' => 0, 'passed' => 0];
    }

    $scores = array_column($students, 'final_score');
    $passed = array_filter($students, fn(array $student): bool => $student['status'] === 'Lulus');

    return [
        'count' => count($students),
        'average' => round(array_sum($scores) / count($scores), 2),
        'highest' => max($scores),
        'passed' => count($passed),
    ];
}

$defaultStudents = [
    ['nim' => '2026001', 'name' => 'Alya Putri', 'assignment' => 88, 'midterm' => 82, 'final_exam' => 90],
    ['nim' => '2026002', 'name' => 'Bagas Pratama', 'assignment' => 76, 'midterm' => 72, 'final_exam' => 78],
    ['nim' => '2026003', 'name' => 'Citra Lestari', 'assignment' => 64, 'midterm' => 60, 'final_exam' => 68],
    ['nim' => '2026004', 'name' => 'Dimas Saputra', 'assignment' => 55, 'midterm' => 48, 'final_exam' => 52],
];

if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [];
    foreach ($defaultStudents as $student) {
        $score = calculateFinalScore($student['assignment'], $student['midterm'], $student['final_exam']);
        $_SESSION['students'][] = $student + [
            'final_score' => $score,
            'grade' => determineGrade($score),
            'status' => determineStatus($score),
        ];
    }
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'reset') {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $nim = cleanText((string) ($_POST['nim'] ?? ''));
    $name = cleanText((string) ($_POST['name'] ?? ''));
    $assignment = filter_input(INPUT_POST, 'assignment', FILTER_VALIDATE_FLOAT);
    $midterm = filter_input(INPUT_POST, 'midterm', FILTER_VALIDATE_FLOAT);
    $finalExam = filter_input(INPUT_POST, 'final_exam', FILTER_VALIDATE_FLOAT);

    if ($nim === '' || $name === '') {
        $errors[] = 'NIM dan nama mahasiswa wajib diisi.';
    }

    foreach (['Nilai tugas' => $assignment, 'Nilai UTS' => $midterm, 'Nilai UAS' => $finalExam] as $label => $value) {
        if ($value === false || $value < 0 || $value > 100) {
            $errors[] = $label . ' harus berupa angka 0–100.';
        }
    }

    $nimExists = array_filter(
        $_SESSION['students'],
        fn(array $student): bool => $student['nim'] === $nim
    );
    if ($nim !== '' && $nimExists !== []) {
        $errors[] = 'NIM sudah terdaftar.';
    }

    if ($errors === []) {
        $score = calculateFinalScore((float) $assignment, (float) $midterm, (float) $finalExam);
        $_SESSION['students'][] = [
            'nim' => $nim,
            'name' => $name,
            'assignment' => (float) $assignment,
            'midterm' => (float) $midterm,
            'final_exam' => (float) $finalExam,
            'final_score' => $score,
            'grade' => determineGrade($score),
            'status' => determineStatus($score),
        ];
        $success = 'Data mahasiswa berhasil ditambahkan.';
    }
}

$keyword = cleanText((string) ($_GET['keyword'] ?? ''));
$sort = (string) ($_GET['sort'] ?? 'score_desc');
$students = $_SESSION['students'];

if ($keyword !== '') {
    $students = array_values(array_filter(
        $students,
        fn(array $student): bool =>
            stripos($student['nim'], $keyword) !== false ||
            stripos($student['name'], $keyword) !== false
    ));
}

usort($students, function (array $a, array $b) use ($sort): int {
    return match ($sort) {
        'name_asc' => strcasecmp($a['name'], $b['name']),
        'score_asc' => $a['final_score'] <=> $b['final_score'],
        default => $b['final_score'] <=> $a['final_score'],
    };
});

$summary = buildSummary($_SESSION['students']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Pengolahan Nilai Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="hero">
    <div class="container hero-content">
        <div>
            <p class="eyebrow">PWeb 2026 · PHP Array & Function</p>
            <h1>Aplikasi Pengolahan Nilai Mahasiswa</h1>
            <p class="subtitle">Mengelola data nilai, menghitung nilai akhir, menentukan grade, dan menampilkan status kelulusan secara otomatis.</p>
        </div>
        <div class="php-badge">PHP<br><strong>8+</strong></div>
    </div>
</header>

<main class="container">
    <section class="stats" aria-label="Ringkasan data">
        <article><span>Total Mahasiswa</span><strong><?= $summary['count'] ?></strong></article>
        <article><span>Rata-rata Kelas</span><strong><?= number_format($summary['average'], 2) ?></strong></article>
        <article><span>Nilai Tertinggi</span><strong><?= number_format($summary['highest'], 2) ?></strong></article>
        <article><span>Jumlah Lulus</span><strong><?= $summary['passed'] ?></strong></article>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <p class="section-label">Form Input</p>
                <h2>Tambah Data Mahasiswa</h2>
            </div>
            <form method="post" onsubmit="return confirm('Kembalikan data ke kondisi awal?')">
                <input type="hidden" name="action" value="reset">
                <button class="button button-secondary" type="submit">Reset Data</button>
            </form>
        </div>

        <?php if ($success !== ''): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($errors !== []): ?>
            <div class="alert error"><strong>Data belum dapat disimpan:</strong><ul>
                <?php foreach ($errors as $error): ?><li><?= $error ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>

        <form class="student-form" method="post" action="index.php">
            <input type="hidden" name="action" value="add">
            <label>NIM<input name="nim" placeholder="Contoh: 2026005" required></label>
            <label>Nama Mahasiswa<input name="name" placeholder="Nama lengkap" required></label>
            <label>Nilai Tugas<input name="assignment" type="number" min="0" max="100" step="0.01" placeholder="0–100" required></label>
            <label>Nilai UTS<input name="midterm" type="number" min="0" max="100" step="0.01" placeholder="0–100" required></label>
            <label>Nilai UAS<input name="final_exam" type="number" min="0" max="100" step="0.01" placeholder="0–100" required></label>
            <button class="button button-primary" type="submit">Simpan Data</button>
        </form>
        <p class="formula">Rumus nilai akhir: <strong>30% Tugas + 30% UTS + 40% UAS</strong></p>
    </section>

    <section class="panel">
        <div class="panel-heading table-heading">
            <div>
                <p class="section-label">Hasil Pengolahan</p>
                <h2>Daftar Nilai Mahasiswa</h2>
            </div>
            <form class="filter-form" method="get">
                <input name="keyword" value="<?= $keyword ?>" placeholder="Cari NIM atau nama">
                <select name="sort">
                    <option value="score_desc" <?= $sort === 'score_desc' ? 'selected' : '' ?>>Nilai tertinggi</option>
                    <option value="score_asc" <?= $sort === 'score_asc' ? 'selected' : '' ?>>Nilai terendah</option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Nama A–Z</option>
                </select>
                <button class="button button-primary" type="submit">Terapkan</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead><tr><th>No.</th><th>NIM</th><th>Nama</th><th>Tugas</th><th>UTS</th><th>UAS</th><th>Nilai Akhir</th><th>Grade</th><th>Status</th></tr></thead>
                <tbody>
                <?php if ($students === []): ?>
                    <tr><td class="empty" colspan="9">Data tidak ditemukan.</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $student['nim'] ?></td>
                            <td class="student-name"><?= $student['name'] ?></td>
                            <td><?= number_format((float) $student['assignment'], 0) ?></td>
                            <td><?= number_format((float) $student['midterm'], 0) ?></td>
                            <td><?= number_format((float) $student['final_exam'], 0) ?></td>
                            <td><strong><?= number_format((float) $student['final_score'], 2) ?></strong></td>
                            <td><span class="grade grade-<?= strtolower($student['grade']) ?>"><?= $student['grade'] ?></span></td>
                            <td><span class="status <?= $student['status'] === 'Lulus' ? 'passed' : 'failed' ?>"><?= $student['status'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<footer><div class="container">PWeb 2026 · Tugas 13.2 Penanganan Form di PHP</div></footer>
</body>
</html>
