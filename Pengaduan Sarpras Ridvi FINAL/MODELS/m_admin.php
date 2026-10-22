<?php
class M_Admin {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getByUsernamePassword($username, $password) {
        $username = mysqli_real_escape_string($this->db, $username);
        $password = mysqli_real_escape_string($this->db, $password);

        $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password' LIMIT 1";
        $result = mysqli_query($this->db, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }

        return mysqli_fetch_assoc($result);
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
