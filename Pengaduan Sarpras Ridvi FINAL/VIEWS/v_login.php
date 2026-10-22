<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengaduan Sarana Prasarana</title>
    <link rel="stylesheet" href="../CSS/01-warna-dan-font.css?v=<?= @filemtime(__DIR__ . '/../CSS/01-warna-dan-font.css') ?>">
    <link rel="stylesheet" href="../CSS/02-navbar-atas.css?v=<?= @filemtime(__DIR__ . '/../CSS/02-navbar-atas.css') ?>">
    <link rel="stylesheet" href="../CSS/03-form-dan-tombol.css?v=<?= @filemtime(__DIR__ . '/../CSS/03-form-dan-tombol.css') ?>">
    <link rel="stylesheet" href="../CSS/04-tabel-dan-badge.css?v=<?= @filemtime(__DIR__ . '/../CSS/04-tabel-dan-badge.css') ?>">
    <link rel="stylesheet" href="../CSS/05-admin-dan-dashboard.css?v=<?= @filemtime(__DIR__ . '/../CSS/05-admin-dan-dashboard.css') ?>">
    <link rel="stylesheet" href="../CSS/08-responsif-dan-hiasan.css?v=<?= @filemtime(__DIR__ . '/../CSS/08-responsif-dan-hiasan.css') ?>">
    <meta name="theme-color" content="#74172f">
</head>

<body>

<div class="navbar top-login-navbar">
    <div class="brand">
        <img class="logo login-brand-logo" src="../ASSETS/logo_sekolah_temagelap.png" alt="Logo Sekolah">
        <span>Pengaduan Sarana Prasarana</span>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema"></button>
    </div>
</div>

<div class="login-page-wrap">
    <div class="login-container">
        <h2>Login Pengaduan Sarana Prasarana</h2>

        <form action="../CONTROLLERS/c_login.php" method="POST" autocomplete="off">

            <div class="input-group">
                <label>NIS Siswa / Username Admin</label>
                <input type="text" name="login" placeholder="Masukkan NIS atau username admin" required autocomplete="off">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required autocomplete="new-password">
            </div>

            <button type="submit" class="login-btn">Login</button>

        </form>

        <div class="text-center">
            <p>Belum punya akun? <a href="v_registrasi_siswa.php">Daftar</a></p>
        </div>
    </div>
</div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>