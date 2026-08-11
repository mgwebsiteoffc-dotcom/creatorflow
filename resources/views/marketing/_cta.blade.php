@props(['title' => 'Ready to run creator campaigns that actually convert?', 'sub' => 'Start free — the Shopify app and web brand panel share the same engine. No card required.'])
<section class="mx-auto max-w-6xl px-4 pb-20">
    <div class="reveal relative overflow-hidden rounded-3xl p-10 text-white md:p-16"
         style="background-image: linear-gradient(120deg, #0f172a 0%, #4c1d95 40%, #db2777 80%, #f59e0b 110%);">
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-cyan-400/30 blur-3xl"></div>
        <div class="relative max-w-2xl">
            <span class="chip !border-white/20 !bg-white/10 !text-white"><span class="chip-dot !bg-white"></span> One product · two doors</span>
            <h2 class="mt-4 text-3xl font-black leading-tight md:text-5xl">{{ $title }}</h2>
            <p class="mt-4 max-w-xl text-white/85">{{ $sub }}</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="btn-glass !text-slate-900">Create account</a>
                <a href="{{ route('shopify.install') }}" class="btn-gradient">Install on Shopify</a>
            </div>
        </div>
    </div>
</section>
