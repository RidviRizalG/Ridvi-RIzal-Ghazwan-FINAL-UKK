<?php
// Khusus fitur Admin: "List Aspirasi" (status Menunggu & Proses) dan, lewat
// ?mode=histori, "Histori Aspirasi" (status Selesai). Keduanya memakai
// filter yang sama: tanggal, kategori, nama siswa, dan kelas.
// Pemberian status dan feedback ditangani oleh c_feedback_admin.php.
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$modelAspirasi = new M_Aspirasi($conn);

// 'list' (default) atau 'histori' (lewat ?mode=histori).
$mode = ($_GET['mode'] ?? '') === 'histori' ? 'histori' : 'list';
$statusTampil = $mode === 'histori' ? M_Aspirasi::STATUS_HISTORI : M_Aspirasi::STATUS_LIST;
$controllerIni = 'c_list_aspirasi_admin.php';
// Awalan query agar mode histori ikut terbawa di link reset & pagination.
$modeQuery = $mode === 'histori' ? 'mode=histori' : '';

$filter = M_Aspirasi::bacaFilter($_GET);
$filterQuery = M_Aspirasi::filterQuery($filter);
$adaFilter = M_Aspirasi::adaFilter($filter);

$aspirasiPage = max(1, (int)($_GET['aspirasi_page'] ?? 1));
$aspirasiLimit = 10;
$totalAspirasi = $modelAspirasi->countAspirasiFiltered($statusTampil, $filter);
$totalAspirasiPages = max(1, (int)ceil($totalAspirasi / $aspirasiLimit));

if ($aspirasiPage > $totalAspirasiPages) {
    $aspirasiPage = $totalAspirasiPages;
}

$aspirasiOffset = ($aspirasiPage - 1) * $aspirasiLimit;
$list_aspirasi = $modelAspirasi->getAspirasiFiltered($statusTampil, $filter, $aspirasiLimit, $aspirasiOffset);

$list_kategori = $modelAspirasi->getKategori();
$list_kelas = $modelAspirasi->getDaftarKelas();

$nama_admin = $_SESSION['nama_admin'] ?? 'Admin';
$role_admin = $_SESSION['role_admin'] ?? 'Admin';

require_once $mode === 'histori'
    ? '../VIEWS/v_histori_aspirasi_admin.php'
    : '../VIEWS/v_list_aspirasi_admin.php';
?>
