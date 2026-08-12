<x-layouts.app panel="creator" title="My profile">
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-bold">My profile</h1>
        <a href="{{ route('creator.profile.edit') }}" class="btn-secondary text-sm">Edit</a>
    </div>

    {{--  Payouts + Instagram cards side-by-side  --}}
    <div class="mt-5 grid gap-4 md:grid-cols-2">
        {{-- Payout details --}}
        <section class="rounded-2xl border {{ $creator->razorpayx_fund_account_id ? 'border-emerald-200 bg-emerald-50/30' : 'border-amber-200 bg-amber-50/40' }} p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest {{ $creator->razorpayx_fund_account_id ? 'text-emerald-700' : 'text-amber-700' }}">Payouts</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        @if($creator->payout_method === 'upi' && $creator->upi_vpa)
                            UPI · {{ $creator->upi_vpa }}
                        @elseif($creator->payout_method === 'bank' && $creator->bank_account_number)
                            Bank · ••••{{ substr($creator->bank_account_number, -4) }} · {{ $creator->bank_ifsc }}
                        @else
                            <span class="text-amber-800">No payout method set yet</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('creator.payout.edit') }}" class="btn-secondary !py-1.5 !text-xs">{{ $creator->upi_vpa || $creator->bank_account_number ? 'Update' : 'Add' }}</a>
            </div>
        </section>

        {{-- Instagram connect --}}
        @php
            $ig = $creator->socialAccounts->firstWhere('platform', 'instagram');
            $igEnabled = \App\Models\PlatformSetting::current()->instagram_enabled;
        @endphp
        <section class="rounded-2xl border {{ $ig?->graph_ig_user_id ? 'border-pink-200 bg-pink-50/30' : 'border-slate-200 bg-white' }} p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-pink-700">Instagram</p>
                    @if($ig?->graph_ig_user_id)
                        <p class="mt-1 text-sm font-semibold text-slate-900"> @{{ $ig->handle }} · {{ number_format($ig->follower_count) }} followers · {{ $ig->engagement_rate }}% ER</p>
                        <p class="text-[11px] text-slate-500">Auto-syncs daily @ 05:00</p>
                    @elseif($igEnabled)
                        <p class="mt-1 text-sm text-slate-600">Connect for auto-verified follower count + engagement rate.</p>
                    @else
                        <p class="mt-1 text-sm text-slate-500">Not enabled by admin yet.</p>
                    @endif
                </div>
                @if($igEnabled)
                    <a href="{{ route('creator.instagram.connect') }}" class="btn-gradient !py-1.5 !text-xs">{{ $ig?->graph_ig_user_id ? 'Reconnect' : 'Connect' }}</a>
                @endif
            </div>
        </section>
    </div>

    {{--  Share your public profile URL (only when the flag is on)  --}}
    @if(\App\Models\PlatformSetting::feature('public_creator_pages'))
        @php $publicUrl = route('creators.show', $creator->slug); @endphp
        <section class="mt-5 rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 via-white to-pink-50 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Your public profile</p>
                    <p class="mt-1 text-sm text-slate-600">Share this link with brands, on your Insta bio, or in your email signature.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ $publicUrl }}" target="_blank" rel="noopener" class="btn-secondary !py-2 text-xs">Preview</a>
                    <button type="button" data-copy="{{ $publicUrl }}" class="btn-gradient !py-2 text-xs">Copy link</button>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 rounded-xl border border-white bg-white/70 p-2 text-sm">
                                <input readonly class="flex-1 border-0 bg-transparent font-mono text-xs focus:outline-none" value="{{ $publicUrl }}" onclick="this.select()">
                <a href="https://wa.me/?text={{ urlencode('Check out my profile on CreatorPlex: '.$publicUrl) }}" target="_blank" rel="noopener" class="rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600">WhatsApp</a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Work with me on CreatorPlex ') }}&url={{ urlencode($publicUrl) }}" target="_blank" rel="noopener" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">𝕏</a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($publicUrl) }}" target="_blank" rel="noopener" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">in</a>
            </div>
            <p class="mt-2 text-[11px] text-slate-500">Instagram bio tip: shorten with bit.ly first — it's still one click for brands.</p>
        </section>
        <script>
        document.querySelectorAll('[data-copy]').forEach(btn =>btn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(btn.dataset.copy);
                const old = btn.textContent;
                btn.textContent = ' Copied!';
                setTimeout(() =>btn.textContent = old, 1500);
            } catch {}
        }));
        </script>
    @endif

    <div class="mt-4 grid gap-5 lg:grid-cols-3">
        <div class="card p-5 text-center lg:col-span-1">
            <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-rose-100 text-3xl font-bold text-rose-700">{{ strtoupper(substr($creator->display_name,0,2)) }}</div>
            <h2 class="mt-3 text-lg font-bold">{{ $creator->display_name }}</h2>
            <p class="text-sm text-slate-500">{{ $creator->city ? $creator->city.', ' : '' }}{{ $creator->country }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-1">
                @foreach($creator->nicheRows as $n)<x-badge tone="violet">{{ $n->niche }}</x-badge>@endforeach
            </div>
            <p class="mt-4 text-sm text-slate-600">{{ $creator->bio }}</p>

            <dl class="mt-5 grid grid-cols-3 gap-2 text-center text-sm">
                <div><p class="text-slate-500">Followers</p><p class="font-semibold">{{ number_format($creator->follower_count_total) }}</p></div>
                <div><p class="text-slate-500">Engage</p><p class="font-semibold">{{ $creator->engagement_rate }}%</p></div>
                <div><p class="text-slate-500">Score</p><p class="font-semibold">{{ $creator->performance_score }}</p></div>
            </dl>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5">
                <h3 class="font-semibold">Rates &amp; preferences</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                    <div><p class="text-slate-500">UGC</p><p class="font-semibold">₹{{ number_format(($creator->rate_ugc_cents ?? 0)/100, 0, '.', ',') }}</p></div>
                    <div><p class="text-slate-500">Video</p><p class="font-semibold">₹{{ number_format(($creator->rate_video_cents ?? 0)/100, 0, '.', ',') }}</p></div>
                    <div><p class="text-slate-500">Post</p><p class="font-semibold">₹{{ number_format(($creator->rate_post_cents ?? 0)/100, 0, '.', ',') }}</p></div>
                    <div><p class="text-slate-500">Story</p><p class="font-semibold">₹{{ number_format(($creator->rate_story_cents ?? 0)/100, 0, '.', ',') }}</p></div>
                </div>
                <div class="mt-3 flex gap-4 text-sm">
                    <span class="{{ $creator->accepts_barter ? 'text-emerald-600' : 'text-slate-400' }}"> Barter</span>
                    <span class="{{ $creator->accepts_paid ? 'text-emerald-600' : 'text-slate-400' }}"> Paid</span>
                    <span class="{{ $creator->open_to_work ? 'text-emerald-600' : 'text-slate-400' }}"> Open to work</span>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="font-semibold">Connected accounts</h3>
                <div class="mt-3 space-y-2">
                    @foreach($creator->socialAccounts as $s)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm">
                            <span class="capitalize">{{ $s->platform }} · {{ $s->handle }}</span>
                            <span class="text-slate-500">{{ number_format($s->follower_count) }} · {{ $s->engagement_rate }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
