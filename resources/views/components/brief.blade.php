@props(['markdown' => '', 'compact' =>false])
@php
    $html = \App\Support\BriefMarkdown::render($markdown);
@endphp

@if(trim((string) $markdown) === '')
    <p class="text-sm italic text-slate-400">No brief provided yet.</p>
@else
    <div {{ $attributes->class(['brief-body', 'brief-body--compact' => $compact]) }}>
        {!! $html !!}
    </div>
@endif
