// Menu navigasi (admin & siswa): tombol di kiri panel membuka / menutup daftar menu.
(function () {
    document.querySelectorAll('.admin-nav').forEach(function (nav) {
        var tombol = nav.querySelector('.nav-toggle');
        if (!tombol) { return; }

        function atur(buka) {
            nav.classList.toggle('nav-open', buka);
            tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
        }

        tombol.addEventListener('click', function (e) {
            e.stopPropagation();
            atur(!nav.classList.contains('nav-open'));
        });
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target)) { atur(false); }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { atur(false); }
        });
    });
})();
