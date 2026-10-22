<?php
class M_Aspirasi {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function countAllAspirasi() {
        $query = "SELECT COUNT(*) AS total FROM input_aspirasi";
        $result = mysqli_query($this->db, $query);
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Daftar aspirasi milik satu siswa.
     * $statusList null = semua status; atau array mis. ['Menunggu', 'Proses'].
     */
    public function getAspirasiByNis($nis, $statusList = null) {
        // Ikat NIS sebagai string agar konsisten dengan nilai sesi/login.
        $query = "SELECT i.id_input, i.lokasi, i.ket, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback
                  FROM input_aspirasi i
                  LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                  LEFT JOIN aspirasi a ON i.id_input = a.id_input
                  WHERE CAST(i.nis AS CHAR) = ?";
        $types = 's';
        $params = [(string)$nis];

        if (is_array($statusList) && $statusList) {
            $query .= " AND COALESCE(a.status, 'Menunggu') IN (" . implode(',', array_fill(0, count($statusList), '?')) . ")";
            $types .= str_repeat('s', count($statusList));
            $params = array_merge($params, array_values($statusList));
        }

        $query .= " ORDER BY i.tgl_pelaporan DESC, i.id_input DESC";

        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return [];
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return [];
        }
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Detail satu pengaduan milik siswa tertentu (khusus halaman detail siswa).
     * NIS ikut di WHERE sehingga siswa tidak bisa membuka pengaduan siswa lain
     * hanya dengan mengganti id di URL.
     */
    public function getAspirasiByIdForNis($id_input, $nis) {
        $query = "SELECT i.id_input, i.lokasi, i.ket, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback,
                         a.updated_at AS tgl_feedback,
                         ad.nama_admin AS nama_admin_feedback
                  FROM input_aspirasi i
                  LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                  LEFT JOIN aspirasi a ON i.id_input = a.id_input
                  LEFT JOIN admin ad ON a.id_admin = ad.id_admin
                  WHERE i.id_input = ? AND CAST(i.nis AS CHAR) = ?
                  LIMIT 1";
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return null;
        }
        $id_input = (int)$id_input;
        $nis = (string)$nis;
        mysqli_stmt_bind_param($stmt, 'is', $id_input, $nis);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    // =====================================================
    // FILTER (SORTIR) ASPIRASI UNTUK ADMIN
    // =====================================================

    const PERIODE_VALID = ['semua', 'hari_ini', 'kemarin', 'minggu_ini', 'bulan_ini', 'custom'];

    /** Status yang tampil di "List Aspirasi" (belum tuntas). */
    const STATUS_LIST = ['Menunggu', 'Proses'];

    /** Status yang tampil di "Histori Aspirasi" (sudah tuntas). */
    const STATUS_HISTORI = ['Selesai'];

