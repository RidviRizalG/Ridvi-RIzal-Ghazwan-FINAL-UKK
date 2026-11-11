// Lonceng notifikasi siswa: badge jumlah yang belum dilihat + buka/tutup panel.
// "Sudah dilihat" disimpan per siswa di localStorage (waktu notifikasi terbaru yang pernah dibuka).
(() => {
    const wrap = document.querySelector('[data-notif]');
    if (!wrap) return;

    const toggle = wrap.querySelector('[data-notif-toggle]');
    const panel = wrap.querySelector('[data-notif-panel]');
    const badge = wrap.querySelector('[data-notif-badge]');
    const items = Array.from(wrap.querySelectorAll('[data-notif-ts]'));
    const key = 'sarpras-notif-seen-' + (wrap.dataset.notifUser || '');

    const read = () => { try { return parseInt(localStorage.getItem(key), 10) || 0; } catch (e) { return 0; } };
    const write = (v) => { try { localStorage.setItem(key, String(v)); } catch (e) {} };

    const newest = items.reduce((m, el) => Math.max(m, parseInt(el.dataset.notifTs, 10) || 0), 0);
    const seen = read();

    items.forEach(el => {
        if ((parseInt(el.dataset.notifTs, 10) || 0) > seen) el.classList.add('is-new');
    });
    const unread = items.filter(el => el.classList.contains('is-new')).length;
    if (unread > 0) {
        badge.textContent = unread > 9 ? '9+' : String(unread);
        badge.hidden = false;
    }

    const setOpen = (open) => {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        if (open) {
            // Tandai sudah dilihat; sorotan "baru" tetap tampil sampai halaman dimuat ulang.
            write(newest);
            badge.hidden = true;
        }
    };

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        setOpen(panel.hidden);
    });
    document.addEventListener('click', (e) => {
        if (!panel.hidden && !wrap.contains(e.target)) setOpen(false);
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.hidden) setOpen(false);
    });
})();
