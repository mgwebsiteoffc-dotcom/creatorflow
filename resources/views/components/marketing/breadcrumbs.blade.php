@props(['crumbs' => []])
@if(count($crumbs) > 1)
    <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
        <ol class="flex flex-wrap items-center gap-1.5">
            @foreach($crumbs as $c)
                @if(! $loop->last)
                    <li><a href="{{ $c['url'] }}" class="hover:text-violet-700">{{ $c['name'] }}</a></li>
                    <li aria-hidden="true">›</li>
                @else
                    <li class="font-semibold text-slate-700" aria-current="page">{{ $c['name'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
