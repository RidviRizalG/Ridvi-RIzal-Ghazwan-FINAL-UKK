-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2026 at 04:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sarana_prasaranarpl4`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `role` enum('Kepala Sekolah','Koordinator Sarpras','Petugas Sarpras') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_admin`, `email`, `no_hp`, `role`, `created_at`) VALUES
(101, 'admin1', 'admin54321', 'Lugh Tuatha De, S.Kom', 'Lugh.sarpras@skolah.sch.id', '081234567890', 'Kepala Sekolah', '2026-08-24 04:18:00'),
(102, 'admin2', 'admin12345', 'Van Nei Fertio, A.Md', 'Van.sarpras@skolah.sch.id', '089876543210', 'Petugas Sarpras', '2026-08-24 04:18:00'),
(103, 'admin_budi', 'pass103_budi', 'Budi Santoso, S.T.', 'budi.sarpras@skolah.sch.id', '081298765432', 'Koordinator Sarpras', '2026-08-24 05:00:00'),
(104, 'admin_siti', 'pass104_siti', 'Siti Rahmawati, S.Pd', 'siti.sarpras@skolah.sch.id', '081311223344', 'Petugas Sarpras', '2026-08-24 05:15:00'),
(105, 'admin_ahmad', 'pass105_ahmad', 'Ahmad Dahlan, M.T.', 'ahmad.sarpras@skolah.sch.id', '081355667788', 'Koordinator Sarpras', '2026-08-24 05:30:00'),
(106, 'admin_dewi', 'pass106_dewi', 'Dewi Lestari, S.Kom', 'dewi.sarpras@skolah.sch.id', '081499887766', 'Petugas Sarpras', '2026-08-24 06:00:00'),
(107, 'admin_eko', 'pass107_eko', 'Eko Prasetyo, A.Md', 'eko.sarpras@skolah.sch.id', '081512341234', 'Petugas Sarpras', '2026-08-24 06:15:00'),
(108, 'admin_fajar', 'pass108_fajar', 'Fajar Nugraha, S.T.', 'fajar.sarpras@skolah.sch.id', '081643214321', 'Petugas Sarpras', '2026-08-24 06:30:00'),
(109, 'admin_gita', 'pass109_gita', 'Gita Gutawa, S.Pd', 'gita.sarpras@skolah.sch.id', '081777889900', 'Koordinator Sarpras', '2026-08-24 07:00:00'),
(110, 'admin_hendra', 'pass110_hendra', 'Hendra Wijaya, S.T.', 'hendra.sarpras@skolah.sch.id', '081822334455', 'Petugas Sarpras', '2026-08-24 07:15:00'),
(111, 'admin_indah', 'pass111_indah', 'Indah Permata, A.Md', 'indah.sarpras@skolah.sch.id', '081966778899', 'Petugas Sarpras', '2026-08-24 07:30:00'),
(112, 'admin_joko', 'pass112_joko', 'Joko Susilo, S.Kom', 'joko.sarpras@skolah.sch.id', '082111223344', 'Petugas Sarpras', '2026-08-24 08:00:00'),
(113, 'admin_kartika', 'pass113_kartika', 'Kartika Putri, S.Pd', 'kartika.sarpras@skolah.sch.id', '082255667788', 'Koordinator Sarpras', '2026-08-24 08:15:00'),
(114, 'admin_lukman', 'pass114_lukman', 'Lukman Hakim, M.Pd', 'lukman.sarpras@skolah.sch.id', '082399887766', 'Petugas Sarpras', '2026-08-24 08:30:00'),
(115, 'admin_maya', 'pass115_maya', 'Maya Soraya, A.Md', 'maya.sarpras@skolah.sch.id', '082412344321', 'Petugas Sarpras', '2026-08-24 09:00:00'),
(116, 'admin_naufal', 'pass116_naufal', 'Naufal Rizky, S.T.', 'naufal.sarpras@skolah.sch.id', '082577889900', 'Petugas Sarpras', '2026-08-24 09:15:00'),
(117, 'admin_olivia', 'pass117_olivia', 'Olivia Zalianty, S.Kom', 'olivia.sarpras@skolah.sch.id', '082611224455', 'Petugas Sarpras', '2026-08-24 09:30:00'),
(118, 'admin_putra', 'pass118_putra', 'Putra Utama, S.T.', 'putra.sarpras@skolah.sch.id', '082766778811', 'Petugas Sarpras', '2026-08-24 10:00:00'),
(119, 'admin_qori', 'pass119_qori', 'Qori Sandioriva, S.Pd', 'qori.sarpras@skolah.sch.id', '082822335566', 'Petugas Sarpras', '2026-08-24 10:15:00'),
(120, 'admin_rizky', 'pass120_rizky', 'Rizky Febian, A.Md', 'rizky.sarpras@skolah.sch.id', '082988990011', 'Petugas Sarpras', '2026-08-24 10:30:00'),
(121, 'admin_ridvi', 'ridvi0505', 'Ridvi Rizal Ghazwan, S.Ars\r\n', 'ridvi.sarpras@skolah.sch.id', '089876543210', 'Petugas Sarpras', '2026-10-15 02:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(5) NOT NULL,
  `id_input` int(5) NOT NULL,
  `status` enum('Menunggu','Proses','Selesai') DEFAULT 'Menunggu',
  `id_kategori` int(5) NOT NULL,
  `feedback` text DEFAULT NULL,
  `id_admin` int(5) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `id_input`, `status`, `id_kategori`, `feedback`, `id_admin`, `updated_at`) VALUES
