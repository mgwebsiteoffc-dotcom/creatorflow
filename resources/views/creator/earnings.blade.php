<x-layouts.app panel="creator" title="Earnings">
    <h1 class="text-2xl font-bold">Earnings</h1>
    <p class="text-sm text-slate-500">Fast payouts after content approval via Stripe Connect.</p>

    <div class="mt-5 grid grid-cols-2 gap-3">
        <x-stat label="Available" :value="'$'.number_format($availableCents/100,2)" tone="emerald"/>
        <x-stat label="Pending" :value="'$'.number_format($pendingCents/100,2)" tone="amber"/>
    </div>

    @if($onboardingUrl)
        <div class="card mt-5 p-5">
            <h2 class="font-semibold">Connect your bank account</h2>
            <p class="mt-1 text-sm text-slate-500">Set up Stripe Express to receive payments. It only takes a minute.</p>
            <a href="{{ $onboardingUrl }}" class="btn-primary mt-3">Connect Stripe</a>
        </div>
    @endif

    <h2 class="mt-8 font-semibold">Payout history</h2>
    <div class="mt-3 space-y-2">
        @forelse($payouts as $p)
            <div class="card flex items-center justify-between p-4 text-sm">
                <div>
                    <p class="font-medium">{{ $p->campaign->title ?? 'Campaign' }}</p>
                    <p class="text-xs text-slate-500">{{ $p->paid_at?->format('M j, Y') ?? 'Scheduled' }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold">${{ number_format($p->net_cents/100,2) }}</p>
                    <x-badge :tone="$p->status === 'paid' ? 'green' : 'amber'">{{ $p->status }}</x-badge>
                </div>
            </div>
        @empty
            <x-empty-state title="No payouts yet" icon="💸">
                Approved content turns into payouts automatically.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $payouts->links() }}</div>
</x-layouts.app>
