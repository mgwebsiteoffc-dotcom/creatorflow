<footer class="relative mt-24 overflow-hidden border-t border-slate-200 bg-slate-950 text-slate-300">
    <div class="pointer-events-none absolute inset-0 opacity-40"
         style="background: radial-gradient(600px 300px at 20% 0%, rgba(124,58,237,.35), transparent 60%), radial-gradient(600px 300px at 80% 100%, rgba(236,72,153,.25), transparent 60%);"></div>

    <div class="relative mx-auto grid max-w-6xl gap-10 px-4 py-14 md:grid-cols-4">
        <div class="md:col-span-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-white">
                <span class="grid h-9 w-9 place-items-center rounded-xl text-white"
                      style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">
                    <span class="text-sm font-black">CF</span>
                </span>
                <span>CreatorFlow</span>
            </a>
            <p class="mt-4 max-w-md text-sm text-slate-400">
                The AI creator-commerce platform. Turn products into campaigns, seed creators in bulk,
                and attribute real revenue — from Shopify or any product source.
            </p>
            <div class="mt-6 flex gap-3">
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="Instagram">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="TikTok">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 3v3.2a5 5 0 003.2 1.2v3a8 8 0 01-4.2-1.2v6.6a5.5 5.5 0 11-5.5-5.5c.3 0 .6 0 .8.1v3.1a2.5 2.5 0 102 2.4V3h3.7z"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="YouTube">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.6 7.2a2.5 2.5 0 00-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 002.4 7.2 26 26 0 002 12a26 26 0 00.4 4.8 2.5 2.5 0 001.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 001.8-1.8A26 26 0 0022 12a26 26 0 00-.4-4.8zM10 15V9l5 3-5 3z"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="X">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5 3H21l-7.5 8.6L22 21h-6.9l-4.7-6.1L4.9 21H1.4l8-9.2L1.6 3h7l4.3 5.7L17.5 3z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-white">Product</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="#platform" class="hover:text-white">Platform</a></li>
                <li><a href="#campaigns" class="hover:text-white">Campaign types</a></li>
                <li><a href="#creators" class="hover:text-white">Creators</a></li>
                <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                <li><a href="{{ route('shopify.install') }}" class="hover:text-white">Shopify app</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-white">Company</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="#" class="hover:text-white">About</a></li>
                <li><a href="#" class="hover:text-white">Blog</a></li>
                <li><a href="#" class="hover:text-white">Careers</a></li>
                <li><a href="#" class="hover:text-white">Contact</a></li>
                <li><a href="#" class="hover:text-white">Privacy · Terms</a></li>
            </ul>
        </div>
    </div>

    <div class="relative border-t border-white/10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-slate-400 sm:flex-row">
            <span>© {{ date('Y') }} CreatorFlow. Built for creators & brands.</span>
            <span class="flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                All systems operational
            </span>
        </div>
    </div>
</footer>
