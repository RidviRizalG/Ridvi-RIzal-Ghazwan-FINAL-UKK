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

            <?php
            // ---------- Dashboard statistik (data dari controller: $stat) ----------
            $stat = $stat ?? [];
            $statusStat = $stat['status'] ?? ['Menunggu' => 0, 'Proses' => 0, 'Selesai' => 0];
            $totalStat = (int)($stat['total'] ?? 0);
            $rt = $stat['rating'] ?? ['jumlah' => 0, 'rata' => null, 'per_bintang' => [], 'belum_dinilai' => 0, 'per_admin' => [], 'komentar' => []];
            $labelRatingStat = M_Aspirasi::RATING_LABEL;

            // Persentase aman (hindari pembagian nol), dibulatkan 1 angka desimal.
            $persen = function ($bagian, $total) {
                return $total > 0 ? round($bagian / $total * 100, 1) : 0;
            };
            $angka1 = function ($n) {
                return number_format((float)$n, 1, ',', '.');
            };
            $isiBintang = function ($rata) {
                return max(0, min(100, round(((float)$rata) / 5 * 100, 1))) . '%';
            };

            $maksKategori = 0;
            foreach (($stat['kategori'] ?? []) as $kat) {
                $maksKategori = max($maksKategori, (int)$kat['total']);
            }
            $maksBulan = 0;
            foreach (($stat['bulanan'] ?? []) as $bln) {
                $maksBulan = max($maksBulan, (int)$bln['total']);
            }
            $maksBintang = $rt['per_bintang'] ? max($rt['per_bintang']) : 0;
            ?>
            <section class="stat-section" aria-labelledby="judul-statistik">
                <h3 class="stat-heading" id="judul-statistik">Statistik Pengaduan</h3>

                <div class="stat-kartu-grid">
                    <div class="stat-kartu">
                        <small>Total Pengaduan</small>
                        <span class="stat-angka"><?= $totalStat ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Menunggu</small>
                        <span class="stat-angka"><?= (int)$statusStat['Menunggu'] ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Diproses</small>
                        <span class="stat-angka"><?= (int)$statusStat['Proses'] ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Selesai</small>
                        <span class="stat-angka"><?= (int)$statusStat['Selesai'] ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Siswa Terdaftar</small>
                        <span class="stat-angka"><?= (int)($stat['total_siswa'] ?? 0) ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Rata-rata Waktu Selesai</small>
                        <span class="stat-angka"><?= htmlspecialchars(M_Aspirasi::formatDurasi($stat['rata_selesai_menit'] ?? null)) ?></span>
                    </div>
                    <div class="stat-kartu">
                        <small>Rata-rata Rating</small>
                        <?php if ($rt['rata'] !== null): ?>
                            <span class="stat-angka"><?= $angka1($rt['rata']) ?> <span>/ 5</span></span>
                            <span class="bintang-skala" style="--isi:<?= $isiBintang($rt['rata']) ?>" role="img" aria-label="Rata-rata <?= $angka1($rt['rata']) ?> dari 5 bintang">
                                <span class="bintang-skala-isi" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span><span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </span>
                            <small style="margin-top:6px;"><?= (int)$rt['jumlah'] ?> penilaian</small>
                        <?php else: ?>
                            <span class="stat-angka">-</span>
                            <small style="margin-top:6px;">Belum ada penilaian</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="stat-panel-grid">

                    <div class="stat-panel">
                        <h4>Pengaduan per Bulan (6 bulan terakhir)</h4>
                        <div class="stat-kolom-grid">
                            <?php foreach (($stat['bulanan'] ?? []) as $bln): ?>
                                <div class="stat-kolom" title="<?= htmlspecialchars($bln['label']) ?>: <?= (int)$bln['total'] ?> pengaduan">
                                    <b><?= (int)$bln['total'] ?></b>
                                    <span class="stat-kolom-batang" style="--tinggi:<?= $persen((int)$bln['total'], max(1, $maksBulan)) * 0.78 ?>%"></span>
                                    <span><?= htmlspecialchars($bln['label']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="stat-panel">
                        <h4>Status Pengaduan</h4>
                        <?php if ($totalStat === 0): ?>
                            <p class="stat-kosong">Belum ada pengaduan.</p>
                        <?php else: ?>
                            <?php foreach (['Menunggu', 'Proses', 'Selesai'] as $st): ?>
                                <div class="stat-bar-baris">
                                    <span class="stat-bar-label"><?= $st === 'Proses' ? 'Diproses' : $st ?></span>
                                    <span class="stat-bar-jalur"><span class="stat-bar-isi" style="--lebar:<?= $persen((int)$statusStat[$st], $totalStat) ?>%"></span></span>
                                    <span class="stat-bar-nilai"><?= (int)$statusStat[$st] ?> (<?= $angka1($persen((int)$statusStat[$st], $totalStat)) ?>%)</span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="stat-panel">
                        <h4>Kategori Terbanyak</h4>
                        <?php if (empty($stat['kategori'])): ?>
                            <p class="stat-kosong">Belum ada pengaduan.</p>
                        <?php else: ?>
                            <?php foreach ($stat['kategori'] as $kat): ?>
                                <div class="stat-bar-baris">
                                    <span class="stat-bar-label" title="<?= htmlspecialchars($kat['nama']) ?>"><?= htmlspecialchars($kat['nama']) ?></span>
                                    <span class="stat-bar-jalur"><span class="stat-bar-isi" style="--lebar:<?= $persen((int)$kat['total'], max(1, $maksKategori)) ?>%"></span></span>
                                    <span class="stat-bar-nilai"><?= (int)$kat['total'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="stat-panel">
                        <h4>Rating per Bintang</h4>
                        <?php if ((int)$rt['jumlah'] === 0): ?>
                            <p class="stat-kosong">Belum ada siswa yang memberi penilaian.</p>
                        <?php else: ?>
                            <?php for ($b = 5; $b >= 1; $b--): $jml = (int)($rt['per_bintang'][$b] ?? 0); ?>
                                <div class="stat-bar-baris">
                                    <span class="stat-bar-label" title="<?= htmlspecialchars($labelRatingStat[$b]) ?>"><?= $b ?> &#9733; <?= htmlspecialchars($labelRatingStat[$b]) ?></span>
                                    <span class="stat-bar-jalur"><span class="stat-bar-isi bintang" style="--lebar:<?= $persen($jml, max(1, $maksBintang)) ?>%"></span></span>
                                    <span class="stat-bar-nilai"><?= $jml ?> (<?= $angka1($persen($jml, (int)$rt['jumlah'])) ?>%)</span>
                                </div>
                            <?php endfor; ?>
                            <?php if ((int)$rt['belum_dinilai'] > 0): ?>
                                <p class="stat-kosong" style="margin-top:12px;"><?= (int)$rt['belum_dinilai'] ?> pengaduan selesai belum dinilai siswa.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="stat-panel">
                        <h4>Rata-rata Rating per Admin</h4>
                        <?php if (empty($rt['per_admin'])): ?>
                            <p class="stat-kosong">Belum ada penilaian untuk admin.</p>
                        <?php else: ?>
                            <div class="stat-tabel-responsif">
                                <table class="stat-tabel">
                                    <thead>
                                        <tr><th>Admin</th><th>Rata-rata</th><th>Ulasan</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rt['per_admin'] as $ad): ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars($ad['nama']) ?>
                                                    <?php if (!empty($ad['role'])): ?><small><?= htmlspecialchars($ad['role']) ?></small><?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= $angka1($ad['rata']) ?></strong>
                                                    <span class="bintang-skala" style="--isi:<?= $isiBintang($ad['rata']) ?>" role="img" aria-label="<?= $angka1($ad['rata']) ?> dari 5 bintang">
                                                        <span class="bintang-skala-isi" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span><span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                                    </span>
                                                </td>
                                                <td><?= (int)$ad['jumlah'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="stat-panel">
                        <h4>Komentar Terbaru dari Siswa</h4>
                        <?php if (empty($rt['komentar'])): ?>
                            <p class="stat-kosong">Belum ada komentar.</p>
                        <?php else: ?>
                            <ul class="stat-komentar">
                                <?php foreach ($rt['komentar'] as $km): ?>
                                    <li>
                                        <div class="stat-komentar-meta">
                                            <span class="bintang-skala" style="--isi:<?= $isiBintang($km['rating']) ?>" role="img" aria-label="<?= (int)$km['rating'] ?> dari 5 bintang">
                                                <span class="bintang-skala-isi" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span><span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                                            </span>
                                            <span><?= htmlspecialchars($km['nama_siswa'] ?? '-') ?> &rarr; <?= !empty($km['nama_admin']) ? htmlspecialchars($km['nama_admin']) : 'admin' ?></span>
                                            <span><?= htmlspecialchars(M_Aspirasi::tglWib($km['tgl_rating'] ?? '')) ?></span>
                                        </div>
                                        <p><?= htmlspecialchars($km['komentar_rating'] ?? '') ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                </div>
            </section>

    </div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>

</html>
