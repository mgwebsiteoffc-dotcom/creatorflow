@props(['inv'])
<div class="card p-4">
    <div class="flex items-start gap-3">
        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-500 text-white"></div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold">{{ $inv->campaign->title }}</p>
            <p class="text-xs text-slate-500">{{ $inv->campaign->workspace->name }} · <span class="capitalize">{{ $inv->campaign->type }}</span></p>
            @if($inv->campaignProduct?->product)
                <p class="mt-1 text-sm text-slate-700">Product: {{ $inv->campaignProduct->product->title }}</p>
            @endif
            @if($inv->message)<p class="mt-1 text-sm text-slate-600">{{ $inv->message }}</p>@endif

            <div class="mt-3 flex gap-2">
                <form method="POST" action="{{ route('creator.invitations.accept', $inv) }}">
                    @csrf
                    <button class="btn-primary !py-1.5 text-xs">Accept</button>
                </form>
                <form method="POST" action="{{ route('creator.invitations.decline', $inv) }}">
                    @csrf
                    <button class="btn-secondary !py-1.5 text-xs">Decline</button>
                </form>
                <a href="{{ route('creator.marketplace.show', $inv->campaign) }}" class="btn-ghost !py-1.5 text-xs">Details</a>
            </div>
        </div>
    </div>
</div>
