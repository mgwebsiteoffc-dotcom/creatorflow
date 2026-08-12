<x-layouts.app panel="guest" title="Sign in">
    <section class="relative min-h-[calc(100vh-4rem)] overflow-hidden">
        <div class="aurora"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 md:grid-cols-2 md:gap-16 md:py-20">
            {{-- LEFT: Form --}}
            <div class="flex items-center">
                <div class="g-border w-full p-1 shadow-[0_30px_80px_-40px_rgba(15,23,42,.25)]">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 md:p-10">
                        <div class="mb-6 flex items-center gap-2 md:hidden">
                            <span class="grid h-9 w-9 place-items-center rounded-xl text-xs font-black text-white"
                                  style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">CP</span>
                            <span class="font-bold">CreatorPlex</span>
                        </div>

                        <span class="chip">
                            <span class="chip-dot"></span>Welcome back
                        </span>
                        <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                            Sign in to <span class="text-gradient">CreatorPlex</span>
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            One account for brand and creator dashboards.
                        </p>

                        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4">
                            @csrf
                            <div>
                                <label class="label">Email</label>
                                <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@brand.com">
                            </div>
                            <div>
                                <div class="flex items-baseline justify-between">
                                    <label class="label">Password</label>
                                    <a href="#" class="text-xs font-semibold text-violet-600 hover:text-violet-800">Forgot?</a>
                                </div>
                                <input class="input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400">Remember me for 30 days
                            </label>
                            <button class="btn-gradient w-full">Sign in →</button>
                        </form>

                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                            <div class="relative flex justify-center"><span class="bg-white px-3 text-xs uppercase tracking-wider text-slate-400">or continue with</span></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" class="btn-secondary">
                                <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.4-1.6 4-5.5 4a6 6 0 110-12 5.4 5.4 0 013.9 1.5l2.6-2.5A9 9 0 1012 21a8.8 8.8 0 009-8.8c0-.6 0-1-.1-1.5H12z"/></svg>
                                Google
                            </button>
                            <button type="button" class="btn-secondary">
                                <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="#95BF47" d="M12 2l3 3 4 1-1 15-6 1-6-1L5 6l4-1 3-3z"/></svg>
                                Shopify
                            </button>
                        </div>

                        <p class="mt-8 text-center text-sm text-slate-500">
                            New here?
                            <a href="{{ route('register') }}" class="font-semibold text-violet-700 hover:text-violet-900">Create an account →</a>
                        </p>

                        <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4 text-xs text-slate-500">
                            <div class="flex items-center gap-2 font-semibold text-slate-700">Platform admin (SaaS owner)</div>
                            <p class="mt-1">Sign in with your admin email — after login you'll be redirected to <code class="rounded bg-white px-1 py-0.5">/admin</code>. Demo seed: <code class="rounded bg-white px-1 py-0.5">admin@creatorplex.test</code> · <code class="rounded bg-white px-1 py-0.5">password</code>.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Marketing rail --}}
            <div class="hidden items-center md:flex">
                <div class="relative w-full">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] bg-gradient-to-tr from-violet-300/40 via-pink-300/40 to-cyan-300/40 blur-2xl"></div>
                    <div class="relative overflow-hidden rounded-3xl p-8 text-white"
                         style="background-image: linear-gradient(160deg,#4c1d95 0%,#7c3aed 35%,#db2777 75%,#f59e0b 110%);">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest opacity-80">
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>Live now
                        </div>
                        <h2 class="mt-3 text-2xl font-black leading-tight md:text-3xl">
                            42 campaigns launched today.
                            ₹1.5Cr attributed this month.
                        </h2>
                        <p class="mt-4 max-w-sm text-sm text-white/85">
                            Real-time creator matching, AI content review, and native Shopify sync — one place.
                        </p>

                        <div class="mt-8 grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-white/10 p-3 backdrop-blur">
                                <div class="text-lg font-black">100K+</div>
                                <div class="text-[10px] uppercase tracking-wider opacity-80">Creators</div>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3 backdrop-blur">
                                <div class="text-lg font-black">1,000+</div>
                                <div class="text-[10px] uppercase tracking-wider opacity-80">Brands</div>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3 backdrop-blur">
                                <div class="text-lg font-black">4.8×</div>
                                <div class="text-[10px] uppercase tracking-wider opacity-80">Avg ROAS</div>
                            </div>
                        </div>
                    </div>

                    <figure class="reveal mt-5 rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex text-amber-500"></div>
                        <blockquote class="mt-3 text-sm text-slate-700">
                            “We shipped 3 seeding campaigns in the first week. UGC quality was so good we're now using it in paid.”
                        </blockquote>
                        <figcaption class="mt-4 flex items-center gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-sm font-bold text-white">S</span>
                            <div>
                                <div class="text-sm font-bold text-slate-900">Samsara</div>
                                <div class="text-xs text-slate-500">Head of growth · DTC food brand</div>
                            </div>
                        </figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
