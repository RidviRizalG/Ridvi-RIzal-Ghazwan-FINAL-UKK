<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa - Pengaduan Sarana Prasarana</title>
    <link rel="stylesheet" href="../CSS/01-warna-dan-font.css?v=<?= @filemtime(__DIR__ . '/../CSS/01-warna-dan-font.css') ?>">
    <link rel="stylesheet" href="../CSS/02-navbar-atas.css?v=<?= @filemtime(__DIR__ . '/../CSS/02-navbar-atas.css') ?>">
    <link rel="stylesheet" href="../CSS/03-form-dan-tombol.css?v=<?= @filemtime(__DIR__ . '/../CSS/03-form-dan-tombol.css') ?>">
    <link rel="stylesheet" href="../CSS/04-tabel-dan-badge.css?v=<?= @filemtime(__DIR__ . '/../CSS/04-tabel-dan-badge.css') ?>">
    <link rel="stylesheet" href="../CSS/05-admin-dan-dashboard.css?v=<?= @filemtime(__DIR__ . '/../CSS/05-admin-dan-dashboard.css') ?>">
    <link rel="stylesheet" href="../CSS/08-responsif-dan-hiasan.css?v=<?= @filemtime(__DIR__ . '/../CSS/08-responsif-dan-hiasan.css') ?>">
    <meta name="theme-color" content="#74172f">
</head>
<body class="dashboard-page">
    <div class="navbar">
        <div class="brand">
            <img class="logo" src="../ASSETS/logo_sekolah_temagelap.png" alt="Logo Sekolah">
            <span>Aplikasi Pengaduan Sarpras</span>
        </div>
        <div class="user-info">
            <span class="role-name">Siswa: <?= htmlspecialchars($nama_siswa ?? 'Siswa') ?></span>
            <small>(<?= htmlspecialchars($kelas ?? '-') ?>)</small>
        <?php require __DIR__ . '/_notifikasi_siswa.php'; ?>
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema"></button>
            <a class="btn-logout" href="../CONTROLLERS/c_logout.php" title="Logout">
                <img src="../ASSETS/logout.png" alt="Logout">
            </a>
        </div>
    </div>

    <main class="container">
        <section class="dashboard-hero student-dashboard-hero">
            <div class="dashboard-hero-content">
                <img class="dashboard-hero-logo" src="../ASSETS/logo_sekolah.png" alt="Logo Sekolah">
                <h2>Selamat datang di Aplikasi Pengaduan Sarana Prasarana</h2>
                <h2>SMK Hunter x Hunter</h2>
                <div class="dashboard-name"><?= htmlspecialchars($nama_siswa ?? 'Siswa') ?></div>
                <p class="student-welcome-text">
                    Sampaikan aspirasi dan pengaduanmu untuk membantu menciptakan lingkungan sekolah yang lebih nyaman dan baik.
                </p>
            </div>
        </section>

        <section class="dashboard-nav-grid dashboard-nav-grid-3 student-dashboard-nav" aria-label="Navigasi siswa">
            <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_beranda_siswa.php?page=form">
                <img src="../ASSETS/lakukan_pengaduan.png" alt="">
                <div class="student-link-text">
                    <span class="student-link-title">Lakukan Pengaduan</span>
                    <small>Sampaikan laporan mengenai kerusakan atau kebutuhan sarana dan prasarana sekolah.</small>
                </div>
            </a>
            <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_list_aspirasi_siswa.php">
                <img src="../ASSETS/detail_aspirasi.png" alt="">
                <div class="student-link-text">
                    <span class="student-link-title">List Aspirasi</span>
                    <small>Pantau pengaduanmu yang masih menunggu atau sedang diproses oleh petugas.</small>
                </div>
            </a>
            <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_list_aspirasi_siswa.php?mode=histori">
                <img src="../ASSETS/histori_pengaduan.png" alt="">
                <div class="student-link-text">
                    <span class="student-link-title">Histori Aspirasi</span>
                    <small>Lihat pengaduanmu yang sudah selesai ditangani, beserta tanggapan dari petugas.</small>
                </div>
            </a>
        </section>
    </main>
    <script src="../JS/notifikasi.js?v=<?= @filemtime(__DIR__ . '/../JS/notifikasi.js') ?>"></script>
<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>
