// Form pengaduan siswa: bukti foto / video dari kamera atau file, pratinjau, dan wajib diisi.
(function () {
    var form = document.getElementById('form-pengaduan');
    if (!form) { return; }

    var inputFoto = document.getElementById('bukti_foto');
    var inputVideo = document.getElementById('bukti_video');
    var inputFile = document.getElementById('bukti_file');
    var semua = [inputFoto, inputVideo, inputFile];

    var zona = document.getElementById('bukti-zona');
    var kosong = document.getElementById('bukti-kosong');
    var hasil = document.getElementById('bukti-hasil');
    var media = document.getElementById('bukti-media');
    var badge = document.getElementById('bukti-badge');
    var nama = document.getElementById('bukti-nama');
    var ukuran = document.getElementById('bukti-ukuran');
    var tombolHapus = document.getElementById('bukti-hapus');
    var pesan = document.getElementById('bukti-error');

    var MAKS_FOTO = 5 * 1024 * 1024;
    var MAKS_VIDEO = 9 * 1024 * 1024;
    var SISI_MAKS = 1600;
    var urlAktif = null;

    function adalahVideo(f) { return /^video\//.test(f.type) || /\.(mp4|webm|mov|3gp)$/i.test(f.name); }
    function adalahFoto(f) { return /^image\//.test(f.type) || /\.(jpe?g|png|webp)$/i.test(f.name); }
    function batas(f) { return adalahVideo(f) ? MAKS_VIDEO : MAKS_FOTO; }

    function format(b) {
        return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
    }

    function terpilih() {
        for (var i = 0; i < semua.length; i++) {
            if (semua[i].files && semua[i].files.length) { return semua[i].files[0]; }
        }
        return null;
    }

    function tampilPesan(teks) {
        pesan.textContent = teks;
        pesan.hidden = false;
        zona.classList.add('bukti-salah');
    }

    function bersihPesan() {
        pesan.hidden = true;
        zona.classList.remove('bukti-salah');
    }

    function kosongkan() {
        semua.forEach(function (i) { i.value = ''; });
        if (urlAktif) { URL.revokeObjectURL(urlAktif); urlAktif = null; }
        media.innerHTML = '';
        hasil.hidden = true;
        kosong.hidden = false;
        zona.classList.remove('bukti-ada');
    }

    function tampilkan(file) {
        if (urlAktif) { URL.revokeObjectURL(urlAktif); }
        urlAktif = URL.createObjectURL(file);
        media.innerHTML = '';
        var el;
        if (adalahVideo(file)) {
            el = document.createElement('video');
            el.src = urlAktif;
            el.controls = true;
            el.muted = true;
            el.preload = 'metadata';
        } else {
            el = document.createElement('img');
            el.src = urlAktif;
            el.alt = 'Pratinjau bukti';
        }
        media.appendChild(el);
        badge.textContent = adalahVideo(file) ? 'Video' : 'Foto';
        nama.textContent = file.name;
        nama.title = file.name;
        ukuran.textContent = format(file.size);
        kosong.hidden = true;
        hasil.hidden = false;
        zona.classList.add('bukti-ada');
        bersihPesan();
    }

    // Perkecil foto besar (hasil kamera HP) agar upload cepat dan tidak melewati batas server.
    function perkecil(input, file) {
        if (!/^image\/(jpeg|png|webp)$/.test(file.type)) { return; }
        var img = new Image();
        var url = URL.createObjectURL(file);
        img.onload = function () {
            URL.revokeObjectURL(url);
            var skala = Math.min(1, SISI_MAKS / Math.max(img.width, img.height));
            if (skala === 1 && file.size <= MAKS_FOTO) { return; }
            var c = document.createElement('canvas');
            c.width = Math.round(img.width * skala);
            c.height = Math.round(img.height * skala);
            c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
            c.toBlob(function (blob) {
                if (!blob || blob.size >= file.size) { return; }
                try {
                    var baru = new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' });
                    var dt = new DataTransfer();
                    dt.items.add(baru);
                    input.files = dt.files;
                    tampilkan(baru);
                } catch (e) { /* biarkan file asli */ }
            }, 'image/jpeg', 0.85);
        };
        img.onerror = function () { URL.revokeObjectURL(url); };
        img.src = url;
    }

    function proses(input, file) {
        if (!adalahFoto(file) && !adalahVideo(file)) {
            kosongkan();
            tampilPesan('File tidak valid. Gunakan foto (JPG, PNG, WEBP) atau video (MP4, WEBM, MOV).');
            return;
        }
        // Hanya satu bukti yang dikirim.
        semua.forEach(function (i) { if (i !== input) { i.value = ''; } });
        tampilkan(file);
        perkecil(input, file);
        if (file.size > batas(file) && adalahVideo(file)) {
            tampilPesan('Ukuran video terlalu besar. Maksimal 9 MB.');
        }
    }

    semua.forEach(function (input) {
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (file) { proses(input, file); }
        });
    });

    tombolHapus.addEventListener('click', kosongkan);

    // Seret & lepas file (laptop).
    ['dragenter', 'dragover'].forEach(function (ev) {
        zona.addEventListener(ev, function (e) { e.preventDefault(); zona.classList.add('bukti-seret'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
        zona.addEventListener(ev, function (e) { e.preventDefault(); zona.classList.remove('bukti-seret'); });
    });
    zona.addEventListener('drop', function (e) {
        var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file) { return; }
        try {
            var dt = new DataTransfer();
            dt.items.add(file);
            inputFile.files = dt.files;
            proses(inputFile, file);
        } catch (err) {
            tampilPesan('Browser ini tidak mendukung seret file. Gunakan tombol Foto, Video, atau File.');
        }
    });

    var tombolKirim = form.querySelector('button[type="submit"]');
    var teksTombol = tombolKirim ? tombolKirim.textContent : '';

    // Kembali dari halaman lain lewat tombol Back: aktifkan lagi tombol kirim.
    window.addEventListener('pageshow', function () {
        form.dataset.terkirim = '';
        if (tombolKirim) { tombolKirim.disabled = false; tombolKirim.textContent = teksTombol; }
    });

    form.addEventListener('submit', function (e) {
        // Klik ganda / Enter berulang: hanya satu pengiriman yang diteruskan.
        if (form.dataset.terkirim === '1') {
            e.preventDefault();
            return;
        }
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity();
            return;
        }
        var file = terpilih();
        if (!file) {
            e.preventDefault();
            tampilPesan('Bukti foto atau video wajib dilampirkan.');
            zona.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (file.size > batas(file)) {
            e.preventDefault();
            tampilPesan(adalahVideo(file)
                ? 'Ukuran video terlalu besar. Maksimal 9 MB.'
                : 'Ukuran foto terlalu besar. Maksimal 5 MB.');
        } else {
            form.dataset.terkirim = '1';
            if (tombolKirim) { tombolKirim.disabled = true; tombolKirim.textContent = 'Mengirim...'; }
        }
    });
})();
