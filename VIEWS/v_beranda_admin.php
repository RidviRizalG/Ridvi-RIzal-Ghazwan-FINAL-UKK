<?php
// Halaman ini hanya boleh dimuat lewat controller, bukan dibuka langsung lewat URL.
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Location: v_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Pengaduan Sarana Prasarana</title>
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
            <span>Aplikasi Pengaduan Sarana Prasarana SMK Hunter x Hunter</span>
        </div>

        <div class="user-info">

            <span class="role-name">
                Admin:
                <?= htmlspecialchars($nama_admin) ?>
            </span>

            <small>
                (<?= htmlspecialchars($role_admin) ?>)
            </small>

            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema"></button>
            <a
                class="btn-logout"
                href="../CONTROLLERS/c_logout.php"
                title="Logout"
            >
                <img src="../ASSETS/logout.png" alt="Logout">
            </a>

        </div>

    </div>

    <!-- =========================
         CONTENT
    ========================= -->

    <div class="container">

            <div class="dashboard-hero">
                <div class="dashboard-hero-content">
                    <img class="dashboard-hero-logo" src="../ASSETS/logo_sekolah.png" alt="Logo Sekolah" style="border:0;object-fit:contain;">
                    <h2>Selamat datang di Pengaduan Sarana Prasarana</h2>
                    <h2>SMK Hunter x Hunter</h2>
                    <div class="dashboard-name">
                        <?= htmlspecialchars($_SESSION['nama_admin'] ?? 'Admin') ?>
                    </div>
                    <h3>Kelola data siswa dan pantau histori aspirasi secara mudah dan terorganisir</h3>
                </div>
            </div>

            <div class="dashboard-nav-grid dashboard-nav-grid-3 student-dashboard-nav">
                <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_list_aspirasi_admin.php">
                    <img src="../ASSETS/detail_aspirasi.png" alt="">
                    <div class="student-link-text">
                        <span class="student-link-title">List Aspirasi</span>
                        <small>Pantau aspirasi siswa yang masih menunggu atau sedang diproses, lalu beri status dan feedback.</small>
                    </div>
                </a>

                <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_histori_aspirasi_admin.php">
                    <img src="../ASSETS/historiaspirasi.png" alt="">
                    <div class="student-link-text">
                        <span class="student-link-title">Histori Aspirasi</span>
                        <small>Lihat aspirasi siswa yang sudah selesai ditangani, beserta feedback yang diberikan.</small>
                    </div>
                </a>

                <a class="dashboard-link student-dashboard-link" href="../CONTROLLERS/c_list_siswa_admin.php">
                    <img src="../ASSETS/datasiswa.png" alt="">
                    <div class="student-link-text">
                        <span class="student-link-title">List Siswa</span>
                        <small>Lihat, tambah, ubah, dan hapus data siswa yang terdaftar di aplikasi.</small>
                    </div>
                </a>
            </div>

    </div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>

</html>