    /**
     * Membersihkan input filter dari $_GET / $_POST menjadi array yang aman.
     * Kunci: periode, dari, sampai, kategori, nama, kelas.
     */
    public static function bacaFilter(array $src) {
        $periode = (string)($src['periode'] ?? 'semua');
        if (!in_array($periode, self::PERIODE_VALID, true)) {
            $periode = 'semua';
        }

        $tanggal = function ($v) {
            $v = trim((string)$v);
            $d = DateTime::createFromFormat('!Y-m-d', $v);
            return ($d && $d->format('Y-m-d') === $v) ? $v : '';
        };
        $dari = $tanggal($src['dari'] ?? '');
        $sampai = $tanggal($src['sampai'] ?? '');
        if ($dari !== '' && $sampai !== '' && $dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        // Tanggal custom tanpa isian sama sekali tidak berarti apa-apa.
        if ($periode === 'custom' && $dari === '' && $sampai === '') {
            $periode = 'semua';
        }
        if ($periode !== 'custom') {
            $dari = '';
            $sampai = '';
        }

        return [
            'periode'  => $periode,
            'dari'     => $dari,
            'sampai'   => $sampai,
            'kategori' => max(0, (int)($src['kategori'] ?? 0)),
            'nama'     => mb_substr(trim((string)($src['nama'] ?? '')), 0, 100),
            'kelas'    => mb_substr(trim((string)($src['kelas'] ?? '')), 0, 15),
        ];
    }

    /** Query string (tanpa "?") berisi filter yang aktif saja; dipakai untuk link & pagination. */
    public static function filterQuery(array $filter) {
        $q = [];
        if ($filter['periode'] !== 'semua') {
            $q['periode'] = $filter['periode'];
        }
        if ($filter['dari'] !== '') {
            $q['dari'] = $filter['dari'];
        }
        if ($filter['sampai'] !== '') {
            $q['sampai'] = $filter['sampai'];
        }
        if ($filter['kategori'] > 0) {
            $q['kategori'] = $filter['kategori'];
        }
        if ($filter['nama'] !== '') {
            $q['nama'] = $filter['nama'];
        }
        if ($filter['kelas'] !== '') {
            $q['kelas'] = $filter['kelas'];
        }
        return http_build_query($q);
    }

    /** True bila ada filter aktif. */
    public static function adaFilter(array $filter) {
        return self::filterQuery($filter) !== '';
    }

    /**
     * Rentang waktu [awal, akhir) untuk filter periode, atau null bila tanpa batas.
     * Akhir bersifat eksklusif (awal hari berikutnya) agar jam 23:59:59 tetap ikut.
     * Memakai zona waktu Asia/Jakarta supaya "hari ini" sesuai hari di sekolah.
     */
    public static function rentangTanggal(array $filter) {
        $tz = new DateTimeZone('Asia/Jakarta');
        $hariIni = new DateTime('today', $tz);
        $besok = (clone $hariIni)->modify('+1 day');
        $fmt = 'Y-m-d H:i:s';

        switch ($filter['periode']) {
            case 'hari_ini':
                return [$hariIni->format($fmt), $besok->format($fmt)];
            case 'kemarin':
                return [(clone $hariIni)->modify('-1 day')->format($fmt), $hariIni->format($fmt)];
            case 'minggu_ini': // Senin s.d. Minggu
                $senin = new DateTime('monday this week', $tz);
                return [$senin->format($fmt), (clone $senin)->modify('+7 day')->format($fmt)];
            case 'bulan_ini':
                $awal = new DateTime('first day of this month 00:00:00', $tz);
                return [$awal->format($fmt), (clone $awal)->modify('+1 month')->format($fmt)];
            case 'custom':
                $awal = $filter['dari'] !== '' ? $filter['dari'] . ' 00:00:00' : null;
                $akhir = null;
                if ($filter['sampai'] !== '') {
                    $akhir = (new DateTime($filter['sampai'] . ' 00:00:00'))->modify('+1 day')->format($fmt);
                }
                return [$awal, $akhir];
        }
        return [null, null];
    }

    /** Menyusun klausa WHERE + parameter terikat. */
    private function susunWhere(array $statusList, array $filter) {
        $where = [];
        $types = '';
        $params = [];

        if ($statusList) {
            $where[] = "COALESCE(a.status, 'Menunggu') IN (" . implode(',', array_fill(0, count($statusList), '?')) . ")";
            $types .= str_repeat('s', count($statusList));
            $params = array_merge($params, array_values($statusList));
        }

        [$awal, $akhir] = self::rentangTanggal($filter);
        if ($awal !== null) {
            $where[] = 'i.tgl_pelaporan >= ?';
            $types .= 's';
            $params[] = $awal;
        }
        if ($akhir !== null) {
            $where[] = 'i.tgl_pelaporan < ?';
            $types .= 's';
            $params[] = $akhir;
        }

        if ($filter['kategori'] > 0) {
            $where[] = 'i.id_kategori = ?';
            $types .= 'i';
            $params[] = $filter['kategori'];
        }

        if ($filter['nama'] !== '') {
            $where[] = 's.nama_siswa LIKE ?';
            $types .= 's';
            $params[] = '%' . addcslashes($filter['nama'], '\\%_') . '%';
        }

        if ($filter['kelas'] !== '') {
            $where[] = 's.kelas = ?';
            $types .= 's';
            $params[] = $filter['kelas'];
        }

        return [$where ? ' WHERE ' . implode(' AND ', $where) : '', $types, $params];
    }

    private const FROM_ADMIN = " FROM input_aspirasi i
                  JOIN kategori k ON i.id_kategori = k.id_kategori
                  JOIN siswa s ON i.nis = s.nis
                  LEFT JOIN aspirasi a ON i.id_input = a.id_input";

    /** Jumlah aspirasi yang cocok dengan status + filter. */
    public function countAspirasiFiltered(array $statusList, array $filter) {
        [$where, $types, $params] = $this->susunWhere($statusList, $filter);
        $stmt = mysqli_prepare($this->db, "SELECT COUNT(*) AS total" . self::FROM_ADMIN . $where);
        if (!$stmt) {
            return 0;
        }
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return 0;
        }
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }

    /** Daftar aspirasi (semua siswa) yang cocok dengan status + filter, untuk admin. */
    public function getAspirasiFiltered(array $statusList, array $filter, $limit = null, $offset = 0) {
        [$where, $types, $params] = $this->susunWhere($statusList, $filter);

        $query = "SELECT i.id_input, i.nis, i.lokasi, i.ket, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback,
                         s.nama_siswa, s.kelas"
               . self::FROM_ADMIN . $where . "
                  ORDER BY CASE COALESCE(a.status, 'Menunggu')
                                WHEN 'Menunggu' THEN 1
                                WHEN 'Proses' THEN 2
                                WHEN 'Selesai' THEN 3
                                ELSE 4
                           END ASC,
                           i.tgl_pelaporan DESC, i.id_input DESC";

        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return [];
        }
        if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return [];
        }
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /** Daftar kelas yang ada di data siswa (untuk dropdown filter kelas). */
    public function getDaftarKelas() {
        $result = mysqli_query($this->db, "SELECT DISTINCT kelas FROM siswa WHERE kelas <> '' ORDER BY kelas ASC");
        $data = [];
        while ($result && $row = mysqli_fetch_assoc($result)) {
            $data[] = $row['kelas'];
        }
        return $data;
    }

