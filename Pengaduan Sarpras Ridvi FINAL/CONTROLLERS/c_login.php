<?php
session_start();

require_once '../MODELS/m_koneksi.php';
require_once '../MODELS/m_admin.php';
require_once '../MODELS/m_siswa.php';

$modelAdmin = new M_Admin($conn);
$modelSiswa = new M_Siswa($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $siswa = $modelSiswa->getByNisPassword($login, $password);
    if ($siswa) {
        session_regenerate_id(true);
        $_SESSION['role'] = 'siswa';
        $_SESSION['nis'] = $siswa['nis'];
        $_SESSION['nama_siswa'] = $siswa['nama_siswa'];
        $_SESSION['kelas'] = $siswa['kelas'];

        header("Location: c_beranda_siswa.php");
        exit();
    }

    $admin = $modelAdmin->getByUsernamePassword($login, $password);
    if ($admin) {
        session_regenerate_id(true);
        $_SESSION['role'] = 'admin';
        $_SESSION['id_admin'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['nama_admin'] = $admin['nama_admin'];
        $_SESSION['role_admin'] = $admin['role'];

        header("Location: c_beranda_admin.php");
        exit();
    }

    echo "<script>
            alert('NIS/Username atau Password yang Anda masukkan salah!');
            window.location.href = '../VIEWS/v_login.php';
          </script>";
    exit();

} else {

    header("Location: ../VIEWS/v_login.php");
    exit();

}
?>