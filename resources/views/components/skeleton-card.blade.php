@props(['count' => 6, 'variant' => 'creator'])

{{-- Skeleton placeholder cards. --}}
<div {{ $attributes->merge(['class' => 'grid gap-3 sm:grid-cols-2 lg:grid-cols-3']) }}>
    @for($i = 0; $i < $count; $i++)
        <div class="flex flex-col rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-3">
                <div class="skeleton h-10 w-10 rounded-full"></div>
                <div class="flex-1 space-y-1.5">
                    <div class="skeleton h-3 w-24"></div>
                    <div class="skeleton h-2.5 w-32"></div>
                </div>
                <div class="skeleton h-4 w-12"></div>
            </div>
            <div class="mt-2 flex gap-1">
                <div class="skeleton h-4 w-12 rounded-md"></div>
                <div class="skeleton h-4 w-16 rounded-md"></div>
            </div>
            <div class="mt-3 grid grid-cols-3 gap-2 rounded-lg border border-slate-100 bg-slate-50/60 p-2">
                <div><div class="skeleton mx-auto h-2 w-12"></div><div class="skeleton mx-auto mt-1 h-3.5 w-10"></div></div>
                <div><div class="skeleton mx-auto h-2 w-10"></div><div class="skeleton mx-auto mt-1 h-3.5 w-10"></div></div>
                <div><div class="skeleton mx-auto h-2 w-8"></div><div class="skeleton mx-auto mt-1 h-3.5 w-8"></div></div>
            </div>
            <div class="mt-3 flex gap-1.5">
                <div class="skeleton h-6 flex-1"></div>
                <div class="skeleton h-6 flex-1"></div>
            </div>
        </div>
    @endfor
</div>
