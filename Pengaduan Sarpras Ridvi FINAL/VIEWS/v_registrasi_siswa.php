<?php require_once __DIR__ . '/../MODELS/m_siswa.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa - Pengaduan Sarana Prasarana</title>
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
        <span>Registrasi Siswa - Pengaduan Sarpras</span>
        <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema"></button>
    </div>
</div>

<div class="main-container">
    <div class="form-container">
        
        <h3>Registrasi Akun Siswa</h3>

        <form action="../CONTROLLERS/c_registrasi_siswa.php?aksi=daftar" method="post" autocomplete="off">

            <div class="form-group">
                <label>NIS (Nomor Induk Siswa)</label>
                <input type="number" name="nis" placeholder="Masukkan NIS" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Nama Lengkap Siswa</label>
                <input type="text" name="nama_siswa" placeholder="Masukkan nama lengkap" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: XII RPL 1" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Jurusan</label>
                <select name="jurusan" required autocomplete="off">
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach (M_Siswa::JURUSAN as $kode => $namaJurusan): ?>
                        <option value="<?= htmlspecialchars($namaJurusan) ?>" <?= ($pilih ?? '') === $namaJurusan ? 'selected' : '' ?>><?= htmlspecialchars($namaJurusan) ?> (<?= $kode ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required autocomplete="off">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Buat password akun" required autocomplete="new-password">
            </div>

            <button type="submit" class="button-submit">
                Daftar Akun Siswa
            </button>

            <div class="text-center">
                <p>Sudah punya akun? <a href="v_login.php">Login di sini</a></p>
            </div>

        </form>
    </div>
</div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>
