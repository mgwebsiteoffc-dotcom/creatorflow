<x-layouts.app panel="creator" :title="$assignment->campaign->title">
    <a href="{{ route('creator.assignments.index') }}" class="text-sm text-slate-500">← My work</a>

    <div class="mt-2 card p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold">{{ $assignment->campaign->title }}</h1>
                <p class="text-sm text-slate-500">{{ $assignment->campaign->workspace->name }}</p>
            </div>
            <x-badge :tone="in_array($assignment->status,['approved','completed']) ? 'green' : 'slate'">{{ str_replace('_',' ', $assignment->status) }}</x-badge>
        </div>

        <ol class="mt-5 grid gap-2 sm:grid-cols-4">
            @foreach(['contract_signed' => 'Agreement', 'order_created' => 'Ordered', 'delivered' => 'Delivered', 'approved' => 'Approved'] as $step => $label)
                @php $done = in_array($assignment->status, array_slice(array_keys(['accepted','contract_sent','contract_signed','order_created','shipped','delivered','in_progress','submitted','changes_requested','approved','completed']), array_search($step, ['contract_signed','order_created','delivered','approved']) !== false ? 0 : 0)); @endphp
                <li class="rounded-xl border p-3 text-center text-sm">
                    <div class="mx-auto mb-1 grid h-7 w-7 place-items-center rounded-full {{ str_contains(implode(',', ['contract_signed','order_created','shipped','delivered','in_progress','submitted','changes_requested','approved','completed']), $step) ? 'bg-emerald-500 text-white' : 'bg-slate-100' }}">✓</div>
                    {{ $label }}
                </li>
            @endforeach
        </ol>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5">
                <h2 class="font-semibold">The product</h2>
                @if($assignment->campaignProduct)
                    <p class="mt-2 text-sm">{{ $assignment->campaignProduct->product->title }}</p>
                    <p class="text-xs text-slate-500">Your code: <code class="rounded bg-slate-100 px-1.5 py-0.5">{{ $assignment->discount_code }}</code></p>
                @endif
                @if($assignment->order)
                    <p class="mt-2 text-sm"><span class="text-slate-500">Order:</span> {{ $assignment->order->order_number }} · {{ $assignment->order->status }}</p>
                    <p class="text-sm"><span class="text-slate-500">Tracking:</span> {{ $assignment->order->tracking_number ?: 'Awaiting shipment' }}</p>
                @endif
            </div>

            @if($assignment->contract && ! $assignment->contract->signed_by_creator_at)
                <div class="card p-5">
                    <h2 class="font-semibold">Agreement</h2>
                    <p class="mt-2 text-sm text-slate-600">Please review and sign the agreement to continue.</p>
                    <details class="mt-3"><summary class="cursor-pointer text-sm text-violet-600">Read agreement</summary>
                        <div class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded bg-slate-50 p-3 text-xs">{{ $assignment->contract->body }}</div>
                    </details>
                    <form method="POST" action="{{ route('creator.assignments.contract', $assignment) }}" class="mt-3">
                        @csrf
                        <button class="btn-primary">I agree &amp; sign</button>
                    </form>
                </div>
            @endif

            <div class="card p-5">
                <h2 class="font-semibold">Submit content</h2>
                <p class="mt-1 text-sm text-slate-500">Upload a photo or video directly from your phone. AI pre-checks it against the brief before the brand sees it.</p>
                <form method="POST" action="{{ route('creator.assignments.content', $assignment) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <select name="type" class="input">
                            <option value="video">Video</option>
                            <option value="image">Image</option>
                            <option value="story">Story</option>
                            <option value="reel">Reel</option>
                            <option value="link">Live link</option>
                        </select>
                    </div>
                    <input type="file" name="file" accept="image/*,video/*" capture="environment" class="block w-full text-sm">
                    <textarea name="caption" class="input min-h-20" placeholder="Caption / notes (optional)"></textarea>
                    <input type="url" name="external_post_url" class="input" placeholder="https://instagram.com/p/... (optional)">
                    <button class="btn-primary w-full">Submit for review</button>
                </form>
            </div>

            @if($assignment->submissions->isNotEmpty())
                <div class="card p-5">
                    <h2 class="font-semibold">Your submissions</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @foreach($assignment->submissions as $sub)
                            <div class="rounded-xl border border-slate-200 p-3">
                                <div class="flex justify-between"><span class="text-xs uppercase text-slate-500">{{ $sub->type }}</span>
                                    <x-badge :tone="$sub->status === 'approved' ? 'green' : ($sub->status === 'changes_requested' ? 'amber' : 'slate')">{{ str_replace('_',' ', $sub->status) }}</x-badge>
                                </div>
                                @if($sub->path && ! str_starts_with($sub->path, 'external/'))
                                    @if(str_starts_with($sub->type, 'image'))
                                        <img src="{{ Storage::url($sub->path) }}" class="mt-2 aspect-square w-full rounded-lg object-cover">
                                    @else
                                        <video src="{{ Storage::url($sub->path) }}" controls class="mt-2 w-full rounded-lg"></video>
                                    @endif
                                @else
                                    <a href="{{ $sub->external_post_url }}" target="_blank" class="mt-2 block truncate text-sm text-violet-600">{{ $sub->external_post_url }}</a>
                                @endif
                                @if($sub->ai_score)<p class="mt-2 text-xs text-slate-500">AI score: {{ round($sub->ai_score) }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="card p-5">
                <h2 class="font-semibold">Brief</h2>
                <div class="mt-2 whitespace-pre-wrap text-sm text-slate-700">{{ $assignment->campaign->brief }}</div>
            </div>
            @if($assignment->payout)
                <div class="card p-5">
                    <h2 class="font-semibold">Payout</h2>
                    <p class="mt-2 text-2xl font-bold">${{ number_format($assignment->payout->net_cents/100,2) }}</p>
                    <p class="text-xs text-slate-500">{{ $assignment->payout->status }} · released after approval</p>
                </div>
            @endif
            <a href="{{ route('messages.start', ['campaign' => $assignment->campaign_id, 'creatorId' => $assignment->creator_id]) }}" class="btn-secondary w-full">Message brand</a>
        </div>
    </div>
</x-layouts.app>
