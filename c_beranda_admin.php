<?php
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$nama_admin = $_SESSION['nama_admin'] ?? 'Admin';
$role_admin = $_SESSION['role_admin'] ?? 'Admin';

// Angka untuk dashboard statistik (total, status, kategori, bulanan, waktu selesai, rating).
$modelAspirasi = new M_Aspirasi($conn);
$stat = $modelAspirasi->getStatistikDashboard();

require_once '../VIEWS/v_beranda_admin.php';
