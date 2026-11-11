<?php
class M_Admin {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
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

    public function getByUsernamePassword($username, $password) {
        $stmt = mysqli_prepare($this->db, 'SELECT * FROM admin WHERE username = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, 's', $username);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$admin || !self::passwordCocok($password, $admin['password'])) {
            return null;
        }

        // Migrasi otomatis: password polos lama (atau hash usang) diganti hash baru saat login berhasil.
        if (!self::sudahHash($admin['password']) || password_needs_rehash($admin['password'], PASSWORD_DEFAULT)) {
            $stmt = mysqli_prepare($this->db, 'UPDATE admin SET password = ? WHERE id_admin = ?');
            if ($stmt) {
                $hash = password_hash((string)$password, PASSWORD_DEFAULT);
                $idAdmin = (int)$admin['id_admin'];
                mysqli_stmt_bind_param($stmt, 'si', $hash, $idAdmin);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        return $admin;
    }

    public function getById($idAdmin) {
        $idAdmin = (int)$idAdmin;
        $query = "SELECT * FROM admin WHERE id_admin = '$idAdmin' LIMIT 1";
        $result = mysqli_query($this->db, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }

        return mysqli_fetch_assoc($result);
    }
}
?>
