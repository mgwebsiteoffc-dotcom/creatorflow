<x-layouts.app panel="creator" title="Creator home">
    <h1 class="text-2xl font-bold">Hi, {{ explode(' ', $creator->display_name)[0] }} 👋</h1>
    <p class="text-sm text-slate-500">{{ $creator->open_to_work ? 'You are open to new campaigns' : 'You are currently unavailable' }}</p>

    <div class="mt-5 grid grid-cols-2 gap-3">
        <x-stat label="Earned (paid)" :value="'$'.number_format($earningsCents/100, 0)" tone="emerald"/>
        <x-stat label="Pending" :value="'$'.number_format($pendingCents/100, 0)" tone="amber"/>
    </div>

    @if($invitations->isNotEmpty())
        <h2 class="mt-8 text-lg font-bold">New invitations</h2>
        <div class="mt-3 space-y-3">
            @foreach($invitations as $inv)
                @include('creator._invitation', ['inv' => $inv])
            @endforeach
        </div>
    @endif

    <h2 class="mt-8 text-lg font-bold">Active work</h2>
    <div class="mt-3 space-y-3">
        @forelse($activeAssignments as $a)
            <a href="{{ route('creator.assignments.show', $a) }}" class="card flex items-center gap-3 p-4">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-violet-100 text-lg">
                    {{ match($a->status) { 'order_created' => '📦', 'shipped','delivered' => '🚚', 'in_progress' => '🎬', 'submitted' => '⏳', default => '•' } }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $a->campaign->title }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $a->campaignProduct->product->title ?? '' }} · {{ str_replace('_',' ', $a->status) }}</p>
                </div>
                @if($a->content_due_date)<span class="text-xs text-slate-400">due {{ $a->content_due_date->format('M j') }}</span>@endif
            </a>
        @empty
            <x-empty-state title="No active campaigns" icon="🎬">
                Browse the marketplace to find your next collaboration.
                <x-slot:action><a href="{{ route('creator.marketplace') }}" class="btn-primary">Browse marketplace</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>
</x-layouts.app>
