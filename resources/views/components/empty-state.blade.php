@props(['title', 'icon' => '✨'])
<div class="card flex flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-3 grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-2xl">{{ $icon }}</div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $slot }}</p>
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
