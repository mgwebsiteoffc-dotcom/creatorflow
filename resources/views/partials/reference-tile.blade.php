@props(['ref', 'canDelete' =>false, 'campaign' =>null])
@php
    $url = $ref->displayUrl();
@endphp
<div class="group relative flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 transition hover:border-violet-300 hover:shadow-sm">
    @if($ref->isImage() && $url)
        <a href="{{ $url }}" target="_blank" class="block shrink-0">
            <img src="{{ $url }}" alt="" class="h-14 w-14 rounded-lg object-cover">
        </a>
    @elseif($ref->isVideo() && $url)
        <a href="{{ $url }}" target="_blank" class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-slate-900 text-white">
            </a>
    @else
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-slate-100 text-2xl">
            {{ $ref->icon() }}
        </div>
    @endif

    <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <div class="truncate text-sm font-semibold text-slate-900">{{ $ref->title }}</div>
                <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                    <span class="uppercase tracking-widest">{{ $ref->kind }}</span>
                    @if($ref->kind === 'file' && $ref->humanSize()) <span>· {{ $ref->humanSize() }}</span> @endif
                    @if($ref->uploader) <span>· {{ $ref->uploader->name ?? 'brand' }}</span> @endif
                </div>
                @if($ref->note)
                    <p class="mt-1 text-xs text-slate-600">{{ $ref->note }}</p>
                @endif
            </div>
            <div class="flex shrink-0 items-center gap-1">
                @if($url)
                    <a href="{{ $url }}" target="_blank"
                       class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:border-violet-300 hover:text-violet-700"
                       title="{{ $ref->kind === 'link' ? 'Open link' : 'View / download' }}">
                        {{ $ref->kind === 'link' ? 'Open' : 'View' }} ↗
                    </a>
                @endif
                @if($canDelete && $campaign)
                    <form method="POST" action="{{ route('brand.campaigns.references.destroy', ['campaign' => $campaign, 'reference' => $ref]) }}"
                          data-confirm="Remove this reference?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-transparent px-2 py-1 text-xs font-semibold text-rose-600 hover:border-rose-200 hover:bg-rose-50"></button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
