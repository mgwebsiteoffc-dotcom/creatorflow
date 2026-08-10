<x-layouts.app panel="guest" title="The AI creator-commerce platform">
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-96 bg-gradient-to-b from-violet-100 via-white to-transparent"></div>

    <section class="px-4 pt-10 md:pt-20">
        <div class="mx-auto max-w-5xl text-center">
            <span class="badge-violet mx-auto">AI-powered · Shopify + Web · One backend</span>
            <h1 class="mt-5 text-4xl font-black tracking-tight text-slate-900 sm:text-6xl">
                Turn products into
                <span class="bg-gradient-to-r from-violet-600 to-cyan-500 bg-clip-text text-transparent">creator campaigns</span>
                in one click.
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">
                CreatorFlow connects your Shopify store — or any product list — to a creator marketplace,
                AI campaign generation, bulk seeding, automatic orders, content review and sales attribution.
                Brand and creator panels are mobile-first PWAs on a single backend.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                @auth
                    <a href="{{ auth()->user()->creator ? route('creator.dashboard') : route('brand.dashboard') }}" class="btn-primary w-full sm:w-auto">Go to dashboard →</a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary w-full sm:w-auto">Start free</a>
                    <a href="{{ route('shopify.install') }}" class="btn-secondary w-full sm:w-auto">Install on Shopify</a>
                @endauth
            </div>
            <p class="mt-3 text-xs text-slate-500">No card required · Demo data is one <code class="rounded bg-slate-100 px-1.5 py-0.5">php artisan migrate:fresh --seed</code> away</p>
        </div>
    </section>

    <section class="mx-auto mt-16 grid max-w-5xl gap-4 px-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['🧠', 'AI campaign in a click', 'Connect products and AI writes the brief, picks hero products, estimates reach and ROI, and recommends creators.'],
            ['📦', 'Bulk seeding native', 'Product A → 100 creators, B → 50. Waitlists, acceptance rates, automatic Shopify orders and tracking.'],
            ['🛍️', 'Shopify-grade sync', 'Products, inventory, images, collections, discounts and orders sync via webhooks + nightly reconcile.'],
            ['🌐', 'Same platform, any business', 'CSV, manual, Woo and API onboarding feed the exact same campaign engine as Shopify.'],
            ['🎬', 'Content to approval', 'Mobile PWA upload, AI content review, change requests, contracts and usage rights.'],
            ['💰', 'Real attribution', 'Unique codes, referral links and multi-touch attribution tie creators to actual revenue.'],
        ] as $f)
            <div class="card p-5">
                <div class="text-2xl">{{ $f[0] }}</div>
                <h3 class="mt-2 font-semibold">{{ $f[1] }}</h3>
                <p class="mt-1 text-sm text-slate-600">{{ $f[2] }}</p>
            </div>
        @endforeach
    </section>

    <section class="mx-auto mt-16 max-w-5xl px-4 pb-16 text-center">
        <div class="card p-8 md:p-12">
            <h2 class="text-2xl font-bold">Two doors. One product.</h2>
            <p class="mx-auto mt-2 max-w-xl text-slate-600">
                The Shopify app is a distribution channel, not a fork. Web brands get the same campaigns,
                creators and analytics — only product sync differs.
            </p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="btn-primary">Create your account</a>
                <a href="{{ route('login') }}" class="btn-secondary">I already have one</a>
            </div>
        </div>
    </section>
</x-layouts.app>
