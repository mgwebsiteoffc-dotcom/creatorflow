@props(['label', 'value', 'hint' => null, 'tone' => 'slate'])
@php
    $tones = [
        'violet' => 'from-violet-500 to-indigo-600',
        'emerald' => 'from-emerald-500 to-teal-600',
        'sky' => 'from-sky-500 to-blue-600',
        'amber' => 'from-amber-500 to-orange-600',
        'rose' => 'from-rose-500 to-pink-600',
        'slate' => 'from-slate-700 to-slate-900',
    ];
    $grad = $tones[$tone] ?? $tones['slate'];
@endphp
<div class="card relative overflow-hidden p-4">
    <div class="absolute right-0 top-0 h-20 w-20 rounded-full bg-gradient-to-br {{ $grad }} opacity-10 blur-xl"></div>
    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</p>
    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $value }}</p>
    @if($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
</div>
