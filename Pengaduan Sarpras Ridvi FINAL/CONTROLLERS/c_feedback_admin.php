<?php
// Khusus fitur Admin: form untuk memberi status dan feedback pada satu aspirasi.
session_start();
require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_aspirasi.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../VIEWS/v_login.php');
    exit();
}

$modelAspirasi = new M_Aspirasi($conn);

// Halaman daftar aspirasi yang dituju setelah selesai / batal.
// $dari = 'list' atau 'histori' (asal admin membuka form), filter ikut dibawa
// supaya admin kembali ke tampilan yang sama.
function urlDaftarAspirasi($halaman, $dari = 'list', $filterQuery = '') {
    $q = $filterQuery;
    if ((int)$halaman > 1) {
        $q .= ($q !== '' ? '&' : '') . 'aspirasi_page=' . (int)$halaman;
    }
    if ($dari === 'histori') {
        $q = 'mode=histori' . ($q !== '' ? '&' . $q : '');
    }
    return 'c_list_aspirasi_admin.php' . ($q !== '' ? '?' . $q : '');
}

// Asal halaman + filter dari request (sudah dibersihkan).
function bacaAsal($src) {
    $dari = ($src['dari_halaman'] ?? '') === 'histori' ? 'histori' : 'list';
    return [$dari, M_Aspirasi::filterQuery(M_Aspirasi::bacaFilter($src))];
}

// ---------- Simpan status + feedback ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_input = (int)($_POST['id_input'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');
    $id_admin = (int)($_SESSION['id_admin'] ?? 0);
    $aspirasiPage = max(1, (int)($_POST['aspirasi_page'] ?? 1));
    [$dari, $filterQuery] = bacaAsal($_POST);

    if ($id_input <= 0) {
        header('Location: ' . urlDaftarAspirasi($aspirasiPage, $dari, $filterQuery));
        exit();
    }

    $formUrl = 'c_feedback_admin.php?id=' . $id_input . '&aspirasi_page=' . $aspirasiPage
             . '&dari_halaman=' . $dari . ($filterQuery !== '' ? '&' . $filterQuery : '');

    if (!in_array($status, ['Menunggu', 'Proses', 'Selesai'], true)) {
        header('Location: ' . $formUrl . '&pesan=status_tidak_valid');
        exit();
    }

    // Minimal salah satu harus diisi/diubah: status dibedakan dari yang tersimpan, atau feedback baru.
    // Bila hanya salah satu yang diisi, tetap boleh disimpan.
    $saatIni = $modelAspirasi->getAspirasiById($id_input);
    if (!$saatIni) {
        header('Location: ' . urlDaftarAspirasi($aspirasiPage, $dari, $filterQuery));
        exit();
    }
    $statusBerubah = $status !== ($saatIni['status'] ?? 'Menunggu');
    $feedbackTerisi = $feedback !== '' && $feedback !== trim((string)($saatIni['feedback'] ?? ''));

    if (!$statusBerubah && !$feedbackTerisi) {
        // Belum ada feedback sama sekali -> minta isi feedback; kalau sudah ada, minta ubah status.
        $pesanError = trim((string)($saatIni['feedback'] ?? '')) === '' ? 'isi_feedback' : 'ubah_status';
        header('Location: ' . $formUrl . '&pesan=' . $pesanError);
        exit();
    }

    if ($modelAspirasi->updateAspirasi($id_input, $status, $feedback, $id_admin)) {
        header('Location: ' . urlDaftarAspirasi($aspirasiPage, $dari, $filterQuery));
        exit();
    }

    header('Location: ' . $formUrl . '&pesan=gagal');
    exit();
}

// ---------- Tampilkan form ----------
$id_input = (int)($_GET['id'] ?? 0);
$aspirasiPage = max(1, (int)($_GET['aspirasi_page'] ?? 1));
[$dari, $filterQuery] = bacaAsal($_GET);
$detail = $id_input > 0 ? $modelAspirasi->getAspirasiById($id_input) : null;

if (!$detail) {
    header('Location: ' . urlDaftarAspirasi($aspirasiPage, $dari, $filterQuery));
    exit();
}

$pesan = $_GET['pesan'] ?? '';
$batalUrl = urlDaftarAspirasi($aspirasiPage, $dari, $filterQuery);

$nama_admin = $_SESSION['nama_admin'] ?? 'Admin';
$role_admin = $_SESSION['role_admin'] ?? 'Admin';

require_once '../VIEWS/v_form_feedback_admin.php';
?>
