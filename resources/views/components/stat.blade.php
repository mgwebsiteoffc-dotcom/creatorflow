@props(['label', 'value', 'hint' =>null, 'tone' => 'slate'])
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
<div class="card relative overflow-hidden p-5">
    <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-gradient-to-br {{ $grad }} opacity-15 blur-2xl"></div>
    <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ $value }}</p>
    @if($hint)<p class="mt-2 text-xs text-slate-500">{{ $hint }}</p>@endif
</div>
