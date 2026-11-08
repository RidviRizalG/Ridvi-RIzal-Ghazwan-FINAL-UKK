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

// Validasi + simpan bukti (foto atau video). Mengembalikan nama file,
// atau null dan $kode berisi alasannya.
function simpanBukti($berkas, &$kode) {
    $maksFoto = 5 * 1024 * 1024;   // 5 MB
    $maksVideo = 9 * 1024 * 1024;  // 9 MB
    $tipeFoto = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tipeVideo = ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/quicktime' => 'mov', 'video/3gpp' => '3gp'];

    if (($berkas['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($berkas['tmp_name'])) {
        $kode = 'bukti_gagal';
        return null;
    }

    // Cek isi file yang sebenarnya, bukan ekstensi / tipe dari browser.
    $tipe = '';
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $tipe = (string)finfo_file($fi, $berkas['tmp_name']);
        finfo_close($fi);
    }
    if ($tipe === '') {
        $info = @getimagesize($berkas['tmp_name']);
        $tipe = $info['mime'] ?? '';
    }
    // Video 3GP kadang terbaca sebagai video/3gp atau audio/3gpp.
    if (in_array($tipe, ['video/3gp', 'audio/3gpp'], true)) {
        $tipe = 'video/3gpp';
    }

    if (isset($tipeFoto[$tipe])) {
        $ekstensi = $tipeFoto[$tipe];
        $maks = $maksFoto;
        // Foto harus benar-benar gambar yang bisa dibaca.
        if (!@getimagesize($berkas['tmp_name'])) {
            $kode = 'bukti_tidak_valid';
            return null;
        }
    } elseif (isset($tipeVideo[$tipe])) {
        $ekstensi = $tipeVideo[$tipe];
        $maks = $maksVideo;
    } else {
        $kode = 'bukti_tidak_valid';
        return null;
    }

    if ($berkas['size'] > $maks) {
        $kode = 'bukti_terlalu_besar';
        return null;
    }

    $folder = __DIR__ . '/../UPLOADS/aspirasi/';
    if (!is_dir($folder) && !@mkdir($folder, 0755, true)) {
        $kode = 'bukti_gagal';
        return null;
    }

    $nama = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ekstensi;
    if (!move_uploaded_file($berkas['tmp_name'], $folder . $nama)) {
        $kode = 'bukti_gagal';
        return null;
    }
    return $nama;
}

// Siswa: mengirim aspirasi / pengaduan baru (dari form pengaduan).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Bila ukuran kiriman melebihi batas server, PHP mengosongkan $_POST dan $_FILES.
    if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        header('Location: c_beranda_siswa.php?page=form&status=bukti_terlalu_besar');
        exit();
    }

    $id_kategori = trim($_POST['id_kategori'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $ket = trim($_POST['ket'] ?? '');

    if ($nis === '' || $id_kategori === '' || $lokasi === '' || $ket === '') {
        header('Location: c_beranda_siswa.php?page=form&status=data_tidak_lengkap');
        exit();
    }

    // Kategori harus angka; lokasi maks. 50 dan keterangan maks. 255 karakter (sesuai kolom database).
    if (!ctype_digit($id_kategori) || mb_strlen($lokasi) > 50 || mb_strlen($ket) > 255) {
        header('Location: c_beranda_siswa.php?page=form&status=data_tidak_lengkap');
        exit();
    }

    // Pengaduan identik yang baru saja masuk (klik ganda / kirim ulang): anggap sudah berhasil, jangan disimpan lagi.
    if ($modelAspirasi->adaPengaduanDobel($nis, $id_kategori, $lokasi, $ket)) {
        header('Location: c_beranda_siswa.php?page=dashboard&status=berhasil');
        exit();
    }

    // Bukti (foto atau video) WAJIB: dari kamera ("bukti_foto" / "bukti_video") atau dari file ("bukti_file").
    $berkas = null;
    foreach (['bukti_foto', 'bukti_video', 'bukti_file'] as $nama) {
        if (isset($_FILES[$nama]) && ($_FILES[$nama]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $berkas = $_FILES[$nama];
            break;
        }
    }

    if ($berkas === null) {
        header('Location: c_beranda_siswa.php?page=form&status=bukti_wajib');
        exit();
    }
    if (in_array($berkas['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
        header('Location: c_beranda_siswa.php?page=form&status=bukti_terlalu_besar');
        exit();
    }

    $namaFoto = simpanBukti($berkas, $kodeError);
    if ($namaFoto === null) {
        header('Location: c_beranda_siswa.php?page=form&status=' . $kodeError);
        exit();
    }

    if ($modelAspirasi->tambahAspirasi($nis, $id_kategori, $lokasi, $ket, $namaFoto)) {
        header('Location: c_beranda_siswa.php?page=dashboard&status=berhasil');
        exit();
    }
    // Gagal menyimpan ke database: hapus bukti yang sudah terlanjur tersimpan.
    @unlink(__DIR__ . '/../UPLOADS/aspirasi/' . $namaFoto);

    header('Location: c_beranda_siswa.php?page=form&status=gagal');
    exit();
}

header('Location: c_beranda_siswa.php?page=dashboard');
exit();
