<?php
class M_Siswa {
    /** Jurusan yang tersedia (singkatan => nama lengkap yang disimpan di database). */
    const JURUSAN = [
        'RPL'   => 'Rekayasa Perangkat Lunak',
        'MPLB'  => 'Manajemen Perkantoran dan Lembaga Bisnis',
        'AKL'   => 'Akuntansi Keuangan dan Lembaga',
        'BC'    => 'Broadcasting',
        'DKV'   => 'Desain Komunikasi Visual',
        'KUL'   => 'Kuliner',
    ];

    public static function jurusanValid($jurusan) {
        return in_array($jurusan, self::JURUSAN, true);
    }

    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function countSiswa($keyword = '') {
        $keyword = mysqli_real_escape_string($this->db, $keyword);
        $query = "SELECT COUNT(*) AS total FROM siswa";

        if ($keyword !== '') {
            $query .= " WHERE nis LIKE '%$keyword%' OR nama_siswa LIKE '%$keyword%' OR kelas LIKE '%$keyword%' OR jurusan LIKE '%$keyword%'";
        }

        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }

    public function getSiswa($limit = null, $offset = 0, $keyword = '') {
        $keyword = mysqli_real_escape_string($this->db, $keyword);
        $query = "SELECT * FROM siswa";

        if ($keyword !== '') {
            $query .= " WHERE nis LIKE '%$keyword%' OR nama_siswa LIKE '%$keyword%' OR kelas LIKE '%$keyword%' OR jurusan LIKE '%$keyword%'";
        }

        $query .= " ORDER BY CAST(nis AS UNSIGNED) ASC, nis ASC";

        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $result = mysqli_query($this->db, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getSiswaByNis($nis) {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM siswa WHERE nis = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, 's', $nis);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        $siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $siswa ?: null;
    }

    public function tambahSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $password) {
        $stmt = mysqli_prepare(
            $this->db,
            'INSERT INTO siswa (nis, nama_siswa, kelas, jurusan, jenis_kelamin, password) VALUES (?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ssssss', $nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $password);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function nisSudahTerdaftar($nis) {
        $stmt = mysqli_prepare($this->db, 'SELECT nis FROM siswa WHERE nis = ? LIMIT 1');
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 's', $nis);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $ada = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $ada;
    }

    public function getByNisPassword($nis, $password) {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM siswa WHERE nis = ? AND password = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, 'ss', $nis, $password);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        $result = mysqli_stmt_get_result($stmt);
        $siswa = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $siswa ?: null;
    }

    public function updateSiswa($nisLama, $nisBaru, $namaSiswa, $kelas, $jurusan, $jenisKelamin) {
        $nisLama = mysqli_real_escape_string($this->db, $nisLama);
        $nisBaru = mysqli_real_escape_string($this->db, $nisBaru);
        $namaSiswa = mysqli_real_escape_string($this->db, $namaSiswa);
        $kelas = mysqli_real_escape_string($this->db, $kelas);
        $jurusan = mysqli_real_escape_string($this->db, $jurusan);
        $jenisKelamin = mysqli_real_escape_string($this->db, $jenisKelamin);

        $query = "UPDATE siswa SET
                    nis = '$nisBaru',
                    nama_siswa = '$namaSiswa',
                    kelas = '$kelas',
                    jurusan = '$jurusan',
                    jenis_kelamin = '$jenisKelamin'
                  WHERE nis = '$nisLama'";
        return mysqli_query($this->db, $query);
    }

    public function hapusSiswa($nis) {
        $stmt = mysqli_prepare($this->db, 'DELETE FROM siswa WHERE nis = ?');
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 's', $nis);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }
}
?>
