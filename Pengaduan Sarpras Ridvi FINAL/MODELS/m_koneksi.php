<?php

// Seluruh aplikasi memakai WIB (Asia/Jakarta, UTC+7), bukan UTC bawaan server.
date_default_timezone_set('Asia/Jakarta');

class Koneksi
{
    private $host = "sql111.infinityfree.com";
    private $username = "if0_43205381";
    private $pass = "vHdAns7i4taU6h";
    private $db = "if0_43205381_sarana_prasaranarpl4";
    private $ports = [3306, 3307];

    public $koneksi;

    public function __construct()
    {
        $lastError = null;

        foreach ($this->ports as $port) {
            $this->koneksi = mysqli_connect(
                $this->host,
                $this->username,
                $this->pass,
                $this->db,
                $port
            );

            if ($this->koneksi) {
                break;
            }

            $lastError = mysqli_connect_error();
        }

        if (!$this->koneksi) {
            die("Koneksi ke database gagal. Periksa host, username, password, dan nama database. Error: " . $lastError);
        }

        mysqli_set_charset($this->koneksi, "utf8mb4");

        // NOW() / CURRENT_TIMESTAMP di MySQL ikut WIB, sehingga tgl_pelaporan dan updated_at tersimpan dalam WIB.
        mysqli_query($this->koneksi, "SET time_zone = '+07:00'");
    }
}

$koneksi = new Koneksi();

$conn = $koneksi->koneksi;
?>