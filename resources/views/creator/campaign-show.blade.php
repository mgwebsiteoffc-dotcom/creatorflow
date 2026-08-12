<x-layouts.app panel="creator" :title="$campaign->title">
    <a href="{{ route('creator.marketplace') }}" class="text-sm text-slate-500 hover:text-slate-800">← Marketplace</a>
    <div class="mt-2 card overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-violet-500 to-rose-500"></div>
        <div class="p-6">
            <p class="text-xs text-slate-500">{{ $campaign->workspace->name }}</p>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $campaign->title }}</h1>
            <div class="mt-3 flex flex-wrap gap-2">
                <x-badge tone="violet">{{ ucfirst($campaign->type) }}</x-badge>
                @if($campaign->niche)<x-badge tone="slate">{{ $campaign->niche }}</x-badge>@endif
                <x-badge tone="amber">{{ $campaign->target_creators }} creators wanted</x-badge>
            </div>

            <h2 class="mt-6 font-bold text-slate-900">Brief</h2>
            <div class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $campaign->brief ?: 'No brief provided yet.' }}</div>

            <h2 class="mt-6 font-bold text-slate-900">Products you could receive</h2>
            <div class="mt-3 space-y-2">
                @foreach($campaign->products as $cp)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-slate-100">📦</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $cp->product->title }}</p>
                            <p class="text-xs text-slate-500">₹{{ number_format(($cp->variant->price_cents ?? $cp->product->priceCents())/100, 2, '.', ',') }} · {{ $cp->target_creators }} creators</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Apply section --}}
    <div class="mt-6">
        @if($existingApplication)
            @php
                $tone = match($existingApplication->status) { 'submitted' => 'amber', 'shortlisted' => 'sky', 'approved' => 'green', 'rejected' => 'rose', default => 'slate' };
                $label = match($existingApplication->status) { 'submitted' => 'Pending review', 'shortlisted' => 'Shortlisted', 'approved' => 'Approved 🎉', 'rejected' => 'Not selected', 'withdrawn' => 'Withdrawn', default => ucfirst($existingApplication->status) };
            @endphp
            <div class="g-border p-1">
                <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-lg font-bold text-slate-900">Your application</h2>
                        <x-badge :tone="$tone">{{ $label }}</x-badge>
                        <span class="text-xs text-slate-500">Applied {{ $existingApplication->created_at->diffForHumans() }}</span>
                    </div>

                    @if($existingApplication->cover_note)
                        <p class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-700">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Your note</span><br>
                            {{ $existingApplication->cover_note }}
                        </p>
                    @endif

                    @if($existingApplication->status === 'approved')
                        <p class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">
                            🎉 The brand approved you! Head to <a href="{{ route('creator.invitations') }}" class="font-semibold underline">Invitations</a> to accept and start work.
                        </p>
                    @elseif(in_array($existingApplication->status, ['submitted','shortlisted']))
                        <form method="POST" action="{{ route('creator.applications.withdraw', $existingApplication) }}"
                              data-confirm="Withdraw your application?" class="mt-4">
                            @csrf
                            <button class="btn-secondary !py-1.5 text-xs">Withdraw application</button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div class="card p-6">
                <h2 class="text-lg font-bold text-slate-900">Apply</h2>
                <p class="mt-1 text-sm text-slate-500">Tell the brand why you're a great fit.</p>
                <form method="POST" action="{{ route('creator.marketplace.apply', $campaign) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="label">Note to the brand</label>
                        <textarea class="input min-h-28" name="cover_note" placeholder="Why are you a great fit? Link a similar post if you have one."></textarea>
                    </div>
                    <div>
                        <label class="label">Your proposed fee (₹ — leave 0 for barter)</label>
                        <input class="input max-w-xs" type="number" step="0.01" name="proposed_fee" min="0" placeholder="0.00">
                    </div>
                    <button class="btn-primary">Send application</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.app>
