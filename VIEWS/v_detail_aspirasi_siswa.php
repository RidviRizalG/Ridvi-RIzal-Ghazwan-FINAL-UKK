<?php
// Halaman ini hanya boleh dimuat lewat controller, bukan dibuka langsung lewat URL.
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Location: v_login.php');
    exit();
}
$nama_siswa = $nama_siswa ?? ($_SESSION['nama_siswa'] ?? 'Siswa');
$kelas = $kelas ?? ($_SESSION['kelas'] ?? '-');

$status = $detail['status'] ?? 'Menunggu';
$badgeClass = ['Proses' => 'badge-proses', 'Selesai' => 'badge-selesai'][$status] ?? 'badge-menunggu';

$tglLapor = M_Aspirasi::tglWib($detail['tgl_pelaporan'], true);
$tglFeedback = M_Aspirasi::tglWib($detail['tgl_feedback'] ?? '', true);
$namaAdmin = trim((string)($detail['nama_admin_feedback'] ?? ''));
$feedback = trim((string)($detail['feedback'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan - Pengaduan Sarana Prasarana</title>
    <link rel="stylesheet" href="../CSS/01-warna-dan-font.css?v=<?= @filemtime(__DIR__ . '/../CSS/01-warna-dan-font.css') ?>">
    <link rel="stylesheet" href="../CSS/02-navbar-atas.css?v=<?= @filemtime(__DIR__ . '/../CSS/02-navbar-atas.css') ?>">
    <link rel="stylesheet" href="../CSS/03-form-dan-tombol.css?v=<?= @filemtime(__DIR__ . '/../CSS/03-form-dan-tombol.css') ?>">
    <link rel="stylesheet" href="../CSS/04-tabel-dan-badge.css?v=<?= @filemtime(__DIR__ . '/../CSS/04-tabel-dan-badge.css') ?>">
    <link rel="stylesheet" href="../CSS/05-admin-dan-dashboard.css?v=<?= @filemtime(__DIR__ . '/../CSS/05-admin-dan-dashboard.css') ?>">
    <link rel="stylesheet" href="../CSS/06-detail-pengaduan.css?v=<?= @filemtime(__DIR__ . '/../CSS/06-detail-pengaduan.css') ?>">
    <link rel="stylesheet" href="../CSS/08-responsif-dan-hiasan.css?v=<?= @filemtime(__DIR__ . '/../CSS/08-responsif-dan-hiasan.css') ?>">
    <meta name="theme-color" content="#74172f">
</head>
<body class="dashboard-page detail-page">

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
        <div class="admin-nav">
            <button type="button" class="nav-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="menu-navigasi">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <span class="nav-label">Menu</span>
            </button>
            <div class="menu" id="menu-navigasi">
            <a class="" href="../CONTROLLERS/c_beranda_siswa.php?page=dashboard" title="Halaman Utama">
                <img src="../ASSETS/logo_sekolah.png" alt="Halaman Utama">
                <span class="nav-label">Halaman Utama</span>
            </a>
            <a class="" href="../CONTROLLERS/c_beranda_siswa.php?page=form" title="Lakukan Pengaduan">
                <img src="../ASSETS/lakukan_pengaduan.png" alt="Lakukan Pengaduan">
                <span class="nav-label">Lakukan Pengaduan</span>
            </a>
            <?php $sudahSelesai = (($detail['status'] ?? '') === 'Selesai'); ?>
            <a class="<?= $sudahSelesai ? '' : 'active' ?>" href="../CONTROLLERS/c_list_aspirasi_siswa.php" title="List Aspirasi">
                <img src="../ASSETS/detail_aspirasi.png" alt="List Aspirasi">
                <span class="nav-label">List Aspirasi</span>
            </a>
            <a class="<?= $sudahSelesai ? 'active' : '' ?>" href="../CONTROLLERS/c_histori_aspirasi_siswa.php" title="Histori Aspirasi">
                <img src="../ASSETS/histori_pengaduan.png" alt="Histori Aspirasi">
                <span class="nav-label">Histori Aspirasi</span>
            </a>
            </div>
            <small>Siswa Panel</small>
        </div>

        <div class="card detail-card">
            <h3>Detail Pengaduan</h3>

            <section class="detail-section" aria-labelledby="judul-info">
                <h4 class="detail-section-title" id="judul-info">Informasi Pengaduan</h4>
                <dl class="detail-grid">
                    <div class="detail-item">
                        <dt>Tanggal Pengaduan</dt>
                        <dd><?= htmlspecialchars($tglLapor) ?></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Kategori</dt>
                        <dd><?= htmlspecialchars($detail['ket_kategori'] ?? '-') ?></dd>
                    </div>
                    <div class="detail-item detail-item-full">
                        <dt>Lokasi</dt>
                        <dd><?= htmlspecialchars($detail['lokasi'] ?? '-') ?></dd>
                    </div>
                    <div class="detail-item detail-item-full">
                        <dt>Isi Pengaduan</dt>
                        <dd class="detail-text"><?= htmlspecialchars($detail['ket'] ?? '-') ?></dd>
                    </div>
                    <?php if (!empty($detail['foto'])): ?>
                    <div class="detail-item detail-item-full">
                        <dt><?= M_Aspirasi::buktiAdalahVideo($detail['foto']) ? 'Video Bukti' : 'Foto Bukti' ?></dt>
                        <dd>
                            <?php if (M_Aspirasi::buktiAdalahVideo($detail['foto'])): ?>
                                <video class="foto-bukti foto-bukti-video" controls playsinline preload="metadata" src="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>">
                                    Browser kamu tidak mendukung pemutar video.
                                </video>
                                <a class="bukti-link" href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" target="_blank" rel="noopener">Buka video di tab baru</a>
                            <?php else: ?>
                                <a href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" target="_blank" rel="noopener">
                                    <img class="foto-bukti" src="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" alt="Foto bukti pengaduan">
                                </a>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </section>

            <section class="detail-section" aria-labelledby="judul-tanggapan">
                <h4 class="detail-section-title" id="judul-tanggapan">Tanggapan Admin</h4>
                <dl class="detail-grid">
                    <div class="detail-item">
                        <dt>Status</dt>
                        <dd><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></dd>
                    </div>
                    <div class="detail-item">
                        <dt>Terakhir Diperbarui</dt>
                        <dd><?= $tglFeedback !== '' ? htmlspecialchars($tglFeedback) : '-' ?></dd>
                    </div>
                    <div class="detail-item detail-item-full">
                        <dt>Ditanggapi Oleh</dt>
                        <dd><?= $namaAdmin !== '' ? 'Admin ' . htmlspecialchars($namaAdmin) : '-' ?></dd>
                    </div>
                    <div class="detail-item detail-item-full">
                        <dt>Feedback</dt>
                        <?php if ($feedback !== ''): ?>
                            <dd class="detail-text"><?= htmlspecialchars($feedback) ?></dd>
                        <?php else: ?>
                            <dd class="detail-empty">Belum ada feedback dari admin.</dd>
                        <?php endif; ?>
                    </div>
                </dl>
            </section>

            <div class="detail-actions">
                <a class="btn-cancel" href="../CONTROLLERS/<?= $sudahSelesai ? 'c_histori_aspirasi_siswa.php' : 'c_list_aspirasi_siswa.php' ?>">&larr; Kembali ke <?= $sudahSelesai ? 'Histori Aspirasi' : 'List Aspirasi' ?></a>
            </div>
        </div>
    </div>

<script src="../JS/notifikasi.js?v=<?= @filemtime(__DIR__ . '/../JS/notifikasi.js') ?>"></script>
<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
<script src="../JS/navigasi.js?v=<?= @filemtime(__DIR__ . '/../JS/navigasi.js') ?>"></script>
</body>
</html>
