<?php
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_siswa.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$modelSiswa = new M_Siswa($conn);
$aksi = $_GET['aksi'] ?? 'list';

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $namaSiswa = trim($_POST['nama_siswa'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nis !== '' && $namaSiswa !== '' && $kelas !== '' && M_Siswa::jurusanValid($jurusan) && in_array($jenisKelamin, ['L', 'P'], true) && $password !== '') {
        $modelSiswa->tambahSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $password);
    }

    header('Location: c_list_siswa_admin.php');
    exit();
}

if ($aksi === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisLama = trim($_POST['nis_lama'] ?? '');
    $nis = trim($_POST['nis'] ?? '');
    $namaSiswa = trim($_POST['nama_siswa'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');

    if ($nisLama !== '' && $nis !== '' && $namaSiswa !== '' && $kelas !== '' && M_Siswa::jurusanValid($jurusan) && in_array($jenisKelamin, ['L', 'P'], true)) {
        $modelSiswa->updateSiswa($nisLama, $nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin);
    }

    header('Location: c_list_siswa_admin.php');
    exit();
}

if ($aksi === 'hapus') {
    $nis = trim($_GET['nis'] ?? '');
    if ($nis !== '') {
        $modelSiswa->hapusSiswa($nis);
    }
    header('Location: c_list_siswa_admin.php');
    exit();
}

// ---------- Tampilan: list siswa, form tambah, form edit ----------
$siswaKeyword = trim($_GET['siswa_keyword'] ?? '');
$siswaPage = max(1, (int)($_GET['siswa_page'] ?? 1));
$siswaLimit = 10;
$totalSiswa = $modelSiswa->countSiswa($siswaKeyword);
$totalSiswaPages = max(1, (int)ceil($totalSiswa / $siswaLimit));

if ($siswaPage > $totalSiswaPages) {
    $siswaPage = $totalSiswaPages;
}

$siswaOffset = ($siswaPage - 1) * $siswaLimit;
$list_siswa = $modelSiswa->getSiswa($siswaLimit, $siswaOffset, $siswaKeyword);

$editing_siswa = null;
if ($aksi === 'edit') {
    $nis = trim($_GET['nis'] ?? '');
    $editing_siswa = $nis !== '' ? $modelSiswa->getSiswaByNis($nis) : null;
    if (!$editing_siswa) {
        header('Location: c_list_siswa_admin.php');
        exit();
    }
}

$showEditForm = ($aksi === 'edit');
$showAddForm = ($aksi === 'tambah');

$nama_admin = $_SESSION['nama_admin'] ?? 'Admin';
$role_admin = $_SESSION['role_admin'] ?? 'Admin';

require_once '../VIEWS/v_list_siswa_admin.php';
?>
