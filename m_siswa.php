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

    /** Membuat hash password (bcrypt bawaan PHP). Password polos tidak pernah disimpan. */
    public static function hashPassword($password) {
        return password_hash((string)$password, PASSWORD_DEFAULT);
    }

    /** True bila nilai tersimpan sudah berupa hash (bukan password polos data lama). */
    public static function sudahHash($tersimpan) {
        return (bool)preg_match('/^\$(2[abxy]|argon2id?)\$/', (string)$tersimpan);
    }

    /** Cocokkan input dengan nilai tersimpan: hash, atau password polos (data lama sebelum migrasi). */
    public static function passwordCocok($input, $tersimpan) {
        if (self::sudahHash($tersimpan)) {
            return password_verify((string)$input, (string)$tersimpan);
        }
        return hash_equals((string)$tersimpan, (string)$input);
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
        $hash = self::hashPassword($password);
        mysqli_stmt_bind_param($stmt, 'ssssss', $nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin, $hash);
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
        // NIS selalu angka; ini juga mencegah teks seperti "201abc" terbaca sebagai 201.
        if (!ctype_digit((string)$nis)) {
            return null;
        }
        $siswa = $this->getSiswaByNis($nis);
        if (!$siswa || !self::passwordCocok($password, $siswa['password'])) {
            return null;
        }

        // Migrasi otomatis: password polos lama (atau hash usang) diganti hash baru saat login berhasil.
        if (!self::sudahHash($siswa['password']) || password_needs_rehash($siswa['password'], PASSWORD_DEFAULT)) {
            $stmt = mysqli_prepare($this->db, 'UPDATE siswa SET password = ? WHERE nis = ?');
            if ($stmt) {
                $hash = self::hashPassword($password);
                mysqli_stmt_bind_param($stmt, 'ss', $hash, $siswa['nis']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        return $siswa;
    }

    /**
     * Ubah data siswa. NIS (dan password) TIDAK ikut diubah: NIS hanya dipakai sebagai kunci WHERE.
     */
    public function updateSiswa($nis, $namaSiswa, $kelas, $jurusan, $jenisKelamin) {
        $stmt = mysqli_prepare(
            $this->db,
            'UPDATE siswa SET nama_siswa = ?, kelas = ?, jurusan = ?, jenis_kelamin = ? WHERE nis = ?'
        );
        if (!$stmt) {
            return false;
        }
        $nis = (string)$nis;
        mysqli_stmt_bind_param($stmt, 'sssss', $namaSiswa, $kelas, $jurusan, $jenisKelamin, $nis);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
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
