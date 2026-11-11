(() => {
    const key = 'sarpras-theme';
    const ASSET = '../ASSETS/';
    const ICON_TO_LIGHT = ASSET + 'tema_terang.png';
    const ICON_TO_DARK = ASSET + 'tema_gelap.png';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        document.querySelectorAll('[data-theme-toggle]').forEach(button => {
            const dark = theme === 'dark';
            const img = button.querySelector('img') || document.createElement('img');
            img.src = dark ? ICON_TO_LIGHT : ICON_TO_DARK;
            img.alt = '';
            if (!img.parentNode) button.appendChild(img);
            button.setAttribute('aria-label', dark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap');
            button.title = dark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap';
            button.setAttribute('aria-pressed', String(dark));
        });

        document.querySelectorAll('img[src*="logo_sekolah"]').forEach(img => {
            // Logo di navbar (latar marun) selalu memakai versi tema gelap agar tetap terlihat jelas.
            if (img.closest('.navbar')) return;
            img.src = ASSET + (theme === 'dark' ? 'logo_sekolah_temagelap.png' : 'logo_sekolah.png');
        });
    }

    let saved = null;
    try { saved = localStorage.getItem(key); } catch (e) { /* penyimpanan diblokir: pakai tema terang */ }
    applyTheme(saved === 'dark' ? 'dark' : 'light');
    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            try { localStorage.setItem(key, next); } catch (e) { /* abaikan */ }
            applyTheme(next);
        });
    });
})();
