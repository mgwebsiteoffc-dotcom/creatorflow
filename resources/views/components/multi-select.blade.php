@props([
    'name',                    // form field name (e.g. "audience[cities][]")
    'options' => [],           // ['slug' => 'Label', ...]
    'selected' => [],          // array of pre-selected slugs
    'placeholder' => 'Pick one or more…',
    'searchPlaceholder' => 'Search…',
    'panelWidth' => 'auto',    // 'auto' or 'sm'|'md'|'lg' etc
])

@php
    $selected = collect($selected)->map(fn ($s) => (string) $s)->all();
    $options  = collect($options);
    $selectedCount = count($selected);
@endphp

<div class="relative" data-ms-wrap>
    {{-- Trigger button (looks like the .input) --}}
    <button type="button" data-ms-trigger
            class="input flex min-h-[42px] w-full flex-wrap items-center gap-1.5 text-left">
        <span data-ms-pills class="flex flex-1 flex-wrap items-center gap-1.5">
            <span data-ms-empty class="text-sm text-slate-400 {{ $selectedCount ? 'hidden' : '' }}">{{ $placeholder }}</span>
            @foreach($selected as $slug)
                @if($options->has($slug))
                    <span data-ms-pill="{{ $slug }}" class="inline-flex items-center gap-1 rounded-md bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-800">
                        <span>{{ $options[$slug] }}</span>
                        <button type="button" data-ms-remove="{{ $slug }}" class="text-violet-500 hover:text-violet-900" aria-label="Remove">×</button>
                    </span>
                @endif
            @endforeach
        </span>
        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
    </button>

    {{-- Dropdown panel --}}
    <div data-ms-panel class="absolute left-0 right-0 top-full z-40 mt-2 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-100 p-2">
            <input data-ms-search type="search" class="input !py-2 text-sm" placeholder="{{ $searchPlaceholder }}" autocomplete="off">
        </div>
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-3 py-1.5 text-[11px] font-semibold text-slate-500">
            <span data-ms-count>{{ $selectedCount }} selected</span>
            <div class="flex gap-3">
                <button type="button" data-ms-all class="font-semibold text-violet-600 hover:text-violet-800">Select all</button>
                <button type="button" data-ms-clear class="text-slate-500 hover:text-slate-800">Clear</button>
            </div>
        </div>
        <div class="max-h-64 overflow-y-auto p-1" data-ms-list>
            @foreach($options as $slug => $label)
                <label data-ms-item="{{ strtolower($label) }}"
                       class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-violet-50">
                    <input type="checkbox" name="{{ $name }}" value="{{ $slug }}"
                           data-ms-value="{{ $slug }}"
                           data-ms-label="{{ $label }}"
                           class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400"
                           @checked(in_array((string) $slug, $selected, true))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
