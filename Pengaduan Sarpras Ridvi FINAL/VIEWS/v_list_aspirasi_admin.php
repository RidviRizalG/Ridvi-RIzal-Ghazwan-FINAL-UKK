<?php
// Admin: List Aspirasi (status Menunggu & Proses).
// Dipanggil oleh CONTROLLERS/c_list_aspirasi_admin.php, yang menyediakan:
// $list_aspirasi, $totalAspirasi, $aspirasiPage, $totalAspirasiPages, $filter, $filterQuery,
// $adaFilter, $list_kategori, $list_kelas, $controllerIni, $modeQuery, $nama_admin, $role_admin.

$periodeLabel = [
    'semua'      => 'Semua waktu',
    'hari_ini'   => 'Hari ini',
    'kemarin'    => 'Kemarin',
    'minggu_ini' => 'Minggu ini',
    'bulan_ini'  => 'Bulan ini',
    'custom'     => 'Pilih tanggal sendiri',
];
$periodeIni = $filter['periode'];
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
    <link rel="stylesheet" href="../CSS/07-filter-aspirasi.css?v=<?= @filemtime(__DIR__ . '/../CSS/07-filter-aspirasi.css') ?>">
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

                <div class="menu">

                    <a
                        class=""
                        href="../CONTROLLERS/c_beranda_admin.php"
                        title="Dashboard"
                    >
                        <img src="../ASSETS/logo_dashboard.png" alt="Dashboard" style="width:24px;height:24px;object-fit:contain;display:block;">
                    </a>

                    <a
                        class="active"
                        href="../CONTROLLERS/c_list_aspirasi_admin.php"
                        title="List Aspirasi"
                    >
                        <img src="../ASSETS/detail_aspirasi.png" alt="List Aspirasi">
                    </a>

                    <a
                        class=""
                        href="../CONTROLLERS/c_list_aspirasi_admin.php?mode=histori"
                        title="Histori Aspirasi"
                    >
                        <img src="../ASSETS/historiaspirasi.png" alt="Histori Aspirasi">
                    </a>

                    <a
                        class=""
                        href="../CONTROLLERS/c_list_siswa_admin.php"
                        title="List Siswa"
                    >
                        <img src="../ASSETS/datasiswa.png" alt="List Siswa">
                    </a>

                </div>

                <small>Admin Panel</small>

            </div>

            <div class="card">

                <h3>List Aspirasi Siswa</h3>
                <p class="page-hint">
                    Aspirasi yang masih berstatus Menunggu atau Proses. Yang sudah Selesai dipindahkan ke Histori Aspirasi.
                </p>

                <!-- FILTER (sortir): tanggal, kategori, nama siswa, kelas -->
