<x-layouts.app panel="guest" :title="$meta['label'].' — CreatorPlex resources'"
    :metaDescription="$meta['sub'].' Free, India-first, no signup.'">

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('resources') }}" class="hover:text-slate-900">Resources</a> → <span>{{ $meta['label'] }}</span>
            </nav>
            <div class="mt-3 flex items-center gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $meta['grad'] }} text-lg text-white">{{ $meta['icon'] }}</span>
                <div>
                    <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">{{ $meta['label'] }}</h1>
                    <p class="mt-1 max-w-2xl text-sm text-slate-600">{{ $meta['sub'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 pt-10">
        @if($items->isEmpty())
            <p class="rounded-2xl border border-slate-200 bg-white p-6 text-center text-slate-500">More resources coming soon in this category.</p>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $r)
                    <a href="{{ route('resources.show', $r['slug']) }}" class="reveal card card-hover group flex flex-col overflow-hidden">
                        <div class="relative h-32 bg-gradient-to-br {{ $meta['grad'] }}">
                            <div class="absolute inset-0 opacity-25" style="background: radial-gradient(200px 200px at 30% 30%, rgba(255,255,255,.5), transparent 60%);"></div>
                            <span class="absolute left-4 top-4 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">{{ $meta['label'] }}</span>
                            <span class="absolute right-4 top-4 text-2xl">{{ $meta['icon'] }}</span>
                        </div>
                        <div class="flex-1 p-5">
                            <h3 class="font-bold text-slate-900">{{ $r['title'] }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{!! $r['summary'] !!}</p>
                        </div>
                        <div class="px-5 pb-5 text-sm font-semibold text-violet-700 group-hover:text-violet-900">Read · {{ $r['read_min'] }} min →</div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
