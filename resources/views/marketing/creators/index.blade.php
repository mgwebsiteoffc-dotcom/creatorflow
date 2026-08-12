<x-layouts.app panel="guest"
    title="Discover Indian creators — verified UGC & Reels creators on CreatorPlex"
    metaDescription="Browse verified Indian creators across Delhi, Mumbai, Bangalore, Hyderabad and more. Beauty, fashion, food, tech, fitness — every niche, every tier.">

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
            <p class="section-eyebrow">Creator directory</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                Discover <span class="text-gradient">Indian creators</span>
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">Real, verified UGC + Reels creators across every city + niche. Share their profile link, invite them to your brand campaign.</p>
        </div>
    </section>

    {{-- Filters --}}
    <form method="GET" class="mx-auto mt-6 flex max-w-6xl flex-wrap gap-2 px-4">
        <input class="input max-w-[200px]" name="city" value="{{ request('city') }}" placeholder="City · e.g. Delhi">
        <input class="input max-w-[180px]" name="niche" value="{{ request('niche') }}" placeholder="Niche · e.g. Beauty">
        <select class="input max-w-[140px]" name="tier">
            <option value="">All tiers</option>
            @foreach(\App\Support\CreatorTaxonomy::tiers() as $slug => $t)
                <option value="{{ $slug }}" @selected(request('tier') === $slug)>{{ $t['label'] }} · {{ $t['range'] }}</option>
            @endforeach
        </select>
        <button class="btn-primary">Filter</button>
    </form>

    <section class="mx-auto max-w-6xl px-4 py-12">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($creators as $c)
                <a href="{{ route('creators.show', $c->slug) }}" class="card card-hover group flex flex-col overflow-hidden">
                    <div class="relative h-24 bg-gradient-to-br from-violet-500 via-pink-500 to-amber-500">
                        <div class="absolute inset-x-4 -bottom-8">
                            <div class="grid h-16 w-16 place-items-center rounded-2xl border-4 border-white bg-white text-lg font-black text-violet-700 shadow-md">{{ strtoupper(substr($c->display_name, 0, 2)) }}</div>
                        </div>
                    </div>
                    <div class="p-5 pt-10">
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-slate-900">{{ $c->display_name }}</h3>
                            @php $tier = $c->currentTier(); @endphp
                            @if($tier)<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700">{{ $tier }}</span>@endif
                        </div>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $c->city ? '📍 '.$c->city.' · ' : '' }}{{ number_format($c->follower_count_total) }} followers · {{ $c->engagement_rate }}% ER</p>
                        <div class="mt-3 flex flex-wrap gap-1">
                            @foreach($c->nicheRows->take(3) as $n)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $n->niche }}</span>
                            @endforeach
                        </div>
                    </div>
                </a>
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">No creators match those filters. Try widening the search.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $creators->links() }}</div>
    </section>
</x-layouts.app>
