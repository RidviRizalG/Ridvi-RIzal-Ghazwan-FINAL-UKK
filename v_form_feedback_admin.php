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

    <title><?= ($detail['status'] ?? '') === 'Selesai' ? 'Detail Histori Pengaduan' : 'Beri Feedback' ?> - Pengaduan Sarana Prasarana</title>
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

        <!-- ADMIN NAVIGATION -->

<div class="admin-nav">

                <button type="button" class="nav-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="menu-navigasi">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <span class="nav-label">Menu</span>
                </button>
                <div class="menu" id="menu-navigasi">

                    <a
                        class=""
                        href="../CONTROLLERS/c_beranda_admin.php"
                        title="Dashboard"
                    >
                        <img src="../ASSETS/logo_dashboard.png" alt="Dashboard" style="width:24px;height:24px;object-fit:contain;display:block;">
                        <span class="nav-label">Dashboard</span>
                    </a>

                    <a
                        class="<?= $dari === 'histori' ? '' : 'active' ?>"
                        href="../CONTROLLERS/c_list_aspirasi_admin.php"
                        title="List Aspirasi"
                    >
                        <img src="../ASSETS/detail_aspirasi.png" alt="List Aspirasi">
                        <span class="nav-label">List Aspirasi</span>
                    </a>

                    <a
                        class="<?= $dari === 'histori' ? 'active' : '' ?>"
                        href="../CONTROLLERS/c_histori_aspirasi_admin.php"
                        title="Histori Aspirasi"
                    >
                        <img src="../ASSETS/historiaspirasi.png" alt="Histori Aspirasi">
                        <span class="nav-label">Histori Aspirasi</span>
                    </a>

                    <a
                        class=""
                        href="../CONTROLLERS/c_list_siswa_admin.php"
                        title="List Siswa"
                    >
                        <img src="../ASSETS/datasiswa.png" alt="List Siswa">
                        <span class="nav-label">List Siswa</span>
                    </a>

                </div>

                <small>Admin Panel</small>

            </div>

            <?php
            $statusSekarang = $detail['status'] ?? 'Menunggu';
            $tglLapor = M_Aspirasi::tglWib($detail['tgl_pelaporan'], true);
            ?>

            <div class="card detail-card">

                <h3><?= $statusSekarang === 'Selesai' ? 'Detail Histori Pengaduan' : 'Beri Feedback' ?></h3>

                <?php if (($pesan ?? '') === 'gagal'): ?>
                    <div class="form-alert">Feedback gagal disimpan. Silakan coba lagi.</div>
                <?php elseif (($pesan ?? '') === 'isi_feedback'): ?>
                    <div class="form-alert">Silahkan isi feedback terlebih dahulu.</div>
                <?php elseif (($pesan ?? '') === 'ubah_status'): ?>
                    <div class="form-alert">Silahkan rubah status terlebih dahulu.</div>
                <?php elseif (($pesan ?? '') === 'status_mundur'): ?>
                    <div class="form-alert">Status tidak bisa dikembalikan ke status sebelumnya.</div>
                <?php elseif (($pesan ?? '') === 'status_tidak_valid'): ?>
                    <div class="form-alert">Status yang dipilih tidak valid.</div>
                <?php endif; ?>

                <section class="detail-section" aria-labelledby="judul-info">
                    <h4 class="detail-section-title" id="judul-info">Detail Pengaduan</h4>
                    <dl class="detail-grid">
                        <div class="detail-item">
                            <dt>Siswa</dt>
                            <dd>
                                <?= htmlspecialchars($detail['nama_siswa'] ?? '-') ?>
                                (<?= htmlspecialchars($detail['kelas'] ?? '-') ?>)
                            </dd>
                        </div>
                        <div class="detail-item">
                            <dt>Tanggal Pengaduan</dt>
                            <dd><?= htmlspecialchars($tglLapor) ?></dd>
                        </div>
                        <div class="detail-item detail-item-full">
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
                                <a class="bukti-link" href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" download="<?= htmlspecialchars($detail['foto']) ?>">&#8681; Download video</a>
                            <?php else: ?>
                                <?php // Klik foto: tampil penuh di halaman ini (ada panah kembali + download). Tanpa JS tetap terbuka di tab baru. ?>
                                <a class="bukti-buka" href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" target="_blank" rel="noopener" title="Klik untuk memperbesar">
                                    <img class="foto-bukti" src="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" alt="Foto bukti pengaduan">
                                </a>
                                <a class="bukti-link" href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" download="<?= htmlspecialchars($detail['foto']) ?>">&#8681; Download foto</a>

                                <div class="bukti-viewer" id="bukti-viewer" hidden role="dialog" aria-modal="true" aria-label="Foto bukti pengaduan">
                                    <div class="bukti-viewer-bar">
                                        <button type="button" class="bukti-viewer-back" id="bukti-viewer-back" aria-label="Kembali ke form pengaduan">&larr; Kembali</button>
                                        <a class="bukti-viewer-download" href="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" download="<?= htmlspecialchars($detail['foto']) ?>">&#8681; Download</a>
                                    </div>
                                    <div class="bukti-viewer-body">
                                        <img src="../UPLOADS/aspirasi/<?= rawurlencode($detail['foto']) ?>" alt="Foto bukti pengaduan (ukuran penuh)">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                    </dl>
                </section>

                <section class="detail-section" aria-labelledby="judul-feedback">
                    <h4 class="detail-section-title" id="judul-feedback"><?= $statusSekarang === 'Selesai' ? 'Status &amp; Feedback' : 'Ubah Status &amp; Feedback' ?></h4>

                    <?php if ($statusSekarang === 'Selesai'): ?>
                        <?php // Sudah Selesai: status dan feedback dikunci, tidak ada form. ?>
                        <dl class="detail-grid">
                            <div class="detail-item detail-item-full">
                                <dt>Status</dt>
                                <dd>Selesai</dd>
                            </div>
                            <div class="detail-item detail-item-full">
                                <dt>Feedback</dt>
                                <dd class="detail-text"><?= htmlspecialchars(($detail['feedback'] ?? '') !== '' ? $detail['feedback'] : '-') ?></dd>
                            </div>
                            <?php
                            $ratingDetail = ($detail['rating'] ?? null) !== null ? (int)$detail['rating'] : null;
                            $komentarDetail = trim((string)($detail['komentar_rating'] ?? ''));
                            ?>
                            <div class="detail-item detail-item-full">
                                <dt>Penilaian Siswa</dt>
                                <?php if ($ratingDetail !== null): ?>
                                    <dd>
                                        <span class="bintang-skala" style="--isi:<?= $ratingDetail * 20 ?>%;font-size:22px;" role="img" aria-label="<?= $ratingDetail ?> dari 5 bintang">
                                            <span class="bintang-skala-isi" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span><span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                        </span>
                                        <strong><?= $ratingDetail ?>/5</strong> &ndash; <?= htmlspecialchars(M_Aspirasi::RATING_LABEL[$ratingDetail] ?? '') ?>
                                        <?php if ($komentarDetail !== ''): ?>
                                            <blockquote class="rating-komentar-baca"><?= htmlspecialchars($komentarDetail) ?></blockquote>
                                        <?php endif; ?>
                                    </dd>
                                <?php else: ?>
                                    <dd class="detail-empty">Siswa belum memberi penilaian.</dd>
                                <?php endif; ?>
                            </div>
                        </dl>
                        <div class="form-action-row">
                            <a class="btn-cancel" href="../CONTROLLERS/<?= htmlspecialchars($batalUrl) ?>">&larr; Kembali</a>
                        </div>
                    <?php else: ?>
                    <form action="../CONTROLLERS/c_feedback_admin.php" method="POST" autocomplete="off">

                        <input type="hidden" name="id_input" value="<?= (int)$detail['id_input'] ?>">
                        <input type="hidden" name="aspirasi_page" value="<?= (int)$aspirasiPage ?>">
                        <input type="hidden" name="dari_halaman" value="<?= htmlspecialchars($dari) ?>">
                        <?php parse_str($filterQuery, $filterHidden); foreach ($filterHidden as $k => $v): ?>
                            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars((string)$v) ?>">
                        <?php endforeach; ?>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <?php
                                // Status hanya boleh maju: Menunggu -> Proses -> Selesai.
                                $urutanStatus = ['Menunggu' => 0, 'Proses' => 1, 'Selesai' => 2];
                                foreach (['Menunggu', 'Proses', 'Selesai'] as $opsi):
                                    if (($urutanStatus[$opsi] ?? 0) < ($urutanStatus[$statusSekarang] ?? 0)) { continue; }
                                ?>
                                    <option value="<?= $opsi ?>" <?= $statusSekarang === $opsi ? 'selected' : '' ?>>
                                        <?= $opsi ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea
                                id="feedback"
                                name="feedback"
                                rows="5"
                                placeholder="Tulis feedback untuk siswa..."
                            ><?= htmlspecialchars($detail['feedback'] ?? '') ?></textarea>
                        </div>

                        <div class="form-action-row">
                            <button type="submit" class="btn-submit btn-submit-compact">Kirim</button>
                            <a class="btn-cancel" href="../CONTROLLERS/<?= htmlspecialchars($batalUrl) ?>">Batal</a>
                        </div>

                    </form>
                    <?php endif; ?>
                </section>

            </div>

    </div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
<script src="../JS/navigasi.js?v=<?= @filemtime(__DIR__ . '/../JS/navigasi.js') ?>"></script>
<script src="../JS/lihat_bukti.js?v=<?= @filemtime(__DIR__ . '/../JS/lihat_bukti.js') ?>"></script>
</body>

</html>