<x-layouts.app panel="creator" :title="$campaign->title">
    <a href="{{ route('creator.marketplace') }}" class="text-sm text-slate-500">← Marketplace</a>
    <div class="mt-2 card overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-violet-500 to-rose-500"></div>
        <div class="p-5">
            <p class="text-xs text-slate-500">{{ $campaign->workspace->name }}</p>
            <h1 class="text-xl font-bold">{{ $campaign->title }}</h1>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-badge tone="violet">{{ ucfirst($campaign->type) }}</x-badge>
                <x-badge tone="slate">{{ $campaign->niche }}</x-badge>
                <x-badge tone="amber">{{ $campaign->target_creators }} creators wanted</x-badge>
            </div>

            <h2 class="mt-5 font-semibold">Brief</h2>
            <div class="mt-1 whitespace-pre-wrap text-sm text-slate-700">{{ $campaign->brief }}</div>

            <h2 class="mt-5 font-semibold">Products you could receive</h2>
            <div class="mt-2 space-y-2">
                @foreach($campaign->products as $cp)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-slate-100">📦</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $cp->product->title }}</p>
                            <p class="text-xs text-slate-500">${{ number_format(($cp->variant->price_cents ?? $cp->product->priceCents())/100,2) }} · {{ $cp->target_creators }} creators</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mt-4 p-5">
        <h2 class="font-semibold">Apply</h2>
        <form method="POST" action="{{ route('creator.marketplace.apply', $campaign) }}" class="mt-3 space-y-3">
            @csrf
            <div>
                <label class="label">Note to the brand</label>
                <textarea class="input min-h-24" name="cover_note" placeholder="Why are you a great fit? Link a similar post if you have one."></textarea>
            </div>
            <div>
                <label class="label">Your proposed fee (in cents, leave empty for barter)</label>
                <input class="input max-w-xs" type="number" name="proposed_fee_cents" min="0" placeholder="0">
            </div>
            <button class="btn-primary">Apply to campaign</button>
        </form>
    </div>
</x-layouts.app>
