@props(['title', 'updated' => null, 'metaDescription' => null])
<x-layouts.app panel="guest" :title="$title" :metaDescription="$metaDescription">
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-4xl px-4 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-slate-900">Home</a> → <span>Legal</span> → <span>{{ $title }}</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">{{ $title }}</h1>
            <p class="mt-3 text-sm text-slate-500">
                Last updated: {{ $updated ?? now()->format('F Y') }} · CreatorPlex ("we", "us", "our") · Operating in India.
            </p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <a href="{{ route('legal.terms') }}" class="chip">Terms</a>
                <a href="{{ route('legal.privacy') }}" class="chip">Privacy</a>
                <a href="{{ route('legal.refund') }}" class="chip">Refund</a>
                <a href="{{ route('legal.cookies') }}" class="chip">Cookies</a>
                <a href="{{ route('legal.shipping') }}" class="chip">Shipping</a>
                <a href="{{ route('legal.content') }}" class="chip">Content guidelines</a>
                <a href="{{ route('legal.creator-agreement') }}" class="chip">Creator agreement</a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-14">
        <article class="prose prose-slate max-w-none prose-headings:tracking-tight prose-h2:mt-10 prose-h3:mt-6 prose-a:text-violet-700">
            {{ $slot }}
        </article>

        <div class="mt-16 rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-sm text-slate-600">Questions? Email <a href="mailto:hello@creatorplex.in" class="text-violet-700 hover:underline">hello@creatorplex.in</a> — we reply within one business day.</p>
        </div>
    </section>
</x-layouts.app>
