<?php
require_once __DIR__ . '/../MODELS/m_koneksi.php';
require_once __DIR__ . '/../MODELS/m_siswa.php';

$modelSiswa = new M_Siswa($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_GET['aksi'] ?? '') !== 'daftar') {
    header('Location: ../VIEWS/v_registrasi_siswa.php');
    exit();
}

$nis = trim($_POST['nis'] ?? '');
$namaSiswa = trim($_POST['nama_siswa'] ?? '');
$kelas = trim($_POST['kelas'] ?? '');
$jurusan = trim($_POST['jurusan'] ?? '');
$jenisKelamin = $_POST['jenis_kelamin'] ?? '';
$password = trim($_POST['password'] ?? '');

if ($nis === '' || $namaSiswa === '' || $kelas === '' || !M_Siswa::jurusanValid($jurusan) || $password === '' || !in_array($jenisKelamin, ['L', 'P'], true)) {
    header('Location: ../VIEWS/v_registrasi_siswa.php?status=data_tidak_lengkap');
    exit();
}

if (!ctype_digit($nis)) {
    header('Location: ../VIEWS/v_registrasi_siswa.php?status=nis_tidak_valid');
    exit();
}

// Kolom siswa.nis bertipe INT (maks. 2147483647); nama maks. 100, kelas maks. 15, password maks. 72 karakter.
if (strlen($nis) > 10 || (int)$nis <= 0 || (int)$nis > 2147483647) {
    header('Location: ../VIEWS/v_registrasi_siswa.php?status=nis_tidak_valid');
    exit();
}

if (mb_strlen($namaSiswa) > 100 || mb_strlen($kelas) > 15 || strlen($password) > 72) {
    header('Location: ../VIEWS/v_registrasi_siswa.php?status=data_terlalu_panjang');
    exit();
}

if ($modelSiswa->nisSudahTerdaftar($nis)) {
    header('Location: ../VIEWS/v_registrasi_siswa.php?status=nis_sudah_terdaftar');
    exit();
}

if ($modelSiswa->tambahSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $password)) {
    header('Location: ../VIEWS/v_login.php?status=registrasi_berhasil');
    exit();
}

header('Location: ../VIEWS/v_registrasi_siswa.php?status=gagal');
exit();
?>
