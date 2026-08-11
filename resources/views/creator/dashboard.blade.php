<x-layouts.app panel="creator" title="Creator home">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Hi, {{ explode(' ', $creator->display_name)[0] }} 👋</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $creator->open_to_work ? 'You are open to new campaigns' : 'You are currently unavailable' }}</p>
        </div>
        <a href="{{ route('creator.marketplace') }}" class="btn-primary !py-2 text-sm">Browse marketplace →</a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-5">
        <x-stat label="Earned (paid)" :value="'$'.number_format($earningsCents/100, 0)" tone="emerald"/>
        <x-stat label="Pending" :value="'$'.number_format($pendingCents/100, 0)" tone="amber"/>
        <x-stat label="Applications" :value="$applicationCounts['total']" :hint="$applicationCounts['pending'].' pending review'" tone="violet"/>
        <x-stat label="Invitations" :value="$invitations->count()" tone="rose"/>
    </div>

    @if($invitations->isNotEmpty())
        <h2 class="mt-10 text-lg font-bold">New invitations</h2>
        <div class="mt-4 space-y-3">
            @foreach($invitations as $inv)
                @include('creator._invitation', ['inv' => $inv])
            @endforeach
        </div>
    @endif

    @if($recentApplications->isNotEmpty())
        <div class="mt-10 flex items-end justify-between gap-3">
            <h2 class="text-lg font-bold">Recent applications</h2>
            <a href="{{ route('creator.applications') }}" class="text-sm font-semibold text-violet-700 hover:text-violet-900">See all →</a>
        </div>
        <div class="mt-4 space-y-3">
            @foreach($recentApplications as $app)
                @php
                    $tone = match($app->status) { 'submitted' => 'amber', 'shortlisted' => 'sky', 'approved' => 'green', 'rejected' => 'rose', default => 'slate' };
                    $label = match($app->status) { 'submitted' => 'Pending', 'shortlisted' => 'Shortlisted', 'approved' => 'Approved 🎉', 'rejected' => 'Not selected', default => ucfirst($app->status) };
                @endphp
                <a href="{{ route('creator.marketplace.show', $app->campaign) }}" class="card flex items-center gap-3 p-4 hover:border-violet-300">
                    <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white">🎁</div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ $app->campaign->title }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $app->campaign->workspace->name ?? '' }} · applied {{ $app->created_at->diffForHumans() }}</p>
                    </div>
                    <x-badge :tone="$tone">{{ $label }}</x-badge>
                </a>
            @endforeach
        </div>
    @endif

    <h2 class="mt-10 text-lg font-bold">Active work</h2>
    <div class="mt-4 space-y-3">
        @forelse($activeAssignments as $a)
            <a href="{{ route('creator.assignments.show', $a) }}" class="card flex items-center gap-3 p-4 hover:border-violet-300">
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
