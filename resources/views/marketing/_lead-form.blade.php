@props(['source' => 'landing', 'title' => 'Ready to launch?', 'sub' => 'Tell us about your goals — we\'ll reply within one business day.'])
<section id="lead-form" class="mx-auto max-w-5xl px-4 py-16">
    <div class="reveal g-border p-1 shadow-xl">
        <div class="grid gap-0 rounded-[calc(1.25rem-1px)] bg-white md:grid-cols-5">
            <div class="rounded-l-[calc(1.25rem-1px)] p-8 text-white md:col-span-2"
                 style="background-image: linear-gradient(140deg,#7c3aed 0%,#ec4899 60%,#f59e0b 130%);">
                <span class="chip !border-white/20 !bg-white/15 !text-white"><span class="chip-dot !bg-white"></span> Talk to sales</span>
                <h2 class="mt-4 text-2xl font-black leading-tight md:text-3xl">{{ $title }}</h2>
                <p class="mt-3 text-sm text-white/85">{{ $sub }}</p>
                <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex gap-2"><span>✓</span> Free 15-minute strategy call</li>
                    <li class="flex gap-2"><span>✓</span> Campaign plan sent by email</li>
                    <li class="flex gap-2"><span>✓</span> Zero pressure — no card required</li>
                </ul>
            </div>
            <div class="p-8 md:col-span-3">
                @if(session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">✓ {{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('leads.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="source" value="{{ $source }}">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Full name *</label>
                            <input class="input" name="name" required placeholder="Your name" value="{{ old('name') }}">
                        </div>
                        <div>
                            <label class="label">Work email *</label>
                            <input class="input" type="email" name="email" required placeholder="you@brand.com" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div>
                        <label class="label">Brand / company</label>
                        <input class="input" name="company" placeholder="Glow &amp; Co." value="{{ old('company') }}">
                    </div>
                    <div>
                        <label class="label">I'm here for…</label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach(['demo' => 'Demo','pricing' => 'Pricing','partnership' => 'Partnership','other' => 'Other'] as $v => $l)
                                <label class="cursor-pointer">
                                    <input type="radio" name="reason" value="{{ $v }}" class="sr-only" {{ old('reason', 'demo') === $v ? 'checked' : '' }}>
                                    <div class="pick-tile">{{ $l }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="label">Message</label>
                        <textarea class="input min-h-24" name="message" placeholder="Product, timeline, budget…">{{ old('message') }}</textarea>
                    </div>
                    <button class="btn-gradient w-full">Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>
