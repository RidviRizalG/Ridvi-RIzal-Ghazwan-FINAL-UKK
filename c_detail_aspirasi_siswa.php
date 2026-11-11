<?php
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (!isset($_SESSION['nis']) || ($_SESSION['role'] ?? '') !== 'siswa') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$modelAspirasi = new M_Aspirasi($conn);

$detail = null;
$pesan = '';
$ratingInput = 0;      // isian yang dikembalikan ke form bila validasi gagal
$komentarInput = '';

// ---------- Simpan rating + komentar kinerja admin ----------
// Hanya untuk pengaduan milik siswa yang login, berstatus Selesai, dan belum pernah dinilai.
// Komentar wajib diisi untuk bintang berapa pun (1 sampai 5).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = (int)($_POST['id_input'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $komentar = M_Aspirasi::bersihkanKomentar(is_string($_POST['komentar'] ?? null) ? $_POST['komentar'] : '');
    $detail = $id_post > 0 ? $modelAspirasi->getAspirasiByIdForNis($id_post, $_SESSION['nis']) : null;

    if (!$detail) {
        header('Location: c_beranda_siswa.php?page=list');
        exit();
    }

    $kembali = 'c_detail_aspirasi_siswa.php?id=' . $id_post . '&pesan=';
    if (($detail['status'] ?? '') !== 'Selesai') {
        header('Location: ' . $kembali . 'belum_selesai');
        exit();
    }
    if ($detail['rating'] !== null) {
        header('Location: ' . $kembali . 'sudah_dinilai#penilaian');
        exit();
    }

    $ratingInput = $rating;
    $komentarInput = mb_substr($komentar, 0, M_Aspirasi::KOMENTAR_MAKS);
    if ($rating < M_Aspirasi::RATING_MIN || $rating > M_Aspirasi::RATING_MAKS) {
        $pesan = 'rating_kosong';
    } elseif ($komentar === '') {
        $pesan = 'komentar_kosong';
    } elseif (mb_strlen($komentar) > M_Aspirasi::KOMENTAR_MAKS) {
        $pesan = 'komentar_panjang';
    } elseif ($modelAspirasi->simpanRating($id_post, $_SESSION['nis'], $rating, $komentar)) {
        header('Location: ' . $kembali . 'rating_ok#penilaian');
        exit();
    } else {
        $pesan = 'gagal';
    }
    // Validasi gagal: halaman dirender ulang di bawah dengan isian tetap terjaga (tanpa redirect).
}

// ---------- Tampilkan detail ----------
if ($detail === null) {
    $id_input = (int)($_GET['id'] ?? 0);
    $detail = $id_input > 0 ? $modelAspirasi->getAspirasiByIdForNis($id_input, $_SESSION['nis']) : null;

    if (!$detail) {
        header('Location: c_beranda_siswa.php?page=list');
        exit();
    }
    $pesan = is_string($_GET['pesan'] ?? null) ? $_GET['pesan'] : '';
}

$nama_siswa = $_SESSION['nama_siswa'] ?? 'Siswa';
$notifikasi = $modelAspirasi->getNotifikasiSiswa($_SESSION['nis']);
$kelas = $_SESSION['kelas'] ?? '-';
require_once '../VIEWS/v_detail_aspirasi_siswa.php';
?>
