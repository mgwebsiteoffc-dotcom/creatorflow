@props(['tone' => 'slate'])
@php
    $map = [
        'slate' => 'badge-slate',
        'green' => 'badge-green',
        'amber' => 'badge-amber',
        'violet' => 'badge-violet',
        'rose' => 'badge-rose',
        'sky' => 'badge-sky',
    ];
@endphp
<span {{ $attributes->class($map[$tone] ?? 'badge-slate') }}>{{ $slot }}</span>
