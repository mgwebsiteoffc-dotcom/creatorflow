<x-layouts.app panel="guest" title="Contact">
    <section class="relative overflow-hidden">
        <div class="aurora"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 md:grid-cols-2 md:gap-16 md:py-24">
            <div>
                <span class="chip"><span class="chip-dot"></span> Contact us</span>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                    Let's <span class="text-gradient">talk creator campaigns</span>
                </h1>
                <p class="mt-4 max-w-md text-slate-600">
                    Sales, partnerships, press, or you just want a demo — pick your lane. We reply within 1 business day.
                </p>

                <div class="mt-10 space-y-4">
                    @foreach([
                        ['💼', 'Sales & demos', 'sales@creatorflow.app'],
                        ['🤝', 'Partnerships',  'partners@creatorflow.app'],
                        ['📰', 'Press',         'press@creatorflow.app'],
                        ['🛟', 'Support',        'help@creatorflow.app'],
                    ] as $c)
                        <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-slate-100 text-lg">{{ $c[0] }}</span>
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $c[1] }}</div>
                                <div class="text-sm text-slate-600">{{ $c[2] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 rounded-2xl bg-slate-950 p-5 text-white">
                    <div class="text-xs font-semibold uppercase tracking-widest opacity-70">Global · remote-first</div>
                    <div class="mt-1 text-lg font-bold">HQ · Delhi NCR &amp; SF</div>
                    <div class="mt-2 text-sm opacity-80">Support in English &amp; Hindi. Timezones covered 22h/day.</div>
                </div>
            </div>

            <div class="reveal">
                <div class="g-border p-1 shadow-xl">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 md:p-8">
                        <h2 class="text-xl font-bold text-slate-900">Send us a message</h2>
                        <p class="mt-1 text-sm text-slate-500">We'll reply within one business day.</p>

                        <form class="mt-6 space-y-4" onsubmit="event.preventDefault(); this.querySelector('[data-ok]').classList.remove('hidden'); this.reset();">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label">Full name</label>
                                    <input class="input" required placeholder="Your name">
                                </div>
                                <div>
                                    <label class="label">Work email</label>
                                    <input class="input" type="email" required placeholder="you@brand.com">
                                </div>
                            </div>
                            <div>
                                <label class="label">Brand / company</label>
                                <input class="input" placeholder="Glow & Co.">
                            </div>
                            <div>
                                <label class="label">I'm here for…</label>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    @foreach(['Demo','Pricing','Partnership','Other'] as $r)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="reason" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                            <div class="pick-tile">{{ $r }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label class="label">Message</label>
                                <textarea class="input min-h-32" placeholder="Tell us about your goals…"></textarea>
                            </div>
                            <button class="btn-gradient w-full">Send message</button>
                            <div data-ok class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                                ✓ Thanks! We'll be in touch within one business day.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
