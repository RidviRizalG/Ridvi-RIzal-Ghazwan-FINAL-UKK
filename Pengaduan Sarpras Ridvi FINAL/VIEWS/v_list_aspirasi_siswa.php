<?php
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas = $_SESSION['kelas'] ?? '-';
$list_aspirasi = $list_aspirasi ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Aspirasi - Pengaduan Sarana Prasarana</title>
    <link rel="stylesheet" href="../CSS/01-warna-dan-font.css?v=<?= @filemtime(__DIR__ . '/../CSS/01-warna-dan-font.css') ?>">
    <link rel="stylesheet" href="../CSS/02-navbar-atas.css?v=<?= @filemtime(__DIR__ . '/../CSS/02-navbar-atas.css') ?>">
    <link rel="stylesheet" href="../CSS/03-form-dan-tombol.css?v=<?= @filemtime(__DIR__ . '/../CSS/03-form-dan-tombol.css') ?>">
    <link rel="stylesheet" href="../CSS/04-tabel-dan-badge.css?v=<?= @filemtime(__DIR__ . '/../CSS/04-tabel-dan-badge.css') ?>">
    <link rel="stylesheet" href="../CSS/05-admin-dan-dashboard.css?v=<?= @filemtime(__DIR__ . '/../CSS/05-admin-dan-dashboard.css') ?>">
    <link rel="stylesheet" href="../CSS/06-detail-pengaduan.css?v=<?= @filemtime(__DIR__ . '/../CSS/06-detail-pengaduan.css') ?>">
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

    <div class="container">
        <div class="student-top-actions" style="margin-bottom: 18px; display: flex; gap: 12px; flex-wrap: wrap; justify-content: flex-end;">
            <a class="student-mini-nav" href="../CONTROLLERS/c_beranda_siswa.php?page=dashboard" title="Halaman Utama">
                <img src="../ASSETS/logo_sekolah.png" alt="Halaman Utama" width="22" height="22">
            </a>
            <a class="student-mini-nav" href="../CONTROLLERS/c_beranda_siswa.php?page=form" title="Lakukan Pengaduan">
                <img src="../ASSETS/lakukan_pengaduan.png" alt="Lakukan Pengaduan" width="22" height="22">
            </a>
            <a class="student-mini-nav active" href="../CONTROLLERS/c_list_aspirasi_siswa.php" title="List Aspirasi">
                <img src="../ASSETS/detail_aspirasi.png" alt="List Aspirasi" width="22" height="22">
            </a>
            <a class="student-mini-nav" href="../CONTROLLERS/c_list_aspirasi_siswa.php?mode=histori" title="Histori Aspirasi">
                <img src="../ASSETS/histori_pengaduan.png" alt="Histori Aspirasi" width="22" height="22">
            </a>
        </div>

        <div class="card">
            <h3>List Aspirasi Anda</h3>
            <p style="margin:-6px 0 14px;font-size:14px;color:var(--teks-medium);">
                Pengaduan Anda yang masih menunggu atau sedang diproses. Yang sudah selesai ada di Histori Aspirasi.
            </p>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tgl / Kategori</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list_aspirasi)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--teks-medium);padding:20px;">
                                    Tidak ada pengaduan yang menunggu atau sedang diproses.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list_aspirasi as $row): ?>
                                <?php
                                $badgeClass = 'badge-menunggu';
                                if (($row['status'] ?? '') === 'Proses') {
                                    $badgeClass = 'badge-proses';
                                }
                                if (($row['status'] ?? '') === 'Selesai') {
                                    $badgeClass = 'badge-selesai';
                                }
                                ?>
                                <tr>
                                    <td data-label="Tgl / Kategori">
                                        <small style="color:var(--accent-text);font-weight:600;">
                                            <?= date('d/m/Y H:i', strtotime($row['tgl_pelaporan'])) ?>
                                        </small>
                                        <br>
                                        <strong>
                                            <?= htmlspecialchars($row['ket_kategori']) ?>
                                        </strong>
                                    </td>

                                    <td data-label="Lokasi">
                                        <strong>
                                            <?= htmlspecialchars($row['lokasi']) ?>
                                        </strong>
                                    </td>

                                    <td data-label="Status">
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($row['status'] ?? 'Menunggu') ?>
                                        </span>
                                    </td>

                                    <td data-label="Aksi">
                                        <a
                                            class="detail-icon-btn"
                                            href="../CONTROLLERS/c_detail_aspirasi_siswa.php?id=<?= (int)$row['id_input'] ?>"
                                            title="Lihat Detail Pengaduan"
                                            aria-label="Lihat detail pengaduan"
                                        >
                                            <img src="../ASSETS/detail_aspirasi.png" alt="Detail Pengaduan" width="26" height="26">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="../JS/notifikasi.js?v=<?= @filemtime(__DIR__ . '/../JS/notifikasi.js') ?>"></script>
<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>
