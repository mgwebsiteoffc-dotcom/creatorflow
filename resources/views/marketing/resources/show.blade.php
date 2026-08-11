@php
    $categories = \App\Http\Controllers\MarketingController::resourceCategories();
    $cat = $categories[$item['category']];
    $html = \App\Support\BriefMarkdown::render($item['body']);
@endphp
<x-layouts.app panel="guest" :title="$item['title']" :metaDescription="strip_tags($item['summary'])">
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-4xl px-4 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('resources') }}" class="hover:text-slate-900">Resources</a> →
                <a href="{{ route('resources.category', $item['category']) }}" class="hover:text-slate-900">{{ $cat['label'] }}</a> →
                <span>{{ $item['title'] }}</span>
            </nav>
            <div class="mt-4 flex items-center gap-3">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $cat['grad'] }} text-lg text-white">{{ $cat['icon'] }}</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-600">{{ $cat['label'] }} · {{ $item['read_min'] }} min read</span>
            </div>
            <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">{{ $item['title'] }}</h1>
            <p class="mt-3 max-w-2xl text-slate-600">{!! $item['summary'] !!}</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-14">
        <article class="brief-body prose prose-slate max-w-none">
            {!! $html !!}
        </article>

        <div class="mt-14 rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-sm font-semibold text-slate-900">Try the tools referenced in this guide</p>
            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('tools.roi') }}"   class="chip">📊 ROI calculator</a>
                <a href="{{ route('tools.rate') }}"  class="chip">💸 Rate calculator</a>
                <a href="{{ route('tools.brief') }}" class="chip">📝 AI brief generator</a>
                <a href="{{ route('register') }}"    class="chip">🚀 Start free</a>
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('resources.category', $item['category']) }}" class="text-sm font-semibold text-violet-700 hover:underline">← All {{ $cat['label'] }}</a>
        </div>
    </section>
</x-layouts.app>
