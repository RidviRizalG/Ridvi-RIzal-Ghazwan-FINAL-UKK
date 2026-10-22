<?php
// Lonceng notifikasi siswa (dipakai di navbar semua halaman siswa).
// Butuh $notifikasi dari controller: hasil M_Aspirasi::getNotifikasiSiswa().
$notifikasi = $notifikasi ?? [];
?>
<div class="notif-wrap" data-notif data-notif-user="<?= htmlspecialchars((string)($_SESSION['nis'] ?? '')) ?>">
    <button type="button" class="notif-toggle" data-notif-toggle aria-label="Notifikasi" aria-expanded="false" title="Notifikasi">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="notif-badge" data-notif-badge hidden>0</span>
    </button>

    <div class="notif-panel" data-notif-panel hidden>
        <div class="notif-head">Notifikasi</div>
        <?php if (empty($notifikasi)): ?>
            <div class="notif-empty">Belum ada tanggapan dari admin.</div>
        <?php else: ?>
            <ul class="notif-list">
                <?php foreach ($notifikasi as $n): ?>
                    <?php
                    $aksi = [];
                    if (!empty($n['ubah_status'])) {
                        $aksi[] = 'merubah status';
                    }
                    if (!empty($n['ada_feedback'])) {
                        $aksi[] = 'mengirim feedback';
                    }
                    if (!$aksi) {
                        $aksi[] = 'memperbarui pengaduanmu';
                    }
                    ?>
                    <li>
                        <a class="notif-item" data-notif-ts="<?= (int)strtotime($n['updated_at']) ?>"
                           href="../CONTROLLERS/c_detail_aspirasi_siswa.php?id=<?= (int)$n['id_input'] ?>">
                            <span class="notif-text">
                                Admin <strong><?= htmlspecialchars($n['nama_admin']) ?></strong>
                                telah <?= implode(' dan ', $aksi) ?>
                            </span>
                            <span class="notif-meta"><?= htmlspecialchars(M_Aspirasi::tglWib($n['updated_at'], true)) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
