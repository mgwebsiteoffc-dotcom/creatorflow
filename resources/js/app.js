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

    /* Reel carousel: [data-reel] scroll container + [data-reel-prev]/[data-reel-next]
       Also handles [data-reel-play] play buttons and [data-reel-filter] category tabs. */
    document.querySelectorAll('[data-reel]').forEach((reel) => {
        const step = () => {
            const visible = Array.from(reel.querySelectorAll('article'))
                .find(a => a.style.display !== 'none');
            return visible ? visible.getBoundingClientRect().width + 16 : 260;
        };
        const scrollBy = (dir) => reel.scrollBy({ left: dir * step(), behavior: 'smooth' });
        document.querySelectorAll('[data-reel-prev]').forEach((b) => b.addEventListener('click', () => scrollBy(-1)));
        document.querySelectorAll('[data-reel-next]').forEach((b) => b.addEventListener('click', () => scrollBy(1)));

        // Play buttons — play the video, hide the button while playing
        reel.querySelectorAll('[data-reel-play]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const card = btn.closest('article');
                const video = card?.querySelector('[data-reel-video]');
                if (video) {
                    // Pause any other playing reel first
                    reel.querySelectorAll('[data-reel-video]').forEach(v => {
                        if (v !== video) { try { v.pause(); v.currentTime = 0; } catch {} }
                    });
                    reel.querySelectorAll('[data-reel-play]').forEach(b => b.classList.remove('opacity-0'));

                    video.play().then(() => {
                        btn.classList.add('opacity-0');
                    }).catch(() => {});
                    video.addEventListener('pause', () => btn.classList.remove('opacity-0'), { once: true });
                    video.addEventListener('ended', () => btn.classList.remove('opacity-0'), { once: true });
                }
            });
        });

        // Category filter tabs
        const filterBar = document.querySelector('[data-reel-filter]');
        if (filterBar) {
            filterBar.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-cat]');
                if (!btn) return;
                const cat = btn.dataset.cat;
                filterBar.querySelectorAll('[data-cat]').forEach(b => b.classList.toggle('is-active', b === btn));
                reel.querySelectorAll('[data-reel-card]').forEach((card) => {
                    const show = cat === 'all' || card.dataset.cat === cat;
                    card.style.display = show ? '' : 'none';
                });
                reel.scrollTo({ left: 0, behavior: 'smooth' });
            });
        }

        // Drag-to-scroll (desktop)
        let down = false, startX = 0, startLeft = 0;
        reel.addEventListener('pointerdown', (e) => {
            // Don't drag when clicking on a play button
            if (e.target.closest('[data-reel-play], video')) return;
            down = true; startX = e.clientX; startLeft = reel.scrollLeft;
            reel.setPointerCapture(e.pointerId); reel.classList.add('cursor-grabbing');
        });
        reel.addEventListener('pointerup',   () => { down = false; reel.classList.remove('cursor-grabbing'); });
        reel.addEventListener('pointercancel',() => { down = false; reel.classList.remove('cursor-grabbing'); });
        reel.addEventListener('pointermove', (e) => { if (!down) return; reel.scrollLeft = startLeft - (e.clientX - startX); });
    });

    /* Notifications dropdown */
    document.querySelectorAll('[data-notif-wrap]').forEach((wrap) => {
        const btn = wrap.querySelector('[data-notif-toggle]');
        const panel = wrap.querySelector('[data-notif-panel]');
        if (!btn || !panel) return;
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (! wrap.contains(e.target)) panel.classList.add('hidden');
        });
    });

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

    /* Markdown mini-editor with live preview.
       Applied to any [data-md-editor] wrapper. Shares the same server-side
       renderer's grammar (headings, bold, italic, links, lists, blockquote, code).
       Preview is done in JS with a small parser mirroring App\Support\BriefMarkdown. */
    const mdInline = (t) => {
        t = t.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const codes = [];
        t = t.replace(/`([^`]+)`/g, (_, c) => { codes.push(`<code>${c}</code>`); return `\x01CODE${codes.length-1}\x01`; });
        t = t.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/gi, (_, txt, url) => `<a href="${url}" target="_blank" rel="noopener">${txt}</a>`);
        t = t.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
        t = t.replace(/__([^_\n]+)__/g, '<strong>$1</strong>');
        t = t.replace(/(^|[^\*\w])\*([^*\n]+)\*(?!\w)/g, '$1<em>$2</em>');
        t = t.replace(/(^|[^_\w])_([^_\n]+)_(?!\w)/g, '$1<em>$2</em>');
        t = t.replace(/\x01CODE(\d+)\x01/g, (_, i) => codes[+i] || '');
        return t;
    };
    const mdRender = (md) => {
        if (!md || !md.trim()) return '<p class="text-sm italic text-slate-400">Nothing to preview.</p>';
        const lines = md.replace(/\r\n?/g, '\n').split('\n');
        let html = '', listStack = [], inBq = false, buf = [];
        const flushPara = () => { if (buf.length) { html += '<p>' + mdInline(buf.join(' ')) + '</p>'; buf = []; } };
        const closeLists = () => { while (listStack.length) html += `</${listStack.pop()}>`; };
        const closeBq = () => { if (inBq) { html += '</blockquote>'; inBq = false; } };
        for (const raw of lines) {
            const line = raw.replace(/\s+$/, '');
            if (line === '') { flushPara(); closeLists(); closeBq(); continue; }
            let m;
            if (/^\s*(---|\*\*\*|___)\s*$/.test(line)) { flushPara(); closeLists(); closeBq(); html += '<hr>'; continue; }
            if ((m = line.match(/^(#{1,3})\s+(.+)$/))) { flushPara(); closeLists(); closeBq(); html += `<h${m[1].length}>${mdInline(m[2])}</h${m[1].length}>`; continue; }
            if ((m = line.match(/^>\s?(.*)$/))) { flushPara(); closeLists(); if (!inBq) { html += '<blockquote>'; inBq = true; } html += `<p>${mdInline(m[1])}</p>`; continue; }
            if ((m = line.match(/^\s*\d+\.\s+(.+)$/))) { flushPara(); closeBq(); if (!listStack.length || listStack.at(-1) !== 'ol') { if (listStack.length) html += `</${listStack.pop()}>`; html += '<ol>'; listStack.push('ol'); } html += `<li>${mdInline(m[1])}</li>`; continue; }
            if ((m = line.match(/^\s*[-*•]\s+(.+)$/))) { flushPara(); closeBq(); if (!listStack.length || listStack.at(-1) !== 'ul') { if (listStack.length) html += `</${listStack.pop()}>`; html += '<ul>'; listStack.push('ul'); } html += `<li>${mdInline(m[1])}</li>`; continue; }
            closeLists(); closeBq(); buf.push(line);
        }
        flushPara(); closeLists(); closeBq();
        return html;
    };

    document.querySelectorAll('[data-md-editor]').forEach((wrap) => {
        const ta = wrap.querySelector('[data-md-textarea]');
        const preview = wrap.querySelector('[data-md-preview]');
        const modes = wrap.querySelectorAll('[data-md-mode]');
        const tools = wrap.querySelectorAll('[data-md]');
        if (!ta) return;

        const setMode = (mode) => {
            modes.forEach(b => b.classList.toggle('is-active', b.dataset.mdMode === mode));
            if (mode === 'preview') {
                preview.innerHTML = `<div class="brief-body">${mdRender(ta.value)}</div>`;
                preview.classList.remove('hidden');
                ta.classList.add('hidden');
            } else {
                preview.classList.add('hidden');
                ta.classList.remove('hidden');
            }
        };
        modes.forEach(b => b.addEventListener('click', () => setMode(b.dataset.mdMode)));

        const wrapSel = (before, after = before, placeholder = '') => {
            const start = ta.selectionStart, end = ta.selectionEnd;
            const sel = ta.value.slice(start, end) || placeholder;
            ta.value = ta.value.slice(0, start) + before + sel + after + ta.value.slice(end);
            ta.focus();
            ta.setSelectionRange(start + before.length, start + before.length + sel.length);
        };
        const linePrefix = (prefix, placeholder = '') => {
            const start = ta.selectionStart;
            const before = ta.value.slice(0, start);
            const lineStart = before.lastIndexOf('\n') + 1;
            const linesAfter = ta.value.slice(lineStart).split('\n')[0] || placeholder;
            ta.value = ta.value.slice(0, lineStart) + prefix + linesAfter + ta.value.slice(lineStart + linesAfter.length);
            ta.focus();
            ta.setSelectionRange(lineStart + prefix.length, lineStart + prefix.length + linesAfter.length);
        };
        tools.forEach(btn => btn.addEventListener('click', () => {
            const kind = btn.dataset.md;
            if (kind === 'b') wrapSel('**','**','bold text');
            else if (kind === 'i') wrapSel('*','*','italic text');
            else if (kind === 'code') wrapSel('`','`','code');
            else if (kind === 'link') {
                const url = prompt('Link URL', 'https://');
                if (url) wrapSel('[', `](${url})`, 'link text');
            }
            else if (kind === 'h2') linePrefix('## ', 'Heading');
            else if (kind === 'ul') linePrefix('- ', 'List item');
            else if (kind === 'ol') linePrefix('1. ', 'List item');
            else if (kind === 'quote') linePrefix('> ', 'Quote');
        }));
    });

    /* Auto-dismissing flashes + close button */
    document.querySelectorAll('[data-flash]').forEach((el) => {
        const close = () => { el.style.transition = 'opacity .3s ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 300); };
        el.querySelector('[data-flash-close]')?.addEventListener('click', close);
        setTimeout(close, 6000);
    });
});
