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

// Token anti-CSRF untuk tombol hapus (form POST).
if (empty($_SESSION['csrf_hapus'])) {
    $_SESSION['csrf_hapus'] = bin2hex(random_bytes(16));
}

// NIS = angka 1..2147483647 (kolom siswa.nis bertipe INT); nama maks. 100, kelas maks. 15.
function nisValid($nis) {
    return ctype_digit($nis) && strlen($nis) <= 10 && (int)$nis > 0 && (int)$nis <= 2147483647;
}

function panjangValid($namaSiswa, $kelas, $password = '') {
    return mb_strlen($namaSiswa) <= 100 && mb_strlen($kelas) <= 15 && strlen($password) <= 72;
}

if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $namaSiswa = trim($_POST['nama_siswa'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nis === '' || $namaSiswa === '' || $kelas === '' || !M_Siswa::jurusanValid($jurusan) || !in_array($jenisKelamin, ['L', 'P'], true) || $password === '' || !panjangValid($namaSiswa, $kelas, $password)) {
        header('Location: c_list_siswa_admin.php?status=data_tidak_valid');
        exit();
    }
    if (!nisValid($nis)) {
        header('Location: c_list_siswa_admin.php?status=nis_tidak_valid');
        exit();
    }
    if ($modelSiswa->nisSudahTerdaftar($nis)) {
        header('Location: c_list_siswa_admin.php?status=nis_sudah_terdaftar');
        exit();
    }

    $berhasil = $modelSiswa->tambahSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $password);
    header('Location: c_list_siswa_admin.php' . ($berhasil ? '' : '?status=gagal'));
    exit();
}

if ($aksi === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // NIS tidak bisa diubah (seperti password): hanya dipakai sebagai kunci data yang diedit.
    // Kolom "nis" dari form (bila ada) sengaja diabaikan, jadi NIS tidak bisa diubah lewat request manual.
    $nis = trim($_POST['nis_lama'] ?? '');
    $namaSiswa = trim($_POST['nama_siswa'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');

    if ($nis === '' || $namaSiswa === '' || $kelas === '' || !M_Siswa::jurusanValid($jurusan) || !in_array($jenisKelamin, ['L', 'P'], true) || !panjangValid($namaSiswa, $kelas)) {
        header('Location: c_list_siswa_admin.php?status=data_tidak_valid');
        exit();
    }
    if (!nisValid($nis) || !$modelSiswa->nisSudahTerdaftar($nis)) {
        header('Location: c_list_siswa_admin.php?status=nis_tidak_valid');
        exit();
    }

    $berhasil = $modelSiswa->updateSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin);
    header('Location: c_list_siswa_admin.php' . ($berhasil ? '' : '?status=gagal'));
    exit();
}

// Hapus siswa hanya lewat POST + token (tidak lagi lewat link GET).
if ($aksi === 'hapus') {
    $csrfKirim = is_string($_POST['csrf'] ?? null) ? $_POST['csrf'] : '';
    $nis = is_string($_POST['nis'] ?? null) ? trim($_POST['nis']) : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals((string)$_SESSION['csrf_hapus'], $csrfKirim) && $nis !== '') {
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
