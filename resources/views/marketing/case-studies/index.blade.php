<x-layouts.app panel="guest"
    title="Case studies — how Indian DTC brands 4× ROAS with CreatorPlex"
    metaDescription="Real influencer marketing campaigns run on CreatorPlex — outcomes, playbooks and metrics from India's top DTC brands.">

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
            <p class="section-eyebrow">Case studies</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                <span class="text-gradient">Real brands</span>. Real ROAS.
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">How Indian DTC brands scale creator marketing on CreatorPlex — with the exact playbook, numbers and creators.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14">
        @if($studies->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-200 bg-white/50 p-16 text-center">
                <p class="text-4xl">📚</p>
                <p class="mt-3 text-slate-600">Publishing our first case studies — check back soon.</p>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($studies as $s)
                    <a href="{{ route('case-studies.show', $s->slug) }}" class="reveal card card-hover group flex flex-col overflow-hidden">
                        <div class="relative h-40 bg-gradient-to-br from-violet-500 via-pink-500 to-amber-500">
                            @if($s->cover_image_path)
                                <img src="{{ $s->cover_image_path }}" alt="{{ $s->brand_name }}" class="h-full w-full object-cover">
                            @endif
                            @if($s->featured)<span class="absolute left-4 top-4 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">⭐ Featured</span>@endif
                        </div>
                        <div class="flex-1 p-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">{{ $s->brand_name }}</p>
                            <h3 class="mt-1 text-lg font-black text-slate-900">{{ $s->headline }}</h3>
                            <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $s->subheadline }}</p>
                            @if(! empty($s->metrics))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach(array_slice($s->metrics, 0, 3) as $m)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ $m['value'] }} · {{ $m['label'] }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="px-5 pb-5 text-sm font-semibold text-violet-700 group-hover:text-violet-900">Read the case study →</div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    @include('marketing._cta')
</x-layouts.app>
