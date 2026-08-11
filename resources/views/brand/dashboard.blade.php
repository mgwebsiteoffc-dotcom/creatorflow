<x-layouts.app panel="brand" title="Dashboard">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Welcome back 👋</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $workspace->name }} · plan <span class="font-semibold capitalize text-slate-700">{{ $workspace->plan }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('brand.products.import') }}" class="btn-secondary !py-2 text-sm">Import CSV</a>
            <a href="{{ route('brand.campaigns.create') }}" class="btn-primary !py-2 text-sm">+ New campaign</a>
        </div>
    </div>

    @if(! $workspace->onboardingComplete())
        <a href="{{ route('brand.onboarding') }}"
           class="mt-6 flex items-center gap-3 rounded-2xl border border-violet-200 bg-gradient-to-r from-violet-50 to-pink-50 p-4 text-sm text-violet-900 transition hover:border-violet-300 hover:shadow-sm">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white shadow-sm">👉</span>
            <span class="flex-1"><strong>Finish onboarding</strong> — connect products and launch your first AI campaign.</span>
            <span class="text-violet-500">→</span>
        </a>
    @endif

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-5">
        <x-stat label="Products" :value="$stats['products']" tone="sky"/>
        <x-stat label="Active campaigns" :value="$stats['active_campaigns']" tone="violet"/>
        <x-stat label="Creators reached" :value="$stats['creators_reached']" tone="amber"/>
        <x-stat label="Attributed revenue" :value="'$'.number_format($stats['attributed_revenue_cents']/100, 0)" tone="emerald" :hint="$stats['pending_content'].' content pending review'"/>
    </div>

    @if($suggestion)
        <div class="mt-8 card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-slate-100 bg-gradient-to-r from-violet-50 to-cyan-50 px-5 py-3">
                <span>✨</span>
                <h2 class="font-semibold">AI campaign suggestion</h2>
                <span class="badge-violet ml-auto">ready to launch</span>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-3">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold">{{ $suggestion['title'] }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $suggestion['summary'] }}</p>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                        <div><dt class="text-slate-500">Type</dt><dd class="font-semibold capitalize">{{ $suggestion['type'] }}</dd></div>
                        <div><dt class="text-slate-500">Creators</dt><dd class="font-semibold">{{ $suggestion['target_creators'] }}</dd></div>
                        <div><dt class="text-slate-500">Invite pool</dt><dd class="font-semibold">{{ $suggestion['invite_pool_size'] }}</dd></div>
                        <div><dt class="text-slate-500">Pred. ROI</dt><dd class="font-semibold text-emerald-600">{{ $suggestion['predicted']['roi_p50'] ?? '—' }}x</dd></div>
                    </dl>
                </div>
                <div class="flex flex-col gap-2">
                    <form method="POST" action="{{ route('brand.campaigns.store') }}">
                        @csrf
                        <input type="hidden" name="title" value="{{ $suggestion['title'] }}">
                        <input type="hidden" name="type" value="{{ $suggestion['type'] }}">
                        <input type="hidden" name="niche" value="{{ $suggestion['niche'] }}">
                        <input type="hidden" name="summary" value="{{ $suggestion['summary'] }}">
                        <input type="hidden" name="brief" value="{{ $suggestion['brief'] }}">
                        <input type="hidden" name="creator_fee_cents" value="0">
                        <input type="hidden" name="commission_rate" value="0">
                        <input type="hidden" name="acceptance_rate_assumed" value="{{ $suggestion['acceptance_rate_assumed'] }}">
                        @foreach($suggestion['seed_products'] as $i => $sp)
                            <input type="hidden" name="products[{{$i}}][product_id]" value="{{ $sp['product_id'] }}">
                            <input type="hidden" name="products[{{$i}}][variant_id]" value="{{ $sp['variant_id'] ?? '' }}">
                            <input type="hidden" name="products[{{$i}}][target_creators]" value="{{ $sp['target_creators'] }}">
                        @endforeach
                        <button class="btn-primary w-full">Create draft from AI</button>
                    </form>
                    <a href="{{ route('brand.campaigns.create') }}" class="btn-secondary w-full">Customize first</a>
                </div>
            </div>
        </div>
    @else
        <x-empty-state title="No products yet" icon="📦" class="mt-8">
            Connect Shopify, upload a CSV, or add products manually so AI can suggest your first campaign.
            <x-slot:action><a href="{{ route('brand.onboarding') }}" class="btn-primary">Add products</a></x-slot:action>
        </x-empty-state>
    @endif

    <h2 class="mt-10 text-lg font-bold">Recent campaigns</h2>
    <div class="mt-4 space-y-3">
        @forelse($recentCampaigns as $campaign)
            @include('brand.campaigns._card', ['campaign' => $campaign])
        @empty
            <p class="text-sm text-slate-500">No campaigns yet — your AI suggestion above is the fastest start.</p>
        @endforelse
    </div>
</x-layouts.app>
