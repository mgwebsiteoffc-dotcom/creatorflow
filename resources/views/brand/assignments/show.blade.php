<x-layouts.app panel="brand" :title="$assignment->creator->display_name">
    <a href="{{ route('brand.assignments.index') }}" class="text-sm text-slate-500">← Assignments</a>
    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="grid h-12 w-12 place-items-center rounded-full bg-rose-100 text-lg font-bold text-rose-700">{{ strtoupper(substr($assignment->creator->display_name,0,2)) }}</div>
            <div>
                <h1 class="text-xl font-bold">{{ $assignment->creator->display_name }}</h1>
                <p class="text-sm text-slate-500">{{ $assignment->campaign->title }}</p>
            </div>
        </div>
        <x-badge :tone="in_array($assignment->status,['approved','completed']) ? 'green' : (in_array($assignment->status,['submitted','changes_requested']) ? 'amber' : 'slate')">
            {{ str_replace('_',' ', $assignment->status) }}
        </x-badge>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Product &amp; order</h2>
                    @if($assignment->order)
                        <a href="{{ route('brand.orders.show', $assignment->order) }}" class="text-xs font-semibold text-violet-700 hover:underline">Manage shipping →</a>
                    @endif
                </div>
                <p class="mt-2 text-sm"><span class="text-slate-500">Product:</span> {{ $assignment->campaignProduct->product->title ?? '—' }}</p>
                <p class="text-sm"><span class="text-slate-500">Code:</span> <code class="rounded bg-slate-100 px-1.5 py-0.5">{{ $assignment->discount_code ?: '—' }}</code></p>
                @if($assignment->order)
                    <p class="text-sm"><span class="text-slate-500">Order:</span>
                        <a href="{{ route('brand.orders.show', $assignment->order) }}" class="font-mono text-violet-700 hover:underline">{{ $assignment->order->order_number }}</a>
                        · <x-badge :tone="in_array($assignment->order->status, ['fulfilled','delivered']) ? 'green' : (in_array($assignment->order->status, ['cancelled','refunded']) ? 'rose' : 'amber')">{{ $assignment->order->status }}</x-badge>
                    </p>
                    <p class="text-sm"><span class="text-slate-500">Tracking:</span>
                        @if($assignment->order->tracking_number)
                            <span class="font-mono">{{ $assignment->order->tracking_number }}</span>
                            @if($assignment->order->tracking_company)<span class="text-slate-500"> · {{ $assignment->order->tracking_company }}</span>@endif
                        @else
                            <span class="text-amber-600">Not shipped yet — <a href="{{ route('brand.orders.show', $assignment->order) }}" class="underline">add tracking</a></span>
                        @endif
                    </p>
                @else
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                        <p class="font-semibold">Order not created yet</p>
                        <p class="mt-1">Once ready, we'll create a Shopify draft order (100 % discount) and you can ship the product.</p>
                        <form method="POST" action="{{ route('brand.orders.createFromAssignment', $assignment) }}" class="mt-2">
                            @csrf
                            <button class="btn-primary !py-1.5 !text-xs">Create order now</button>
                        </form>
                    </div>
                @endif

                {{-- Creator shipping address --}}
                @if($assignment->creator->preferences?->shipping_address)
                    <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-3 text-xs">
                        <p class="font-bold uppercase tracking-widest text-slate-500">Ship to</p>
                        <address class="mt-1 not-italic text-slate-700">
                            @foreach($assignment->creator->preferences->shipping_address as $line)
                                @if($line){{ $line }}<br>@endif
                            @endforeach
                        </address>
                    </div>
                @endif
            </div>

            <div class="card p-5">
                <h2 class="font-semibold">Content submissions</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @forelse($assignment->submissions as $sub)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase text-slate-500">{{ $sub->type }}</span>
                                <x-badge :tone="$sub->status === 'approved' ? 'green' : ($sub->status === 'changes_requested' ? 'amber' : 'slate')">{{ str_replace('_',' ', $sub->status) }}</x-badge>
                            </div>
                            @if(str_starts_with($sub->type, 'image'))
                                <img src="{{ Storage::url($sub->path) }}" class="mt-2 aspect-square w-full rounded-lg object-cover">
                            @elseif(str_starts_with($sub->type, 'video'))
                                <video src="{{ Storage::url($sub->path) }}" controls class="mt-2 w-full rounded-lg"></video>
                            @else
                                <a href="{{ $sub->external_post_url }}" target="_blank" class="mt-2 block truncate text-sm text-violet-600">{{ $sub->external_post_url }}</a>
                            @endif
                            @if($sub->caption)<p class="mt-2 text-sm text-slate-600">{{ $sub->caption }}</p>@endif
                            @if($sub->ai_score)
                                <p class="mt-2 text-xs text-slate-500">AI score: <strong class="text-slate-800">{{ round($sub->ai_score) }}</strong></p>
                            @endif
                            @if(! in_array($sub->status, ['approved','rejected']))
                                <div class="mt-3 flex gap-2">
                                    <form method="POST" action="{{ route('brand.content.approve', $sub) }}">
                                        @csrf
                                        <button class="btn-primary !py-1.5 text-xs">Approve &amp; pay</button>
                                    </form>
                                    <button onclick="document.getElementById('changes-{{ $sub->id }}').classList.toggle('hidden')" class="btn-secondary !py-1.5 text-xs">Request changes</button>
                                </div>
                                <form method="POST" action="{{ route('brand.content.changes', $sub) }}" id="changes-{{ $sub->id }}" class="mt-2 hidden">
                                    @csrf
                                    <textarea name="comment" class="input min-h-16" placeholder="What needs to change?"></textarea>
                                    <button class="btn-secondary mt-2 w-full !py-1.5 text-xs">Send feedback</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No content submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-5">
        <div class="card p-5">
            <div class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white"></div>
                <h2 class="text-lg font-bold text-slate-900">Brief</h2>
            </div>
            <div class="mt-3">
                <x-brief :markdown="$assignment->campaign->brief" compact />
            </div>
        </div>

        @if(($assignment->campaign->references ?? collect())->isNotEmpty())
            <div class="card p-5">
                <div class="flex items-center gap-2">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-cyan-500 to-emerald-500 text-white"></div>
                    <h2 class="text-lg font-bold text-slate-900">References</h2>
                    <span class="ml-auto text-xs font-semibold text-slate-500">{{ $assignment->campaign->references->count() }}</span>
                </div>
                <div class="mt-3 space-y-2">
                    @foreach($assignment->campaign->references as $ref)
                        @include('partials.reference-tile', ['ref' => $ref, 'canDelete' =>false])
                    @endforeach
                </div>
                <a href="{{ route('brand.campaigns.show', $assignment->campaign) }}#references" class="mt-3 inline-block text-xs font-semibold text-violet-700 hover:text-violet-900">Manage references →</a>
            </div>
        @endif

        <div class="card p-5">
            <h2 class="font-semibold">Agreement</h2>
            @if($assignment->contract)
                <p class="mt-2 text-sm"><span class="text-slate-500">Status:</span> {{ $assignment->contract->status }}</p>
                <p class="text-sm"><span class="text-slate-500">Fee:</span> {{ $currentWorkspace->formatMoney((int) $assignment->fee_cents) }}</p>
                <details class="mt-3"><summary class="cursor-pointer text-sm text-violet-600">View agreement</summary>
                    <div class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded bg-slate-50 p-3 text-xs text-slate-700">{{ $assignment->contract->body }}</div>
                </details>
            @else
                <p class="mt-2 text-sm text-slate-500">Agreement pending.</p>
            @endif

            @if($assignment->payout)
                <h2 class="mt-5 font-semibold">Payout</h2>
                <p class="mt-2 text-sm">{{ $currentWorkspace->formatMoney((int) $assignment->payout->net_cents, $assignment->payout->currency) }} · {{ $assignment->payout->status }}</p>
            @endif

            <form method="POST" action="{{ route('messages.start', ['campaign' => $assignment->campaign_id, 'creatorId' => $assignment->creator_id]) }}" class="mt-5">
                @csrf
                <button type="submit" class="btn-secondary w-full">Message creator</button>
            </form>
        </div>
        </div>
    </div>
</x-layouts.app>
