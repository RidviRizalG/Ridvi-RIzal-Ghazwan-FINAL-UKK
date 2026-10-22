<?php
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas = $_SESSION['kelas'] ?? '-';
$list_kategori = $list_kategori ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengaduan - Pengaduan Sarana Prasarana</title>
    <link rel="stylesheet" href="../CSS/01-warna-dan-font.css?v=<?= @filemtime(__DIR__ . '/../CSS/01-warna-dan-font.css') ?>">
    <link rel="stylesheet" href="../CSS/02-navbar-atas.css?v=<?= @filemtime(__DIR__ . '/../CSS/02-navbar-atas.css') ?>">
    <link rel="stylesheet" href="../CSS/03-form-dan-tombol.css?v=<?= @filemtime(__DIR__ . '/../CSS/03-form-dan-tombol.css') ?>">
    <link rel="stylesheet" href="../CSS/04-tabel-dan-badge.css?v=<?= @filemtime(__DIR__ . '/../CSS/04-tabel-dan-badge.css') ?>">
    <link rel="stylesheet" href="../CSS/05-admin-dan-dashboard.css?v=<?= @filemtime(__DIR__ . '/../CSS/05-admin-dan-dashboard.css') ?>">
    <link rel="stylesheet" href="../CSS/08-responsif-dan-hiasan.css?v=<?= @filemtime(__DIR__ . '/../CSS/08-responsif-dan-hiasan.css') ?>">
    <meta name="theme-color" content="#74172f">
</head>
<body>

<div class="navbar">
    <div class="brand">
        <img class="logo" src="../ASSETS/logo_sekolah_temagelap.png" alt="Logo Sekolah">
        <span>Aplikasi Pengaduan Sarpras</span>
    </div>

    <div class="user-info">
        <span class="role-name">
            Siswa:
            <?= htmlspecialchars($nama_siswa) ?>
        </span>
        <small>(<?= htmlspecialchars($kelas) ?>)</small>
        <?php require __DIR__ . '/_notifikasi_siswa.php'; ?>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema"></button>
        <a class="btn-logout" href="../CONTROLLERS/c_logout.php" title="Logout">
            <img src="../ASSETS/logout.png" alt="Logout">
        </a>
    </div>
</div>

<main class="student-form-page">
    <section class="form-container student-form-card no-top-nav">
        <div class="form-heading">
            <span class="form-eyebrow">SARANA &amp; PRASARANA SEKOLAH</span>
            <h1>Form Pengaduan</h1>
            <p>Isi informasi berikut dengan jelas agar petugas dapat menindaklanjuti laporanmu.</p>
        </div>

        <?php if (($_GET['status'] ?? '') === 'data_tidak_lengkap'): ?>
            <div class="form-alert">Mohon lengkapi semua kolom sebelum mengirim pengaduan.</div>
        <?php elseif (($_GET['status'] ?? '') === 'gagal'): ?>
            <div class="form-alert">Pengaduan belum berhasil dikirim. Silakan coba kembali.</div>
        <?php endif; ?>

        <form action="../CONTROLLERS/c_tambah_aspirasi_siswa.php" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="id_kategori">Kategori Sarana/Prasarana <span>*</span></label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="">Pilih kategori pengaduan</option>
                    <?php foreach ($list_kategori as $kat): ?>
                        <option value="<?= htmlspecialchars($kat['id_kategori']) ?>">
                            <?= htmlspecialchars($kat['ket_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="lokasi">Lokasi Sarana/Prasarana <span>*</span></label>
                <input id="lokasi" type="text" name="lokasi" maxlength="50"
                       placeholder="Contoh: Ruang XII RPL 1 atau Lab Komputer" required>
                <small class="field-hint">Tuliskan lokasi yang spesifik agar mudah ditemukan.</small>
            </div>

            <div class="form-group">
                <label for="ket">Keterangan Pengaduan <span>*</span></label>
                <textarea id="ket" name="ket" rows="5" maxlength="255"
                          placeholder="Jelaskan masalah atau kerusakan yang kamu temukan..." required></textarea>
                <small class="field-hint">Jelaskan kondisi dan kebutuhan dengan singkat serta jelas (maksimal 255 karakter).</small>
            </div>

            <div class="form-action-row">
                <button type="submit" class="btn-submit btn-submit-compact">Kirim Pengaduan</button>
                <a class="btn-cancel" href="../CONTROLLERS/c_beranda_siswa.php?page=dashboard">Kembali</a>
            </div>
        </form>
    </section>
</main>

<script src="../JS/notifikasi.js?v=<?= @filemtime(__DIR__ . '/../JS/notifikasi.js') ?>"></script>
<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>
