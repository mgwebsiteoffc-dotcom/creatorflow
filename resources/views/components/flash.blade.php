@props(['type' => 'success'])
@php
    $styles = match($type) {
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'error' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-sky-50 text-sky-800 border-sky-200',
    };
@endphp
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
     class="mb-4 flex items-start justify-between gap-3 rounded-2xl border px-4 py-3 text-sm {{ $styles }}">
    <div>{{ $slot }}</div>
    <button @click="show=false" class="text-current opacity-60 hover:opacity-100">&times;</button>
</div>
