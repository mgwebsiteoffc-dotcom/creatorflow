<x-layouts.app panel="guest" :title="strip_tags($item['title']).' · influencer marketing'">
    @include('marketing._hero', [
        'eyebrow' => 'Influencer marketing · '.strip_tags($item['title']),
        'title'   => strip_tags($item['title']).' campaigns that <span class="text-gradient">convert</span>',
        'sub'     => $item['tagline'],
        'ctaText' => 'Start free',
    ])

    <section class="mx-auto max-w-6xl px-4 pb-16">
        {{-- Icon banner --}}
        <div class="reveal overflow-hidden rounded-3xl bg-gradient-to-br {{ $item['grad'] }} p-8 text-white shadow-lg md:p-12">
            <div class="text-5xl">{{ $item['emoji'] }}</div>
            <h2 class="mt-4 text-3xl font-black leading-tight md:text-4xl">{!! $item['title'] !!}</h2>
            <p class="mt-2 max-w-2xl text-white/90">{{ $item['why'] }}</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-3 md:max-w-lg">
                @foreach($item['stats'] as $s)
                    <div class="rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="text-[10px] uppercase tracking-widest opacity-80">{{ $s[0] }}</div>
                        <div class="text-2xl font-black">{{ $s[1] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Other industries</p>
            <h2 class="section-title reveal mt-3">Campaigns that work for every niche</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach($all as $s => $it)
                @if($s !== $slug)
                    <a href="{{ route('industry.show', $s) }}" class="reveal card card-hover flex items-start gap-3 p-5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $it['grad'] }} text-lg text-white shadow-sm">{{ $it['emoji'] }}</div>
                        <div>
                            <h3 class="font-bold text-slate-900">{!! $it['title'] !!}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $it['tagline'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
