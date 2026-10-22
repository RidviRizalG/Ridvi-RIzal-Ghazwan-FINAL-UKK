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
            const isDashboardLogo = img.classList.contains('dashboard-hero-logo');
            const base = isDashboardLogo ? 'logo_sekolah' : 'logo_sekolah';
            img.src = ASSET + (theme === 'dark' ? 'logo_sekolah_temagelap.png' : 'logo_sekolah.png');
        });
    }

    const saved = localStorage.getItem(key);
    applyTheme(saved === 'dark' ? 'dark' : 'light');
    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            localStorage.setItem(key, next);
            applyTheme(next);
        });
    });
})();
