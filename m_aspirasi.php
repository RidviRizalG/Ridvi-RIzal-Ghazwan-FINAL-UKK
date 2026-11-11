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
        $query = "SELECT i.id_input, i.lokasi, i.ket, i.foto, i.tgl_pelaporan,
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

    // ---------------------------------------------------------------------------------
    // Rating kepuasan siswa terhadap kinerja admin (1 = sangat tidak puas ... 5 = sangat puas).
    // Disimpan di tabel aspirasi (kolom rating, komentar_rating, tgl_rating) tanpa tabel baru.
    // Komentar WAJIB diisi untuk bintang berapa pun. Bila database lama belum punya kolomnya,
    // kolom dibuat otomatis sekali saja sehingga tidak perlu impor ulang SQL.
    // ---------------------------------------------------------------------------------
    const RATING_MIN = 1;
    const RATING_MAKS = 5;
    const KOMENTAR_MAKS = 500;
    const RATING_LABEL = [
        1 => 'Sangat tidak puas',
        2 => 'Tidak puas',
        3 => 'Cukup puas',
        4 => 'Puas',
        5 => 'Sangat puas',
    ];

    /** True bila kolom rating, komentar_rating & tgl_rating tersedia (dibuat otomatis jika belum ada). */
    private function pastikanKolomRating() {
        static $siap = null;
        if ($siap !== null) {
            return $siap;
        }
        $definisi = [
            'rating'          => 'TINYINT(1) NULL DEFAULT NULL',
            'komentar_rating' => 'TEXT NULL DEFAULT NULL',
            'tgl_rating'      => 'DATETIME NULL DEFAULT NULL',
        ];
        $ada = [];
        $hasil = mysqli_query($this->db, 'SHOW COLUMNS FROM aspirasi');
        while ($hasil && $row = mysqli_fetch_assoc($hasil)) {
            $ada[$row['Field']] = true;
        }
        $siap = true;
        foreach ($definisi as $kolom => $tipe) {
            if (isset($ada[$kolom])) {
                continue;
            }
            if (!mysqli_query($this->db, 'ALTER TABLE aspirasi ADD COLUMN `' . $kolom . '` ' . $tipe)) {
                $siap = false;
            }
        }
        return $siap;
    }

    /** Potongan SELECT untuk kolom rating (NULL bila kolom belum bisa dibuat, agar halaman tetap jalan). */
    private function kolomRatingSql($denganKomentar = true) {
        if ($this->pastikanKolomRating()) {
            return $denganKomentar
                ? 'a.rating, a.komentar_rating, a.tgl_rating'
                : 'a.rating';
        }
        return $denganKomentar
            ? 'NULL AS rating, NULL AS komentar_rating, NULL AS tgl_rating'
            : 'NULL AS rating';
    }

    /**
     * Detail satu pengaduan milik siswa tertentu (khusus halaman detail siswa).
     * NIS ikut di WHERE sehingga siswa tidak bisa membuka pengaduan siswa lain
     * hanya dengan mengganti id di URL.
     */
    public function getAspirasiByIdForNis($id_input, $nis) {
        $kolomRating = $this->kolomRatingSql();
        $query = "SELECT i.id_input, i.lokasi, i.ket, i.foto, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback,
                         a.updated_at AS tgl_feedback,
                         ad.nama_admin AS nama_admin_feedback,
                         $kolomRating
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

    /** Rapikan komentar rating: trim + samakan baris baru. */
    public static function bersihkanKomentar($komentar) {
        return trim(str_replace(["\r\n", "\r"], "\n", (string)$komentar));
    }

    /**
     * Simpan rating + komentar siswa untuk pengaduan miliknya. Hanya berhasil bila pengaduan milik
     * siswa tersebut, berstatus Selesai, belum pernah dinilai (rating diberikan sekali), rating 1-5,
     * dan komentar terisi (maks. KOMENTAR_MAKS karakter).
     * updated_at sengaja dipertahankan agar "Terakhir Diperbarui" & notifikasi tidak bergeser.
     */
    public function simpanRating($id_input, $nis, $rating, $komentar) {
        $rating = (int)$rating;
        $komentar = self::bersihkanKomentar($komentar);
        if ($rating < self::RATING_MIN || $rating > self::RATING_MAKS
            || $komentar === '' || mb_strlen($komentar) > self::KOMENTAR_MAKS
            || !$this->pastikanKolomRating()) {
            return false;
        }
        $stmt = mysqli_prepare(
            $this->db,
            "UPDATE aspirasi a
             JOIN input_aspirasi i ON a.id_input = i.id_input
             SET a.rating = ?, a.komentar_rating = ?, a.tgl_rating = NOW(), a.updated_at = a.updated_at
             WHERE a.id_input = ? AND CAST(i.nis AS CHAR) = ?
               AND a.status = 'Selesai' AND a.rating IS NULL"
        );
        if (!$stmt) {
            return false;
        }
        $id_input = (int)$id_input;
        $nis = (string)$nis;
        mysqli_stmt_bind_param($stmt, 'isis', $rating, $komentar, $id_input, $nis);
        $berhasil = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $berhasil;
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
                         " . $this->kolomRatingSql(false) . ",
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
        $kolomRating = $this->kolomRatingSql();
        $query = "SELECT i.id_input, i.nis, i.lokasi, i.ket, i.foto, i.tgl_pelaporan,
                         k.ket_kategori,
                         COALESCE(a.status, 'Menunggu') AS status,
                         a.feedback,
                         a.updated_at AS tgl_feedback,
                         $kolomRating,
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

    // ---------------------------------------------------------------------------------
    // Catatan aksi admin terakhir per pengaduan (bahan teks notifikasi siswa).
    // Tabel aspirasi hanya menyimpan status & feedback TERKINI, bukan apa yang terakhir diubah.
    // Karena skema database tidak diubah, aksi terakhir dicatat di UPLOADS/aksi_admin.php
    // (dibuat otomatis saat admin menyimpan; diawali penjaga exit agar isinya tak bisa dibuka lewat URL).
    // Bila catatan tidak ada / tidak cocok dengan data, notifikasi memakai tebakan dari kondisi sekarang.
    // ---------------------------------------------------------------------------------
    private const PENJAGA_AKSI = "<?php http_response_code(404); exit; ?>\n";

    private static function berkasAksi() {
        return __DIR__ . '/../UPLOADS/aksi_admin.php';
    }

    private static function uraiAksi($isi) {
        if (!is_string($isi) || strncmp($isi, self::PENJAGA_AKSI, strlen(self::PENJAGA_AKSI)) !== 0) {
            return [];
        }
        $data = json_decode(substr($isi, strlen(self::PENJAGA_AKSI)), true);
        return is_array($data) ? $data : [];
    }

    /** Catat aksi admin pada pengaduan: apakah status diubah dan/atau feedback diisi. */
    public function catatAksiAdmin($id_input, $statusBerubah, $feedbackBerubah, $status) {
        $fp = @fopen(self::berkasAksi(), 'c+');
        if (!$fp) {
            return false;
        }
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return false;
        }
        $data = self::uraiAksi(stream_get_contents($fp));
        $data[(string)(int)$id_input] = [
            's'      => $statusBerubah ? 1 : 0,
            'f'      => $feedbackBerubah ? 1 : 0,
            'status' => (string)$status,
            'ts'     => time(),
        ];
        if (count($data) > 5000) { // simpan 5000 catatan terbaru saja
            uasort($data, function ($a, $b) { return ($b['ts'] ?? 0) <=> ($a['ts'] ?? 0); });
            $data = array_slice($data, 0, 5000, true);
        }
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, self::PENJAGA_AKSI . json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
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
        $catatan = self::uraiAksi(@file_get_contents(self::berkasAksi()));
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['nama_admin'] = trim((string)$row['nama_admin']);
            $row['ada_feedback'] = trim((string)($row['feedback'] ?? '')) !== '';
            $row['ubah_status'] = ($row['status'] ?? 'Menunggu') !== 'Menunggu';

            // Aksi admin terakhir: dari catatan bila cocok dengan data (status sama, waktu berdekatan).
            $aksi = $catatan[(string)(int)$row['id_input']] ?? null;
            $tsDb = strtotime((string)$row['updated_at']);
            $cocok = is_array($aksi)
                && ($aksi['status'] ?? '') === ($row['status'] ?? '')
                && $tsDb
                && abs($tsDb - (int)($aksi['ts'] ?? 0)) <= 10;
            if ($cocok) {
                $row['aksi_status'] = !empty($aksi['s']);
                $row['aksi_feedback'] = !empty($aksi['f']);
            } else {
                $row['aksi_status'] = $row['ubah_status'];
                $row['aksi_feedback'] = $row['ada_feedback'];
            }
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    // =====================================================
    // STATISTIK DASHBOARD ADMIN (dihitung dari tabel yang sudah ada, tanpa tabel baru)
    // =====================================================

    /** Jalankan query tanpa parameter, kembalikan semua baris ([] bila gagal). */
    private function ambilSemua($sql) {
        $hasil = mysqli_query($this->db, $sql);
        $data = [];
        while ($hasil && $row = mysqli_fetch_assoc($hasil)) {
            $data[] = $row;
        }
        return $data;
    }

    /** Durasi dalam menit -> teks ringkas ("35 menit", "6,5 jam", "3,2 hari"). */
    public static function formatDurasi($menit) {
        if ($menit === null) {
            return '-';
        }
        $menit = (float)$menit;
        if ($menit < 60) {
            return max(1, (int)round($menit)) . ' menit';
        }
        if ($menit < 60 * 48) {
            return rtrim(rtrim(number_format($menit / 60, 1, ',', ''), '0'), ',') . ' jam';
        }
        return rtrim(rtrim(number_format($menit / 1440, 1, ',', ''), '0'), ',') . ' hari';
    }

    /**
     * Seluruh angka untuk dashboard statistik admin:
     * total & status pengaduan, per kategori, per bulan (6 bulan), rata-rata waktu selesai,
     * serta rating (rata-rata, jumlah per bintang, per admin, komentar terbaru).
     */
    public function getStatistikDashboard() {
        $stat = [
            'total' => 0,
            'status' => ['Menunggu' => 0, 'Proses' => 0, 'Selesai' => 0],
            'total_siswa' => 0,
            'rata_selesai_menit' => null,
            'kategori' => [],
            'bulanan' => [],
            'rating' => [
                'jumlah' => 0,
                'rata' => null,
                'per_bintang' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'belum_dinilai' => 0,
                'per_admin' => [],
                'komentar' => [],
            ],
        ];

        // Status
        $sql = "SELECT COALESCE(a.status, 'Menunggu') AS status, COUNT(*) AS total
                FROM input_aspirasi i LEFT JOIN aspirasi a ON i.id_input = a.id_input
                GROUP BY COALESCE(a.status, 'Menunggu')";
        foreach ($this->ambilSemua($sql) as $row) {
            if (isset($stat['status'][$row['status']])) {
                $stat['status'][$row['status']] = (int)$row['total'];
            }
        }
        $stat['total'] = array_sum($stat['status']);

        // Total siswa
        $row = $this->ambilSemua('SELECT COUNT(*) AS total FROM siswa')[0] ?? null;
        $stat['total_siswa'] = (int)($row['total'] ?? 0);

        // Rata-rata waktu penyelesaian (laporan masuk -> status Selesai terakhir diperbarui)
        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, i.tgl_pelaporan, a.updated_at)) AS rata
                FROM input_aspirasi i JOIN aspirasi a ON i.id_input = a.id_input
                WHERE a.status = 'Selesai' AND a.updated_at IS NOT NULL AND a.updated_at >= i.tgl_pelaporan";
        $row = $this->ambilSemua($sql)[0] ?? null;
        $stat['rata_selesai_menit'] = ($row && $row['rata'] !== null) ? (float)$row['rata'] : null;

        // Per kategori (8 teratas)
        $sql = "SELECT k.ket_kategori, COUNT(*) AS total
                FROM input_aspirasi i JOIN kategori k ON i.id_kategori = k.id_kategori
                GROUP BY k.id_kategori, k.ket_kategori
                ORDER BY total DESC, k.ket_kategori ASC
                LIMIT 8";
        foreach ($this->ambilSemua($sql) as $row) {
            $stat['kategori'][] = ['nama' => $row['ket_kategori'], 'total' => (int)$row['total']];
        }

        // Per bulan: 6 bulan terakhir (termasuk bulan ini), bulan kosong tetap tampil bernilai 0.
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $awal = new DateTime('first day of this month 00:00:00', new DateTimeZone('Asia/Jakarta'));
        $awal->modify('-5 month');
        $bulan = [];
        for ($n = 0; $n < 6; $n++) {
            $kunci = $awal->format('Y-m');
            $bulan[$kunci] = ['label' => $namaBulan[(int)$awal->format('n') - 1] . ' ' . $awal->format('y'), 'total' => 0];
            $awal->modify('+1 month');
        }
        $batas = array_key_first($bulan) . '-01 00:00:00';
        $stmt = mysqli_prepare($this->db, "SELECT DATE_FORMAT(tgl_pelaporan, '%Y-%m') AS ym, COUNT(*) AS total
                                           FROM input_aspirasi WHERE tgl_pelaporan >= ? GROUP BY ym");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $batas);
            if (mysqli_stmt_execute($stmt)) {
                $hasil = mysqli_stmt_get_result($stmt);
                while ($hasil && $row = mysqli_fetch_assoc($hasil)) {
                    if (isset($bulan[$row['ym']])) {
                        $bulan[$row['ym']]['total'] = (int)$row['total'];
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
        $stat['bulanan'] = array_values($bulan);

        // Rating
        if ($this->pastikanKolomRating()) {
            $r = &$stat['rating'];
            foreach ($this->ambilSemua('SELECT rating, COUNT(*) AS total FROM aspirasi WHERE rating IS NOT NULL GROUP BY rating') as $row) {
                $bintang = (int)$row['rating'];
                if (isset($r['per_bintang'][$bintang])) {
                    $r['per_bintang'][$bintang] = (int)$row['total'];
                }
            }
            $r['jumlah'] = array_sum($r['per_bintang']);
            if ($r['jumlah'] > 0) {
                $jumlahNilai = 0;
                foreach ($r['per_bintang'] as $bintang => $jml) {
                    $jumlahNilai += $bintang * $jml;
                }
                $r['rata'] = $jumlahNilai / $r['jumlah'];
            }
            $r['belum_dinilai'] = max(0, $stat['status']['Selesai'] - $r['jumlah']);

            // Rata-rata rating // Rata-rata & sebaran bintang per admin jumlah ulasan per admin
            $sql = "SELECT ad.id_admin, ad.nama_admin, ad.role,
                           COUNT(a.rating) AS jumlah, AVG(a.rating) AS rata
                    FROM aspirasi a JOIN admin ad ON a.id_admin = ad.id_admin
                    WHERE a.rating IS NOT NULL
                    GROUP BY ad.id_admin, ad.nama_admin, ad.role
                    ORDER BY rata DESC, jumlah DESC, ad.nama_admin ASC
                    LIMIT 10";
            foreach ($this->ambilSemua($sql) as $row) {
                $r['per_admin'][] = [
                    'nama'   => $row['nama_admin'],
                    'role'   => $row['role'],
                    'jumlah' => (int)$row['jumlah'],
                    'rata'   => (float)$row['rata'],
                ];
            }

            // Komentar terbaru
            $sql = "SELECT a.rating, a.komentar_rating, a.tgl_rating,
                           s.nama_siswa, ad.nama_admin, k.ket_kategori
                    FROM aspirasi a
                    JOIN input_aspirasi i ON a.id_input = i.id_input
                    JOIN siswa s ON i.nis = s.nis
                    LEFT JOIN admin ad ON a.id_admin = ad.id_admin
                    LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                    WHERE a.rating IS NOT NULL
                    ORDER BY a.tgl_rating DESC, a.id_aspirasi DESC
                    LIMIT 5";
            $r['komentar'] = $this->ambilSemua($sql);
            unset($r);
        }

        return $stat;
    }

    /** Format tanggal tampilan: d/m/Y H:i (WIB). */
    public static function tglWib($waktu, $denganLabel = false) {
        if (empty($waktu)) {
            return '';
        }
        $t = strtotime($waktu);
        return $t ? date('d/m/Y H:i', $t) . ($denganLabel ? ' WIB' : '') : '';
    }

    /** True bila file bukti (kolom foto) adalah video. */
    public static function buktiAdalahVideo($nama) {
        return in_array(strtolower(pathinfo((string)$nama, PATHINFO_EXTENSION)), ['mp4', 'webm', 'mov', '3gp'], true);
    }

    public function tambahAspirasi($nis, $id_kategori, $lokasi, $ket, $foto = null) {
        $stmt = mysqli_prepare(
            $this->db,
            'INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, foto, tgl_pelaporan) VALUES (?, ?, ?, ?, ?, NOW())'
        );
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'iisss', $nis, $id_kategori, $lokasi, $ket, $foto);
        $berhasil = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $berhasil;
    }

    /**
     * True bila pengaduan yang sama persis (siswa, kategori, lokasi, keterangan) sudah masuk
     * dalam $detik terakhir. Mencegah pengaduan ganda akibat klik dua kali atau kirim ulang.
     */
    public function adaPengaduanDobel($nis, $id_kategori, $lokasi, $ket, $detik = 60) {
        $stmt = mysqli_prepare(
            $this->db,
            'SELECT 1 FROM input_aspirasi
             WHERE CAST(nis AS CHAR) = ? AND id_kategori = ? AND lokasi = ? AND ket = ?
               AND tgl_pelaporan >= (NOW() - INTERVAL ' . (int)$detik . ' SECOND)
             LIMIT 1'
        );
        if (!$stmt) {
            return false;
        }
        $nis = (string)$nis;
        $id_kategori = (int)$id_kategori;
        mysqli_stmt_bind_param($stmt, 'siss', $nis, $id_kategori, $lokasi, $ket);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }
        mysqli_stmt_store_result($stmt);
        $ada = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $ada;
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