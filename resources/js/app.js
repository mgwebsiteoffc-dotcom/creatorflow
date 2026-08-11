import './bootstrap';

/* Lightweight progressive enhancements (no framework dependency):
 *   - PWA install prompt
 *   - auto-dismissing flashes
 *   - confirm for destructive actions
 *   - reveal-on-scroll animations
 *   - simple tab groups ([data-tabs] + [data-tab] + [data-panel])
 *   - mobile marketing nav toggle
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

    /* Reveal on scroll */
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && revealEls.length) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('in'));
    }

    /* Simple tab groups */
    document.querySelectorAll('[data-tabs]').forEach((group) => {
        const tabs = group.querySelectorAll('[data-tab]');
        const panels = group.querySelectorAll('[data-panel]');
        const activate = (name) => {
            tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.tab === name));
            panels.forEach((p) => p.classList.toggle('hidden', p.dataset.panel !== name));
        };
        tabs.forEach((t) => t.addEventListener('click', () => activate(t.dataset.tab)));
        if (tabs.length) activate(tabs[0].dataset.tab);
    });

    /* Marketing nav toggle */
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => navMenu.classList.toggle('hidden'));
    }

    /* Auto-dismissing flashes + close button */
    document.querySelectorAll('[data-flash]').forEach((el) => {
        const close = () => { el.style.transition = 'opacity .3s ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 300); };
        el.querySelector('[data-flash-close]')?.addEventListener('click', close);
        setTimeout(close, 6000);
    });
});
