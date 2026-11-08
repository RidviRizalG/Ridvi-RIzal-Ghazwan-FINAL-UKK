<?php
// Halaman ini hanya boleh dimuat lewat controller, bukan dibuka langsung lewat URL.
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Location: v_login.php');
    exit();
}
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
        <?php elseif (($_GET['status'] ?? '') === 'bukti_wajib'): ?>
            <div class="form-alert">Bukti (foto atau video) wajib dilampirkan sebelum mengirim pengaduan.</div>
        <?php elseif (($_GET['status'] ?? '') === 'bukti_tidak_valid'): ?>
            <div class="form-alert">File yang dipilih tidak valid. Gunakan foto (JPG, PNG, WEBP) atau video (MP4, WEBM, MOV).</div>
        <?php elseif (($_GET['status'] ?? '') === 'bukti_terlalu_besar'): ?>
            <div class="form-alert">Ukuran file terlalu besar. Foto maksimal 5 MB, video maksimal 9 MB.</div>
        <?php elseif (($_GET['status'] ?? '') === 'bukti_gagal'): ?>
            <div class="form-alert">Bukti gagal diunggah. Silakan coba kembali.</div>
        <?php elseif (($_GET['status'] ?? '') === 'gagal'): ?>
            <div class="form-alert">Pengaduan belum berhasil dikirim. Silakan coba kembali.</div>
        <?php endif; ?>

        <form id="form-pengaduan" action="../CONTROLLERS/c_tambah_aspirasi_siswa.php" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
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

            <div class="form-group">
                <label id="label-bukti">Bukti Foto / Video <span>*</span></label>

                <div class="bukti-zona" id="bukti-zona" aria-labelledby="label-bukti">
                    <div class="bukti-zona-isi" id="bukti-kosong">
                        <div class="bukti-ikon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                        </div>
                        <p class="bukti-judul">Lampirkan foto atau video kerusakan</p>
                        <p class="bukti-sub"><span class="bukti-drag">Seret file ke sini, atau </span>pilih salah satu cara di bawah</p>

                        <div class="bukti-pilihan">
                            <label class="bukti-opsi" for="bukti_foto">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8a2 2 0 0 1 2-2h2l1.5-2h7L17 6h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="12" cy="13" r="3.5"/></svg>
                                <strong>Foto</strong><small>Ambil dari kamera</small>
                            </label>
                            <label class="bukti-opsi" for="bukti_video">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="13" height="12" rx="2"/><path d="M16 10.5 21 8v8l-5-2.5z"/></svg>
                                <strong>Video</strong><small>Rekam dari kamera</small>
                            </label>
                            <label class="bukti-opsi" for="bukti_file">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                <strong>File</strong><small>Pilih dari galeri / file</small>
                            </label>
                        </div>
                    </div>

                    <div class="bukti-hasil" id="bukti-hasil" hidden>
                        <div class="bukti-media" id="bukti-media"></div>
                        <div class="bukti-info">
                            <span class="bukti-badge" id="bukti-badge">Foto</span>
                            <p class="bukti-nama" id="bukti-nama"></p>
                            <p class="bukti-ukuran" id="bukti-ukuran"></p>
                            <button type="button" class="bukti-ganti" id="bukti-hapus">Ganti / hapus</button>
                        </div>
                    </div>
                </div>

                <input id="bukti_foto" class="foto-input" type="file" name="bukti_foto" accept="image/*" capture="environment">
                <input id="bukti_video" class="foto-input" type="file" name="bukti_video" accept="video/*" capture="environment">
                <input id="bukti_file" class="foto-input" type="file" name="bukti_file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime,video/3gpp">

                <div id="bukti-error" class="form-alert bukti-error" role="alert" hidden>Bukti foto atau video wajib dilampirkan.</div>
                <small class="field-hint">Wajib. Foto: JPG, PNG, WEBP (maks. 5 MB). Video: MP4, WEBM, MOV (maks. 9 MB).</small>
            </div>

            <div class="form-action-row">
                <button type="submit" class="btn-submit btn-submit-compact">Kirim Pengaduan</button>
                <a class="btn-cancel" href="../CONTROLLERS/c_beranda_siswa.php?page=dashboard">Kembali</a>
            </div>
        </form>
    </section>
</main>

<script src="../JS/notifikasi.js?v=<?= @filemtime(__DIR__ . '/../JS/notifikasi.js') ?>"></script>
<script src="../JS/bukti.js?v=<?= @filemtime(__DIR__ . '/../JS/bukti.js') ?>"></script>
<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>
</html>
