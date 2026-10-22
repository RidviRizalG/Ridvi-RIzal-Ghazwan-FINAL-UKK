<?php
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (!isset($_SESSION['nis']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header("Location: ../VIEWS/v_login.php");
    exit();
}

$modelAspirasi = new M_Aspirasi($conn);

$nis = $_SESSION['nis'];
$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$kelas = $_SESSION['kelas'] ?? '-';

$page = $_GET['page'] ?? 'dashboard';
$notifikasi = $modelAspirasi->getNotifikasiSiswa($_SESSION['nis']);

if ($page === 'form') {
    // Kategori hanya dibutuhkan oleh form pengaduan.
    $list_kategori = $modelAspirasi->getKategori();
    require_once '../VIEWS/v_form_aspirasi_siswa.php';

} elseif ($page === 'list') {
    // List aspirasi (Menunggu & Proses) ditangani oleh c_list_aspirasi_siswa.php.
    header("Location: c_list_aspirasi_siswa.php");
    exit();

} elseif ($page === 'histori') {
    // Histori aspirasi (Selesai) = c_list_aspirasi_siswa.php?mode=histori.
    header("Location: c_list_aspirasi_siswa.php?mode=histori");
    exit();

} else {
    require_once '../VIEWS/v_beranda_siswa.php';
}
?>
