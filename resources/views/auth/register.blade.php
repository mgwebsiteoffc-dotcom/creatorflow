<x-layouts.app panel="guest" title="Create account">
    <section class="relative min-h-[calc(100vh-4rem)] overflow-hidden">
        <div class="aurora"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-10 md:grid-cols-5 md:gap-14 md:py-16">
            {{-- LEFT: Form (3/5) --}}
            <div class="md:col-span-3">
                <div class="g-border p-1 shadow-[0_30px_80px_-40px_rgba(15,23,42,.25)]">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 md:p-10">
                        <span class="chip">
                            <span class="chip-dot"></span> Free · No card required
                        </span>
                        <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                            Create your <span class="text-gradient">CreatorFlow</span> account
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            Two doors. One product. Pick how you'll use CreatorFlow.
                        </p>

                        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                            @csrf

                            {{-- Account type toggle (visual, keeps hidden inputs) --}}
                            <div>
                                <label class="label">I am a…</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="group relative cursor-pointer overflow-hidden rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50 has-[:checked]:shadow-[0_10px_30px_-15px_rgba(124,58,237,.5)]">
                                        <input type="radio" name="account_type" value="brand" class="peer sr-only" {{ old('account_type','brand') === 'brand' ? 'checked' : '' }}>
                                        <div class="flex items-start gap-3">
                                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-500 text-lg text-white">🛍️</span>
                                            <div>
                                                <div class="text-sm font-bold text-slate-900">Brand / Merchant</div>
                                                <div class="mt-0.5 text-xs text-slate-500">Run creator campaigns, seed products, attribute revenue.</div>
                                            </div>
                                        </div>
                                        <span class="pointer-events-none absolute right-3 top-3 hidden h-5 w-5 place-items-center rounded-full bg-violet-600 text-[10px] text-white peer-checked:grid">✓</span>
                                    </label>

                                    <label class="group relative cursor-pointer overflow-hidden rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:shadow-[0_10px_30px_-15px_rgba(244,63,94,.5)]">
                                        <input type="radio" name="account_type" value="creator" class="peer sr-only" {{ old('account_type') === 'creator' ? 'checked' : '' }}>
                                        <div class="flex items-start gap-3">
                                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 text-lg text-white">🎬</span>
                                            <div>
                                                <div class="text-sm font-bold text-slate-900">Creator</div>
                                                <div class="mt-0.5 text-xs text-slate-500">Find brand collabs, get paid on time, keep the free products.</div>
                                            </div>
                                        </div>
                                        <span class="pointer-events-none absolute right-3 top-3 hidden h-5 w-5 place-items-center rounded-full bg-rose-600 text-[10px] text-white peer-checked:grid">✓</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label">Full name</label>
                                    <input class="input" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Alex Rivera">
                                </div>
                                <div>
                                    <label class="label">Work email</label>
                                    <input class="input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@brand.com">
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label">Password</label>
                                    <input class="input" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters">
                                </div>
                                <div>
                                    <label class="label">Confirm password</label>
                                    <input class="input" type="password" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <label class="flex items-start gap-2 text-xs text-slate-500">
                                <input type="checkbox" required class="mt-0.5 h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400">
                                <span>I agree to the <a href="#" class="font-semibold text-violet-700 hover:text-violet-900">Terms</a> and <a href="#" class="font-semibold text-violet-700 hover:text-violet-900">Privacy Policy</a>.</span>
                            </label>

                            <button class="btn-gradient w-full">
                                Create free account
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-500">
                            Already have one?
                            <a href="{{ route('login') }}" class="font-semibold text-violet-700 hover:text-violet-900">Sign in →</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Benefits (2/5) --}}
            <div class="md:col-span-2">
                <div class="sticky top-24 space-y-5">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-violet-700">Why CreatorFlow</h3>
                        <ul class="mt-4 space-y-4 text-sm text-slate-700">
                            @foreach([
                                ['Free forever plan', '5 campaigns/mo · unlimited creators'],
                                ['AI campaign briefs', 'From product to launch-ready in minutes'],
                                ['Native Shopify sync', 'Products, orders, tracking — automatic'],
                                ['Real attribution', 'Unique codes + referral links + multi-touch'],
                            ] as $benefit)
                                <li class="flex gap-3">
                                    <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white">✓</span>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $benefit[0] }}</div>
                                        <div class="text-xs text-slate-500">{{ $benefit[1] }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-3xl bg-slate-950 p-6 text-white">
                        <div class="flex -space-x-2">
                            @foreach(['#f472b6','#a78bfa','#22d3ee','#f59e0b','#34d399'] as $c)
                                <span class="h-8 w-8 rounded-full ring-2 ring-slate-950" style="background:{{ $c }}"></span>
                            @endforeach
                        </div>
                        <p class="mt-4 text-sm">
                            <span class="font-black text-white">Join 100K+ creators</span> and
                            <span class="font-black text-white">1,000+ brands</span> already shipping campaigns.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