    /**
     * Detail satu aspirasi untuk admin (dipakai form feedback).
     * Memuat data siswa, kategori, serta status dan feedback saat ini.
     */
    public function getAspirasiById($id_input) {
        $query = "SELECT i.id_input, i.nis, i.lokasi, i.ket, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback,
                         a.updated_at AS tgl_feedback,
                         s.nama_siswa, s.kelas
                  FROM input_aspirasi i
                  JOIN kategori k ON i.id_kategori = k.id_kategori
                  JOIN siswa s ON i.nis = s.nis
                  LEFT JOIN aspirasi a ON i.id_input = a.id_input
                  WHERE i.id_input = ?
                  LIMIT 1";
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return null;
        }
        $id_input = (int)$id_input;
        mysqli_stmt_bind_param($stmt, 'i', $id_input);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    public function updateAspirasi($id_input, $status, $feedback, $id_admin) {
        $id_input = (int)$id_input;
        $status = mysqli_real_escape_string($this->db, $status);
        $feedback = mysqli_real_escape_string($this->db, $feedback);
        $id_admin = (int)$id_admin;

        $cek = mysqli_query($this->db, "SELECT id_aspirasi FROM aspirasi WHERE id_input = '$id_input' LIMIT 1");
        if (!$cek) {
            return false;
        }
        if (mysqli_num_rows($cek) > 0) {
            $query = "UPDATE aspirasi SET status = '$status', feedback = '$feedback', id_admin = '$id_admin', updated_at = NOW() WHERE id_input = '$id_input'";
            return mysqli_query($this->db, $query);
        }

        $row = mysqli_fetch_assoc(mysqli_query($this->db, "SELECT id_kategori FROM input_aspirasi WHERE id_input = '$id_input' LIMIT 1"));
        $id_kategori = $row['id_kategori'] ?? 0;

        $query = "INSERT INTO aspirasi (id_input, status, id_kategori, feedback, id_admin, updated_at)
                  VALUES ('$id_input', '$status', '$id_kategori', '$feedback', '$id_admin', NOW())";
        return mysqli_query($this->db, $query);
    }

    /**
     * Notifikasi untuk siswa: setiap pengaduan miliknya yang sudah ditanggapi admin
     * (id_admin terisi), terbaru di atas. Teks aksi disusun dari kondisi saat ini:
     * status selain "Menunggu" = admin mengubah status; feedback terisi = admin mengirim feedback.
     */
    public function getNotifikasiSiswa($nis, $limit = 10) {
        $query = "SELECT i.id_input, i.lokasi, k.ket_kategori,
                         a.status, a.feedback, a.updated_at,
                         ad.nama_admin
                  FROM input_aspirasi i
                  JOIN aspirasi a ON i.id_input = a.id_input
                  JOIN admin ad ON a.id_admin = ad.id_admin
                  LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                  WHERE CAST(i.nis AS CHAR) = ?
                  ORDER BY a.updated_at DESC, i.id_input DESC
                  LIMIT " . (int)$limit;
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) {
            return [];
        }
        $nis = (string)$nis;
        mysqli_stmt_bind_param($stmt, 's', $nis);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return [];
        }
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['nama_admin'] = trim((string)$row['nama_admin']);
            $row['ada_feedback'] = trim((string)($row['feedback'] ?? '')) !== '';
            $row['ubah_status'] = ($row['status'] ?? 'Menunggu') !== 'Menunggu';
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /** Format tanggal tampilan: d/m/Y H:i (WIB). */
    public static function tglWib($waktu, $denganLabel = false) {
        if (empty($waktu)) {
            return '';
        }
        $t = strtotime($waktu);
        return $t ? date('d/m/Y H:i', $t) . ($denganLabel ? ' WIB' : '') : '';
    }

    public function tambahAspirasi($nis, $id_kategori, $lokasi, $ket) {
        $stmt = mysqli_prepare(
            $this->db,
            'INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, tgl_pelaporan) VALUES (?, ?, ?, ?, NOW())'
        );
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'iiss', $nis, $id_kategori, $lokasi, $ket);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    public function getKategori() {
        $query = "SELECT * FROM kategori";
        $result = mysqli_query($this->db, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}
?>