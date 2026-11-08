// Tampilan penuh foto bukti (admin): klik foto membuka viewer, panah "Kembali" / Esc / klik area gelap menutupnya.
(function () {
    var viewer = document.getElementById('bukti-viewer');
    var pembuka = document.querySelector('.bukti-buka');
    var tombolKembali = document.getElementById('bukti-viewer-back');
    if (!viewer || !pembuka || !tombolKembali) { return; }

    // Pindahkan viewer langsung ke <body>. Di dalam kartu, animasi (transform) pada
    // .container > * membuat position: fixed mengikuti kartu, bukan layar.
    document.body.appendChild(viewer);

    function buka() {
        viewer.hidden = false;
        document.body.classList.add('bukti-viewer-aktif');
        tombolKembali.focus();
    }

    function tutup() {
        viewer.hidden = true;
        document.body.classList.remove('bukti-viewer-aktif');
        pembuka.focus();
    }

    pembuka.addEventListener('click', function (e) {
        e.preventDefault();
        buka();
    });
    tombolKembali.addEventListener('click', tutup);
    viewer.addEventListener('click', function (e) {
        if (e.target === viewer || e.target.classList.contains('bukti-viewer-body')) { tutup(); }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !viewer.hidden) { tutup(); }
    });
})();