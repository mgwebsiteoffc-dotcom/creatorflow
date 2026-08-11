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

    /* Onboarding wizard: [data-wizard] with [data-step="1"] blocks and [data-next]/[data-prev] buttons.
       Rules:
       - Only PAST steps that were successfully advanced past show as "done" (green ✓).
       - Current step shows as "active" (gradient).
       - Future steps and never-visited past ones show as neutral.
       - Users can only JUMP to steps they have already visited.
       - Wizard blocks form submission unless we are on the last step and it was reached via a full Next flow.
    */
    document.querySelectorAll('[data-wizard]').forEach((wiz) => {
        const steps = Array.from(wiz.querySelectorAll('[data-step]'));
        const dots = Array.from(wiz.querySelectorAll('[data-dot]'));
        const bars = Array.from(wiz.querySelectorAll('[data-bar]'));
        const total = steps.length;
        // If URL has ?step=N or the wizard element has data-start-step, hydrate
        const urlStep = Number(new URL(window.location.href).searchParams.get('step') || 0);
        const startStep = urlStep || Number(wiz.dataset.startStep || 1) || 1;
        let current = Math.min(Math.max(startStep, 1), total);
        // Track the highest step the user has legitimately reached via Next (validation-passing).
        // This is what turns dots green. Bumping backwards via Back doesn't un-green.
        let reached = current;

        const render = () => {
            steps.forEach((s) => s.classList.toggle('is-active', Number(s.dataset.step) === current));
            dots.forEach((d) => {
                const n = Number(d.dataset.dot);
                const isActive = n === current;
                const isDone = n < reached;                 // completed = strictly before the highest reached step
                d.classList.toggle('is-active', isActive);
                d.classList.toggle('is-done', isDone && !isActive);
                d.textContent = (isDone && !isActive) ? '✓' : n;
                d.style.cursor = (n <= reached) ? 'pointer' : 'not-allowed';
                d.disabled = n > reached;
                d.setAttribute('aria-current', isActive ? 'step' : 'false');
            });
            bars.forEach((b) => {
                const n = Number(b.dataset.bar);
                b.classList.toggle('is-done', n < reached);
            });
            const progress = wiz.querySelector('[data-progress]');
            if (progress) progress.textContent = `Step ${current} of ${total}`;
            wiz.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const clearFieldError = (field) => {
            field.classList.remove('ring-2', 'ring-rose-400');
            const holder = field.closest('[data-field]') || field.parentElement;
            holder?.querySelector('[data-field-error]')?.remove();
        };
        const setFieldError = (field, msg) => {
            field.classList.add('ring-2', 'ring-rose-400');
            const holder = field.closest('[data-field]') || field.parentElement;
            if (!holder) return;
            if (holder.querySelector('[data-field-error]')) return;
            const err = document.createElement('p');
            err.setAttribute('data-field-error', '');
            err.className = 'mt-1 text-xs font-medium text-rose-600';
            err.textContent = msg;
            holder.appendChild(err);
        };

        const validateStep = (stepEl) => {
            let ok = true;
            let firstBad = null;
            stepEl.querySelectorAll('[data-required]').forEach((field) => {
                if (field.dataset.required === 'group') {
                    const name = field.dataset.name;
                    const min = Number(field.dataset.min || 1);
                    const count = wiz.querySelectorAll(
                        `input[name="${name}"]:checked, input[name="${name}[]"]:checked`
                    ).length;
                    const wrap = field;
                    wrap.classList.toggle('ring-2', count < min);
                    wrap.classList.toggle('ring-rose-400', count < min);
                    wrap.classList.toggle('rounded-2xl', count < min);
                    let msgEl = wrap.parentElement.querySelector('[data-field-error]');
                    if (count < min) {
                        if (!msgEl) {
                            msgEl = document.createElement('p');
                            msgEl.setAttribute('data-field-error', '');
                            msgEl.className = 'mt-1 text-xs font-medium text-rose-600';
                            wrap.parentElement.appendChild(msgEl);
                        }
                        msgEl.textContent = `Please pick at least ${min}.`;
                        ok = false;
                        if (!firstBad) firstBad = wrap;
                    } else if (msgEl) { msgEl.remove(); }
                } else {
                    const val = (field.value ?? '').toString().trim();
                    if (val === '') {
                        setFieldError(field, 'This field is required.');
                        ok = false;
                        if (!firstBad) firstBad = field;
                    } else {
                        clearFieldError(field);
                    }
                }
            });
            if (firstBad) firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
                if (current < total) {
                    current++;
                    if (current > reached) reached = current;
                    render();
                }
            } else if (prev) {
                e.preventDefault();
                if (current > 1) { current--; render(); }
            } else if (jump) {
                e.preventDefault();
                const n = Number(jump.dataset.jump);
                if (n <= reached) { current = n; render(); }
            }
        });

        // Guard against submits triggered from anywhere but the last step's explicit submit button.
        if (wiz.tagName === 'FORM') {
            wiz.addEventListener('submit', (e) => {
                if (current !== total) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Validate current step so the user sees what's wrong
                    const cur = steps.find((s) => Number(s.dataset.step) === current);
                    if (cur && validateStep(cur) && current < total) {
                        current++;
                        if (current > reached) reached = current;
                        render();
                    }
                    return false;
                }
                // On last step — validate it too, block if invalid
                const cur = steps.find((s) => Number(s.dataset.step) === total);
                if (cur && !validateStep(cur)) { e.preventDefault(); return false; }
            });
        }

        // Prevent Enter key from submitting the wizard form from any step but the last
        wiz.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.tagName === 'INPUT' && wiz.tagName === 'FORM' && current !== total) {
                e.preventDefault();
                const cur = steps.find((s) => Number(s.dataset.step) === current);
                if (validateStep(cur)) {
                    current++;
                    if (current > reached) reached = current;
                    render();
                }
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
