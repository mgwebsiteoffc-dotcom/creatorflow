@props(['title', 'icon' => 'file-text'])
<div {{ $attributes->class('card flex flex-col items-center justify-center px-6 py-14 text-center') }}>
    <div class="mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 shadow-inner">
        <x-icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
    <p class="mt-1.5 max-w-md text-sm text-slate-500">{{ $slot }}</p>
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
