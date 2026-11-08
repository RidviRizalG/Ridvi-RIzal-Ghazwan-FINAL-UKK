<?php
// Khusus fitur Admin: "Histori Aspirasi" (status Selesai) dengan
// filter tanggal, kategori, nama siswa, dan kelas.
// List Aspirasi (Menunggu & Proses) ditangani oleh c_list_aspirasi_admin.php.
// Pemberian status dan feedback ditangani oleh c_feedback_admin.php.
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$modelAspirasi = new M_Aspirasi($conn);

$statusTampil = M_Aspirasi::STATUS_HISTORI;
$controllerIni = 'c_histori_aspirasi_admin.php';

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

require_once '../VIEWS/v_histori_aspirasi_admin.php';
?>