<form class="filter-bar" method="GET" action="../CONTROLLERS/<?= htmlspecialchars($controllerIni) ?>" autocomplete="off">
            <?php if ($modeQuery !== ''): ?><input type="hidden" name="mode" value="histori"><?php endif; ?>

            <div class="filter-field">
                <label for="f-periode">Tanggal</label>
                <select id="f-periode" name="periode">
                    <?php foreach ($periodeLabel as $nilai => $label): ?>
                        <option value="<?= $nilai ?>" <?= $periodeIni === $nilai ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-field filter-custom" id="f-custom" <?= $periodeIni === 'custom' ? '' : 'hidden' ?>>
                <label for="f-dari">Dari tanggal</label>
                <input type="date" id="f-dari" name="dari" value="<?= htmlspecialchars($filter['dari']) ?>">
            </div>

            <div class="filter-field filter-custom" id="f-custom2" <?= $periodeIni === 'custom' ? '' : 'hidden' ?>>
                <label for="f-sampai">Sampai tanggal</label>
                <input type="date" id="f-sampai" name="sampai" value="<?= htmlspecialchars($filter['sampai']) ?>">
            </div>

            <div class="filter-field">
                <label for="f-kategori">Kategori</label>
                <select id="f-kategori" name="kategori">
                    <option value="0">Semua kategori</option>
                    <?php foreach ($list_kategori as $kat): ?>
                        <option value="<?= (int)$kat['id_kategori'] ?>" <?= (int)$kat['id_kategori'] === $filter['kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['ket_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-field">
                <label for="f-nama">Nama siswa</label>
                <input type="text" id="f-nama" name="nama" maxlength="100" placeholder="Cari nama..." value="<?= htmlspecialchars($filter['nama']) ?>">
            </div>

            <div class="filter-field">
                <label for="f-kelas">Kelas</label>
                <select id="f-kelas" name="kelas">
                    <option value="">Semua kelas</option>
                    <?php foreach ($list_kelas as $kelasOpsi): ?>
                        <option value="<?= htmlspecialchars($kelasOpsi) ?>" <?= $kelasOpsi === $filter['kelas'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelasOpsi) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter" title="Terapkan sortir" aria-label="Terapkan sortir">
                    <img src="../ASSETS/sortir.png" alt="Sortir">
                </button>
                <?php if ($adaFilter): ?>
                    <a class="btn-filter btn-filter-reset" href="../CONTROLLERS/<?= htmlspecialchars($controllerIni . ($modeQuery !== '' ? '?' . $modeQuery : '')) ?>" title="Reset sortir" aria-label="Reset sortir">
                        <img src="../ASSETS/reset_search.png" alt="Reset">
                    </a>
                <?php endif; ?>
            </div>

        </form>

        <script>
        (function () {
            var periode = document.getElementById('f-periode');
            var custom = [document.getElementById('f-custom'), document.getElementById('f-custom2')];
            if (!periode) { return; }
            periode.addEventListener('change', function () {
                var tampil = periode.value === 'custom';
                custom.forEach(function (el) { el.hidden = !tampil; });
            });
        })();
        </script>

                <p class="filter-summary">
                    <?= (int)$totalAspirasi ?> aspirasi<?= $adaFilter ? ' sesuai filter' : '' ?>
                </p>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Feedback</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($list_aspirasi)): ?>

                                <tr>

                                    <td
                                        colspan="8"
                                        style="text-align:center;color:var(--teks-medium);padding:20px;"
                                    >
                                        <?php if ($adaFilter): ?>
                                            Tidak ada aspirasi yang sesuai dengan filter.
                                        <?php else: ?>
                                            Tidak ada aspirasi yang menunggu atau diproses.
                                        <?php endif; ?>
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

                                        <td data-label="Tanggal">
                                            <small>
                                                <?= date(
                                                    'd/m/Y H:i',
                                                    strtotime($row['tgl_pelaporan'])
                                                ) ?>
                                            </small>
                                        </td>

                                        <td data-label="Siswa">

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $row['nama_siswa'] ?? '-'
                                                ) ?>
                                            </strong>

                                            <br>

                                            <small>
                                                <?= htmlspecialchars(
                                                    $row['kelas'] ?? '-'
                                                ) ?>
                                            </small>

                                        </td>

                                        <td data-label="Kategori">
                                            <?= htmlspecialchars(
                                                $row['ket_kategori'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td data-label="Lokasi">
                                            <?= htmlspecialchars(
                                                $row['lokasi'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td data-label="Keterangan">
                                            <?= htmlspecialchars(
                                                $row['ket'] ?? '-'
                                            ) ?>
                                        </td>

                                        <td data-label="Status">

                                            <span class="badge <?= $badgeClass ?>">
                                                <?= htmlspecialchars(
                                                    $row['status'] ?? 'Menunggu'
                                                ) ?>
                                            </span>

                                        </td>

                                        <td data-label="Feedback">
                                            <?php $feedbackTeks = trim((string)($row['feedback'] ?? '')); ?>
                                            <?php if ($feedbackTeks !== ''): ?>
                                                <span style="white-space:pre-line;"><?= htmlspecialchars($feedbackTeks) ?></span>
                                            <?php else: ?>
                                                <small style="color:var(--teks-medium);font-style:italic;">Belum ada feedback</small>
                                            <?php endif; ?>
                                        </td>

                                        <td data-label="Aksi">
                                            <a
                                                class="detail-icon-btn"
                                                href="../CONTROLLERS/c_feedback_admin.php?id=<?= (int)$row['id_input'] ?>&aspirasi_page=<?= (int)$aspirasiPage ?>&dari_halaman=list<?= $filterQuery !== '' ? '&' . htmlspecialchars($filterQuery) : '' ?>"
                                                title="Beri Feedback"
                                            >
                                                <img src="../ASSETS/mengisi_feedback.png" alt="Beri Feedback">
                                            </a>
                                        </td>


                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <?php
                $urlHalaman = function ($n) use ($controllerIni, $filterQuery, $modeQuery) {
                    $q = ($modeQuery !== '' ? $modeQuery . '&' : '') . $filterQuery . ($filterQuery !== '' ? '&' : '') . 'aspirasi_page=' . (int)$n;
                    return '../CONTROLLERS/' . $controllerIni . '?' . htmlspecialchars($q);
                };
                ?>
                <div class="pagination" style="margin-top:14px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <?php if ($aspirasiPage > 1): ?>
                        <a class="btn-logout" href="<?= $urlHalaman($aspirasiPage - 1) ?>">Sebelumnya</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalAspirasiPages; $i++): ?>
                        <a class="btn-logout" style="<?= $i === $aspirasiPage ? 'background:#6b1d2f;color:#fff;' : '' ?>" href="<?= $urlHalaman($i) ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($aspirasiPage < $totalAspirasiPages): ?>
                        <a class="btn-logout" href="<?= $urlHalaman($aspirasiPage + 1) ?>">Berikutnya</a>
                    <?php endif; ?>
                </div>

            </div>

    </div>

<script src="../JS/theme.js?v=<?= @filemtime(__DIR__ . '/../JS/theme.js') ?>"></script>
</body>

</html>
