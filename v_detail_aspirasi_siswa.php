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

// Rating kepuasan siswa terhadap kinerja admin (hanya untuk pengaduan yang sudah Selesai).
$pesan = $pesan ?? '';
$ratingInput = (int)($ratingInput ?? 0);
$komentarInput = (string)($komentarInput ?? '');
$ratingSiswa = ($detail['rating'] ?? null) !== null ? (int)$detail['rating'] : null;
$komentarSiswa = trim((string)($detail['komentar_rating'] ?? ''));
$tglRating = M_Aspirasi::tglWib($detail['tgl_rating'] ?? '', true);
$labelRating = M_Aspirasi::RATING_LABEL;
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

            <?php if ($status === 'Selesai'): ?>
            <section class="detail-section" id="penilaian" aria-labelledby="judul-penilaian">
                <h4 class="detail-section-title" id="judul-penilaian">Penilaian Kinerja Admin</h4>

                <?php if ($pesan === 'rating_kosong'): ?>
                    <div class="form-alert">Pilih jumlah bintang terlebih dahulu.</div>
                <?php elseif ($pesan === 'komentar_kosong'): ?>
                    <div class="form-alert">Komentar wajib diisi untuk memberi penilaian, berapa pun bintangnya.</div>
                <?php elseif ($pesan === 'komentar_panjang'): ?>
                    <div class="form-alert">Komentar terlalu panjang (maksimal <?= M_Aspirasi::KOMENTAR_MAKS ?> karakter).</div>
                <?php elseif ($pesan === 'gagal'): ?>
                    <div class="form-alert">Penilaian gagal disimpan. Silakan coba lagi.</div>
                <?php elseif ($pesan === 'sudah_dinilai'): ?>
                    <div class="form-alert">Pengaduan ini sudah kamu nilai sebelumnya.</div>
                <?php elseif ($pesan === 'rating_ok'): ?>
                    <div class="rating-sukses">Penilaian terkirim. Terima kasih sudah memberi masukan!</div>
                <?php endif; ?>

                <?php if ($ratingSiswa !== null): ?>
                    <p class="rating-intro">Penilaian kamu untuk kinerja <?= $namaAdmin !== '' ? 'Admin ' . htmlspecialchars($namaAdmin) : 'admin' ?>:</p>
                    <div class="rating-bintang rating-baca" role="img" aria-label="Rating <?= $ratingSiswa ?> dari 5 bintang: <?= htmlspecialchars($labelRating[$ratingSiswa] ?? '') ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="rating-bintang-ikon<?= $i <= $ratingSiswa ? ' aktif' : '' ?>" aria-hidden="true">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <p class="rating-hasil"><strong><?= $ratingSiswa ?>/5</strong> &ndash; <?= htmlspecialchars($labelRating[$ratingSiswa] ?? '') ?></p>
                    <?php if ($komentarSiswa !== ''): ?>
                        <blockquote class="rating-komentar-baca"><?= htmlspecialchars($komentarSiswa) ?></blockquote>
                    <?php endif; ?>
                    <p class="rating-catatan">
                        Terima kasih atas penilaianmu<?= $tglRating !== '' ? ' (' . htmlspecialchars($tglRating) . ')' : '' ?>.
                    </p>
                <?php else: ?>
                    <form action="../CONTROLLERS/c_detail_aspirasi_siswa.php" method="POST" class="rating-form" autocomplete="off">
                        <input type="hidden" name="id_input" value="<?= (int)$detail['id_input'] ?>">
                        <p class="rating-intro">
                            Pengaduanmu sudah selesai. Seberapa puas kamu dengan kinerja <?= $namaAdmin !== '' ? 'Admin ' . htmlspecialchars($namaAdmin) : 'admin' ?>?
                        </p>

                        <div class="rating-kotak">
                            <fieldset class="rating-bintang" aria-label="Beri rating dari 1 (sangat tidak puas) sampai 5 (sangat puas)">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" name="rating" id="rating-<?= $i ?>" value="<?= $i ?>" <?= $ratingInput === $i ? 'checked' : '' ?> required>
                                    <label for="rating-<?= $i ?>" title="<?= htmlspecialchars($labelRating[$i]) ?>">
                                        <span class="rating-bintang-ikon" aria-hidden="true">&#9733;</span>
                                        <span class="rating-sr"><?= $i ?> bintang &ndash; <?= htmlspecialchars($labelRating[$i]) ?></span>
                                    </label>
                                <?php endfor; ?>
                            </fieldset>
                            <div class="rating-skala" aria-hidden="true">
                                <span>Sangat tidak puas</span>
                                <span>Sangat puas</span>
                            </div>
                            <p class="rating-pilihan" aria-live="polite">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="rating-pilihan-<?= $i ?>"><strong><?= $i ?>/5</strong> &ndash; <?= htmlspecialchars($labelRating[$i]) ?></span>
                                <?php endfor; ?>
                                <span class="rating-pilihan-kosong">Klik bintang untuk memberi penilaian.</span>
                            </p>
                        </div>

                        <div class="rating-komentar">
                            <label for="rating-komentar">Komentar <span class="rating-wajib" title="Wajib diisi">*</span></label>
                            <textarea
                                id="rating-komentar"
                                name="komentar"
                                rows="4"
                                maxlength="<?= M_Aspirasi::KOMENTAR_MAKS ?>"
                                placeholder="Ceritakan alasan penilaianmu. Wajib diisi, berapa pun bintang yang kamu pilih."
                                required
                            ><?= htmlspecialchars($komentarInput) ?></textarea>
                            <small>Wajib diisi, maksimal <?= M_Aspirasi::KOMENTAR_MAKS ?> karakter. Penilaian hanya bisa dikirim satu kali.</small>
                        </div>

                        <button type="submit" class="btn-submit btn-submit-compact rating-kirim">Kirim Penilaian</button>
                    </form>
                <?php endif; ?>
            </section>
            <?php endif; ?>

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
