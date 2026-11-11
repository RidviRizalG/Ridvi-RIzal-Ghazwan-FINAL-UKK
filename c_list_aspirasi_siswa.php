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
$notifikasi = $modelAspirasi->getNotifikasiSiswa($_SESSION['nis']);

// Siswa: "List Aspirasi" (Menunggu & Proses) milik siswa yang login.
// Histori Aspirasi (Selesai) ditangani oleh c_histori_aspirasi_siswa.php.
$list_aspirasi = $modelAspirasi->getAspirasiByNis($nis, M_Aspirasi::STATUS_LIST);

require_once '../VIEWS/v_list_aspirasi_siswa.php';
