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

// Siswa: mengirim aspirasi / pengaduan baru (dari form pengaduan).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_kategori = trim($_POST['id_kategori'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $ket = trim($_POST['ket'] ?? '');

    if ($nis === '' || $id_kategori === '' || $lokasi === '' || $ket === '') {
        header('Location: c_beranda_siswa.php?page=form&status=data_tidak_lengkap');
        exit();
    }

    if ($modelAspirasi->tambahAspirasi($nis, $id_kategori, $lokasi, $ket)) {
        header('Location: c_beranda_siswa.php?page=dashboard&status=berhasil');
        exit();
    }

    header('Location: c_beranda_siswa.php?page=form&status=gagal');
    exit();
}

header('Location: c_beranda_siswa.php?page=dashboard');
exit();
