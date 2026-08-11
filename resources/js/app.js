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

    /* Onboarding wizard: [data-wizard] with [data-step="1"] blocks and [data-next]/[data-prev] buttons */
    document.querySelectorAll('[data-wizard]').forEach((wiz) => {
        const steps = Array.from(wiz.querySelectorAll('[data-step]'));
        const dots = Array.from(wiz.querySelectorAll('[data-dot]'));
        const bars = Array.from(wiz.querySelectorAll('[data-bar]'));
        const total = steps.length;
        let current = 1;

        const render = () => {
            steps.forEach((s) => s.classList.toggle('is-active', Number(s.dataset.step) === current));
            dots.forEach((d) => {
                const n = Number(d.dataset.dot);
                d.classList.toggle('is-active', n === current);
                d.classList.toggle('is-done', n < current);
                if (n < current) d.textContent = '✓';
                else d.textContent = n;
            });
            bars.forEach((b) => {
                const n = Number(b.dataset.bar);
                b.classList.toggle('is-done', n < current);
            });
            const progress = wiz.querySelector('[data-progress]');
            if (progress) progress.textContent = `Step ${current} of ${total}`;
            // scroll wizard into view smoothly
            wiz.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const validateStep = (stepEl) => {
            let ok = true;
            stepEl.querySelectorAll('[data-required]').forEach((field) => {
                // required checkbox group: at least one checked
                if (field.dataset.required === 'group') {
                    const name = field.dataset.name;
                    const anyChecked = wiz.querySelectorAll(`input[name="${name}"]:checked, input[name="${name}[]"]:checked`).length > 0;
                    field.classList.toggle('ring-2', !anyChecked);
                    field.classList.toggle('ring-rose-400', !anyChecked);
                    if (!anyChecked) ok = false;
                } else if (field.value === '' || field.value == null) {
                    field.classList.add('ring-2', 'ring-rose-400');
                    ok = false;
                } else {
                    field.classList.remove('ring-2', 'ring-rose-400');
                }
            });
            return ok;
        };

        wiz.addEventListener('click', (e) => {
            const next = e.target.closest('[data-next]');
            const prev = e.target.closest('[data-prev]');
            const jump = e.target.closest('[data-jump]');
            if (next) {
                e.preventDefault();
                const cur = steps.find((s) => Number(s.dataset.step) === current);
                if (!validateStep(cur)) return;
                if (current < total) { current++; render(); }
            } else if (prev) {
                e.preventDefault();
                if (current > 1) { current--; render(); }
            } else if (jump) {
                e.preventDefault();
                const n = Number(jump.dataset.jump);
                if (n <= current) { current = n; render(); }
            }
        });

        render();
    });

    /* Auto-dismissing flashes + close button */
    document.querySelectorAll('[data-flash]').forEach((el) => {
        const close = () => { el.style.transition = 'opacity .3s ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 300); };
        el.querySelector('[data-flash-close]')?.addEventListener('click', close);
        setTimeout(close, 6000);
    });
});
