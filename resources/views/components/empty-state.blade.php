@props(['title', 'icon' => '✨'])
<div {{ $attributes->class('card flex flex-col items-center justify-center px-6 py-16 text-center') }}>
    <div class="mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-3xl shadow-inner">{{ $icon }}</div>
    <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 max-w-md text-sm text-slate-500">{{ $slot }}</p>
    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
