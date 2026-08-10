import './bootstrap';

/* Lightweight progressive enhancements (no framework dependency):
 *   - PWA install prompt
 *   - auto-dismissing flashes
 *   - confirm for destructive actions
 */
document.addEventListener('DOMContentLoaded', () => {
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const btn = document.getElementById('pwa-install');
        if (btn) btn.classList.remove('hidden');
    });

    const installBtn = document.getElementById('pwa-install');
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            installBtn.classList.add('hidden');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('submit', (e) => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });
});
