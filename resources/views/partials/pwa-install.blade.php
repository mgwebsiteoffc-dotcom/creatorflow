{{-- PWA install prompt: shows once when Chrome/Edge/Samsung fires beforeinstallprompt,
     or a Safari-specific instruction sheet on iOS. Auto-suppresses when already installed,
     when the user dismisses, or when snoozed within the last 7 days. --}}
<div id="pwa-install-sheet" class="pointer-events-none fixed inset-x-0 bottom-0 z-40 hidden md:bottom-6 md:right-6 md:left-auto md:max-w-sm">
    <div class="pointer-events-auto mx-3 mb-16 md:mb-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl ring-1 ring-black/5">
        <div class="flex items-start gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl text-white shadow-sm" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">
                <span class="text-lg font-black">CP</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-black text-slate-900">Install CreatorPlex</p>
                <p class="mt-0.5 text-xs text-slate-500" data-pwa-body>Faster than the browser. Home-screen icon + offline access.</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <button id="pwa-install-cta" class="btn-primary !py-1.5 !text-xs">📲 Install</button>
                    <button id="pwa-install-later" class="btn-ghost !py-1.5 !text-xs">Later</button>
                </div>
            </div>
            <button id="pwa-install-close" class="text-slate-400 hover:text-slate-700" aria-label="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const sheet    = document.getElementById('pwa-install-sheet');
    const cta      = document.getElementById('pwa-install-cta');
    const later    = document.getElementById('pwa-install-later');
    const close    = document.getElementById('pwa-install-close');
    const body     = sheet.querySelector('[data-pwa-body]');
    const KEY      = 'pwa_install_snoozed_until';
    let deferred   = null;

    const isStandalone = () => matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    const isIOSSafari = () => {
        const ua = navigator.userAgent;
        return /iphone|ipad|ipod/i.test(ua) && !/crios|fxios/i.test(ua);
    };

    const snoozed = () => {
        const until = Number(localStorage.getItem(KEY) || 0);
        return until && Date.now() < until;
    };
    const snooze = (days) => localStorage.setItem(KEY, String(Date.now() + days*86400000));

    const show = () => {
        if (isStandalone() || snoozed()) return;
        sheet.classList.remove('hidden');
    };
    const hide = () => sheet.classList.add('hidden');

    // Chrome / Edge / Samsung → intercepts the browser's own bar.
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferred = e;
        // Give the user 4 seconds to look at the page before nudging them.
        setTimeout(show, 4000);
    });

    // iOS Safari has no beforeinstallprompt — show the "tap Share → Add to Home Screen" hint.
    if (isIOSSafari() && !isStandalone() && !snoozed()) {
        cta.style.display = 'none';
        body.innerHTML = 'Tap the <strong>Share</strong> icon in Safari, then choose <strong>Add to Home Screen</strong>.';
        setTimeout(show, 6000);
    }

    cta?.addEventListener('click', async () => {
        if (!deferred) return;
        deferred.prompt();
        const { outcome } = await deferred.userChoice;
        deferred = null;
        if (outcome === 'dismissed') snooze(3);
        hide();
    });
    later?.addEventListener('click', () => { snooze(7); hide(); });
    close?.addEventListener('click', () => { snooze(7); hide(); });

    // If the browser already installed it after showing the sheet, disappear cleanly.
    window.addEventListener('appinstalled', () => { snooze(365); hide(); });
})();
</script>
