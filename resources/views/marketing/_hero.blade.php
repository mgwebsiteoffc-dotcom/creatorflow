@props(['eyebrow' => 'CreatorPlex', 'title' => '', 'sub' => '', 'ctaText' => 'Start free', 'ctaHref' => null])
@php $ctaHref = $ctaHref ?? route('register'); @endphp

<section class="relative overflow-hidden">
    <div class="aurora"></div>
    <div class="dotted absolute inset-0 -z-10"></div>

    <div class="mx-auto max-w-4xl px-4 pb-14 pt-16 text-center md:pb-20 md:pt-24">
        <span class="chip reveal mx-auto"><span class="chip-dot"></span> {{ $eyebrow }}</span>
        <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
            {!! $title !!}
        </h1>
        @if($sub)
            <p class="reveal mx-auto mt-5 max-w-2xl text-lg text-slate-600">{!! $sub !!}</p>
        @endif
        <div class="reveal mt-8 flex justify-center gap-3">
            <a href="{{ $ctaHref }}" class="btn-gradient">
                {{ $ctaText }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="{{ route('contact') }}" class="btn-glass">Talk to sales</a>
        </div>
    </div>
</section>
