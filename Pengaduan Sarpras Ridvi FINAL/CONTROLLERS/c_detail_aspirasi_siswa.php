<?php
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (!isset($_SESSION['nis']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$id_input = (int)($_GET['id'] ?? 0);
$modelAspirasi = new M_Aspirasi($conn);
$detail = $id_input > 0 ? $modelAspirasi->getAspirasiByIdForNis($id_input, $_SESSION['nis']) : null;

if (!$detail) {
    header('Location: c_beranda_siswa.php?page=list');
    exit();
}

$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$notifikasi = $modelAspirasi->getNotifikasiSiswa($_SESSION['nis']);
$kelas = $_SESSION['kelas'] ?? '-';
require_once '../VIEWS/v_detail_aspirasi_siswa.php';
?>