(501, 401, 'Proses', 301, 'Teknisi sudah dijadwalkan untuk mengecek proyektor', 101, '2026-08-24 08:30:00'),
(502, 402, 'Selesai', 302, 'Kran air sudah berhasil diganti dengan yang baru', 102, '2026-08-24 10:00:00'),
(503, 403, 'Selesai', 303, 'Access Point lab komputer 3 telah direstart dan berfungsi normal', 103, '2026-08-24 11:30:00'),
(504, 404, 'Proses', 304, 'RAM komputer PC-05 sedang diganti oleh teknisi lab', 104, '2026-08-24 12:00:00'),
(505, 405, 'Selesai', 305, 'Stop kontak telah diganti dengan tipe standar keamanan baru', 105, '2026-08-24 14:15:00'),
(506, 406, 'Menunggu', 306, 'Laporan diterima, menunggu jadwal perbaikan AC dari teknisi luar', NULL, NULL),
(507, 407, 'Proses', 307, 'Papan penyangga basket dalam proses pemesanan suku cadang', 107, '2026-08-25 10:00:00'),
(508, 408, 'Selesai', 308, 'Sebanyak 5 kursi baca yang rusak sudah diperbaiki dan diperkuat', 108, '2026-08-25 14:00:00'),
(509, 409, 'Selesai', 309, 'Saluran pipa wastafel kantin sudah dibersihkan dari sumbatan', 109, '2026-08-25 11:30:00'),
(510, 410, 'Selesai', 310, 'Bohlam lampu parkir barat sudah diganti dengan lampu LED baru', 110, '2026-08-25 15:00:00'),
(511, 411, 'Selesai', 311, 'Grendel pintu toilet telah diganti baru', 111, '2026-08-25 13:45:00'),
(512, 412, 'Proses', 312, 'Meja guru ditarik ke bengkel sekolah untuk diperbaiki', 112, '2026-08-25 16:00:00'),
(513, 413, 'Menunggu', 313, 'Pengajuan penggantian papan tulis baru sudah dikirim ke sarpras', NULL, NULL),
(514, 414, 'Selesai', 314, 'Pengeras suara sudah diganti horn driver baru', 114, '2026-08-26 10:30:00'),
(515, 415, 'Selesai', 315, 'Kabel HDMI baru sudah dipasang dan dites berfungsi lancar', 115, '2026-08-26 11:00:00'),
(516, 416, 'Selesai', 316, 'Seal kran air lab biologi telah diganti', 116, '2026-08-26 14:20:00'),
(517, 417, 'Proses', 317, 'Headset sedang diproses klaim garansi/perbaikan kabel internal', 117, '2026-08-26 15:00:00'),
(518, 418, 'Selesai', 318, 'Tempat sampah dipasang engsel pengganti baru', 118, '2026-08-26 16:15:00'),
(519, 419, 'Selesai', 319, 'Dua kran tempat wudhu telah diganti kran stainless baru', 119, '2026-08-27 10:00:00'),
(520, 420, 'Menunggu', 320, 'Laporan sudah dicatat, perbaikan akan dilakukan sebelum event sekolah', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `input_aspirasi`
--

CREATE TABLE `input_aspirasi` (
  `id_input` int(5) NOT NULL,
  `nis` int(10) NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `ket` varchar(255) NOT NULL,
  `tgl_pelaporan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `input_aspirasi`
--

INSERT INTO `input_aspirasi` (`id_input`, `nis`, `id_kategori`, `lokasi`, `ket`, `tgl_pelaporan`) VALUES
(401, 201, 301, 'Ruang XII RPL 1', 'Proyektor mati total saat KBM', '2026-08-24 08:00:00'),
(402, 202, 302, 'Toilet Lantai 2', 'Kran air bocor dan air meluap', '2026-08-24 09:15:00'),
(403, 203, 303, 'Lab Komputer 3', 'Koneksi Wi-Fi tidak bisa terhubung (no internet access)', '2026-08-24 10:00:00'),
(404, 204, 304, 'Lab RPL 2', 'Komputer PC-05 mengalami bluescreen berulang kali', '2026-08-24 10:30:00'),
(405, 205, 305, 'Ruang XI TKJ 1', 'Stop kontak bagian belakang terbakar/korslet', '2026-08-24 11:15:00'),
(406, 206, 306, 'Ruang Guru Utama', 'AC mengeluarkan suara bising dan meneteskan air', '2026-08-24 13:00:00'),
(407, 207, 307, 'Lapangan Basket', 'Net ring basket terlepas dan papan penyangga retak', '2026-08-25 08:10:00'),
(408, 208, 308, 'Perpustakaan Lt 1', 'Kursi baca siswa banyak yang bergoyang dan rusak', '2026-08-25 09:00:00'),
(409, 209, 309, 'Kantin Utama', 'Wastafel tempat cuci piring kantin tersumbat total', '2026-08-25 09:45:00'),
(410, 210, 310, 'Parkir Motor Siswa', 'Lampu penerangan area parkir barat padam', '2026-08-25 10:20:00'),
(411, 211, 311, 'Toilet Siswa Lt 3', 'Pintu toilet nomor 2 kuncinya rusak tidak bisa dikunci', '2026-08-25 11:00:00'),
(412, 212, 312, 'Ruang XI DKV 2', 'Meja guru patah pada bagian kaki kiri', '2026-08-25 13:30:00'),
(413, 213, 313, 'Ruang X RPL 1', 'Papan tulis whiteboard terkelupas dan susah dihapus', '2026-08-26 08:00:00'),
(414, 214, 314, 'Lapangan Upacara', 'Speaker pengeras suara sebelah timur suara pecah', '2026-08-26 08:45:00'),
(415, 215, 315, 'Ruang XI TKJ 1', 'Kabel HDMI LCD proyektor putus di dalam port', '2026-08-26 09:30:00'),
(416, 216, 316, 'Lab Biologi', 'Kran air di meja praktikum nomor 4 bocor halus', '2026-08-26 10:15:00'),
(417, 217, 317, 'Lab Bahasa 1', 'Headset meja 12 suara hanya keluar sebelah', '2026-08-26 11:30:00'),
(418, 218, 318, 'Taman Depan XI DKV 2', 'Tempat sampah pemilahan plastik patah engselnya', '2026-08-26 13:00:00'),
(419, 219, 319, 'Mushola Sekolah', 'Kran wudhu bagian barat ada 2 yang patah', '2026-08-27 08:30:00'),
(420, 220, 320, 'Aula Sekolah', 'Lampu sorot panggung bagian tengah berkedip-kedip', '2026-08-27 09:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(5) NOT NULL,
  `ket_kategori` varchar(30) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `ket_kategori`, `deskripsi`) VALUES
(301, 'Fasilitas Kelas', 'Pengaduan terkait sarana prasarana di dalam ruang kelas'),
(302, 'Layanan Kebersihan', 'Pengaduan terkait kebersihan lingkungan sekolah dan toilet'),
(303, 'Jaringan & Internet', 'Kerusakan atau gangguan koneksi Wi-Fi dan LAN sekolah'),
(304, 'Perangkat Komputer', 'Gangguan hardware/software komputer di laboratorium'),
(305, 'Kelistrikan', 'Masalah lampu mati, stop kontak rusak, atau saklar bermasalah'),
(306, 'Pendingin Ruangan (AC)', 'AC tidak dingin, bocor, atau mati total'),
(307, 'Fasilitas Olahraga', 'Kerusakan alat olahraga seperti bola, net, atau lapangan'),
(308, 'Perpustakaan', 'Kerusakan meja/kursi baca, pendingin, atau sistem perpustakaan'),
(309, 'Kantin Sekolah', 'Kebersihan meja makan kantin dan kelayakan fasilitas penunjang'),
(310, 'Keamanan & Parkir', 'Fasilitas area parkir, gerbang sekolah, dan penerangan luar'),
(311, 'Toilet & Sanitasi', 'Wastafel tersumbat, pintu toilet rusak, atau ketersediaan air'),
(312, 'Meubelair & Furnitur', 'Kerusakan meja, kursi siswa/guru di berbagai ruangan'),
(313, 'Papan Tulis & Spidol', 'Papan tulis rusak, keretakan Whiteboard, atau ketersediaan spidol'),
(314, 'Sound System', 'Pengeras suara sekolah, speaker kelas, atau mikrofon lapangan'),
(315, 'Proyektor & Layar', 'Kerusakan LCD proyektor, kabel HDMI/VGA, dan layar gantung'),
(316, 'Laboratorium IPA', 'Kerusakan alat-alat praktikum, kran gas, dan meja lab'),
(317, 'Laboratorium Bahasa', 'Gangguan headset, mikrofon, atau konsol master lab bahasa'),
(318, 'Taman & Penghijauan', 'Pemeliharaan tanaman, tempat sampah taman, dan kebersihan selokan'),
(319, 'Fasilitas Ibadah (Mushola)', 'Kebersihan karpet mushola, sajadah, tempat wudhu, dan mukena'),
(320, 'Aula / GSG', 'Kerusakan panggung, sound aula, panggung acara, dan pencahayaan');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(10) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(15) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama_siswa`, `kelas`, `jurusan`, `jenis_kelamin`, `password`, `created_at`) VALUES
(201, 'Killua Zoldyck', 'XII RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'siswa1234', '2026-08-24 04:18:00'),
(202, 'Allen Haelwol', 'XII RPL 2', 'Rekayasa Perangkat Lunak', 'L', 'siswa4321', '2026-08-24 04:18:00'),
(203, 'Cain Von Silford', 'XII RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'pass203', '2026-08-24 05:00:00'),
(204, 'Kaoruko Waguri', 'XII RPL 2', 'Rekayasa Perangkat Lunak', 'P', 'pass204', '2026-08-24 05:05:00'),
(205, 'Marin Kitagawa', 'XI RPL 1', 'Rekayasa Perangkat Lunak', 'P', 'pass205', '2026-08-24 05:10:00'),
(206, 'Dion Pratama', 'XI RPL 2', 'Rekayasa Perangkat Lunak', 'L', 'pass206', '2026-08-24 05:15:00'),
(207, 'Erika Putri', 'X BC 1', 'Broadcasting', 'P', 'pass207', '2026-08-24 05:20:00'),
(208, 'Farma De Medicis', 'X BC 2', 'Broadcasting', 'L', 'pass208', '2026-08-24 05:25:00'),
(209, 'Gilang Ramadhan', 'XII RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'pass209', '2026-08-24 05:30:00'),
(210, 'Hani Puspita', 'XII RPL 2', 'Rekayasa Perangkat Lunak', 'P', 'pass210', '2026-08-24 05:35:00'),
(211, 'Irfan Syahputra', 'XI DKV 1', 'Desain Komunikasi Visual', 'L', 'pass211', '2026-08-24 05:40:00'),
(212, 'Jessica Mila', 'XI DKV 2', 'Desain Komunikasi Visual', 'P', 'pass212', '2026-08-24 05:45:00'),
(213, 'Kevin Sanjaya', 'X RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'pass213', '2026-08-24 05:50:00'),
(214, 'Larasati Putri', 'X RPL 2', 'Rekayasa Perangkat Lunak', 'P', 'pass214', '2026-08-24 05:55:00'),
(215, 'Muhammad Rizky', 'XI RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'pass215', '2026-08-24 06:00:00'),
(216, 'Nadia Vega', 'XI RPL 2', 'Rekayasa Perangkat Lunak', 'P', 'pass216', '2026-08-24 06:05:00'),
(217, 'Oky Setiana', 'XII DKV 1', 'Desain Komunikasi Visual', 'P', 'pass217', '2026-08-24 06:10:00'),
(218, 'Panji Petualang', 'XII DKV 2', 'Desain Komunikasi Visual', 'L', 'pass218', '2026-08-24 06:15:00'),
(219, 'Qabil Hidayat', 'X RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'pass219', '2026-08-24 06:20:00'),
(220, 'Rania Salsabila', 'X RPL 2', 'Rekayasa Perangkat Lunak', 'P', 'pass220', '2026-08-24 06:25:00'),
(221, 'Muhammad Khairul Zimam Raqilla', 'X DKV 1', 'Desain Komunikasi Visual', 'L', 'zimam1902', '2026-10-15 02:42:32'),
(222, 'Rafiif Alfiansyah', 'X RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'rafiif2403', '2026-10-15 02:42:32'),
(223, 'Azka Gumilar', 'X RPL 1', 'Rekayasa Perangkat Lunak', 'L', 'azka0812', '2026-10-15 02:42:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`),
  ADD KEY `id_input` (`id_input`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD PRIMARY KEY (`id_input`),
  ADD KEY `nis` (`nis`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=521;

--
-- AUTO_INCREMENT for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  MODIFY `id_input` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD CONSTRAINT `aspirasi_ibfk_1` FOREIGN KEY (`id_input`) REFERENCES `input_aspirasi` (`id_input`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aspirasi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aspirasi_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD CONSTRAINT `input_aspirasi_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `input_aspirasi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
