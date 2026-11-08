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

// Siswa: "Histori Aspirasi" (Selesai) milik siswa yang login.
// List Aspirasi (Menunggu & Proses) ditangani oleh c_list_aspirasi_siswa.php.
$list_aspirasi = $modelAspirasi->getAspirasiByNis($nis, M_Aspirasi::STATUS_HISTORI);

require_once '../VIEWS/v_histori_aspirasi_siswa.php';
