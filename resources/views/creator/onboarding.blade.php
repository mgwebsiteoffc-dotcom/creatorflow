<x-layouts.app panel="creator" title="Complete your creator profile">
    <div class="mx-auto max-w-4xl">
        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-rose-600">
            <span class="chip-dot" style="background:#f43f5e; box-shadow: 0 0 0 4px rgba(244,63,94,.15);"></span> Creator onboarding
        </div>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
            Let's build your <span class="text-gradient">creator profile</span>
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            <span data-progress>Step 1 of 4</span> · Takes about 2 minutes. Brands discover you the moment you publish.
        </p>

        <form method="POST" action="{{ route('creator.onboarding.store') }}" data-wizard class="mt-8">
            @csrf

            {{-- STEPPER --}}
            <div class="mb-8 flex items-center gap-2">
                @foreach([1,2,3,4] as $i)
                    <button type="button" data-jump="{{ $i }}" data-dot="{{ $i }}" class="wizard-dot" aria-label="Step {{ $i }}">{{ $i }}</button>
                    @if($i < 4)<div data-bar="{{ $i }}" class="wizard-bar"><span></span></div>@endif
                @endforeach
            </div>

            <div class="g-border p-1">
                <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 md:p-8">

                    {{-- STEP 1: Basics --}}
                    <div data-step="1" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">The basics</h2>
                            <p class="mt-1 text-sm text-slate-500">This is what brands see first.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">Display name *</label>
                                <input class="input" name="display_name" data-required value="{{ old('display_name', auth()->user()->name) }}" placeholder="Alex Rivera">
                            </div>
                            <div>
                                <label class="label">Country (ISO 2)</label>
                                <input class="input" name="country" maxlength="2" placeholder="US" value="{{ old('country') }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="label">Bio</label>
                            <textarea class="input min-h-28" name="bio" placeholder="Tell brands what you create, who your audience is, and what makes your content pop.">{{ old('bio') }}</textarea>
                            <p class="mt-1 text-xs text-slate-400">Pro tip: mention 2–3 brands you've worked with and your best-performing content angle.</p>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <a href="{{ route('home') }}" class="btn-ghost">← Cancel</a>
                            <button type="button" data-next class="btn-gradient">Next: Niches →</button>
                        </div>
                    </div>

                    {{-- STEP 2: Niches --}}
                    <div data-step="2" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Pick your niches</h2>
                            <p class="mt-1 text-sm text-slate-500">Choose up to 4. This drives which campaigns you'll see.</p>
                        </div>

                        <div data-required="group" data-name="niches" class="flex flex-wrap gap-2 rounded-2xl p-2">
                            @foreach(['Beauty & Skincare','Fashion','Food & Beverage','Fitness','Travel','Tech','Home','Gaming','Parenting','Pets','Finance','Lifestyle','Comedy','Music','Education'] as $n)
                                <label>
                                    <input type="checkbox" name="niches[]" value="{{ $n }}" class="sr-only" @checked(in_array($n, old('niches', [])))>
                                    <span class="pick-chip">{{ $n }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <label class="label">Content vibe (optional)</label>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach(['Aesthetic','Playful','Authentic','Educational','Bold','Chill'] as $vibe)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="vibes[]" value="{{ $vibe }}" class="sr-only">
                                        <div class="pick-tile">{{ $vibe }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <button type="button" data-prev class="btn-ghost">← Back</button>
                            <button type="button" data-next class="btn-gradient">Next: Socials →</button>
                        </div>
                    </div>

                    {{-- STEP 3: Socials & stats --}}
                    <div data-step="3" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Connect your socials</h2>
                            <p class="mt-1 text-sm text-slate-500">We only need handles — connecting accounts unlocks better matching later.</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-pink-500 to-orange-500 text-white">◎</span>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900">Instagram</div>
                                    <input class="input mt-1" name="instagram_handle" placeholder="@handle" value="{{ old('instagram_handle') }}">
                                </div>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                                <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-white">♪</span>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900">TikTok</div>
                                    <input class="input mt-1" name="tiktok_handle" placeholder="@handle" value="{{ old('tiktok_handle') }}">
                                </div>
                            </div>
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white">▶</span>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-900">YouTube</div>
                                    <input class="input mt-1" name="youtube_handle" placeholder="@handle" value="{{ old('youtube_handle') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">Total followers</label>
                                <input class="input" type="number" name="follower_count_total" min="0" value="{{ old('follower_count_total', 0) }}" placeholder="e.g. 62000">
                            </div>
                            <div>
                                <label class="label">Avg engagement %</label>
                                <input class="input" type="number" step="0.1" min="0" max="100" name="engagement_rate" value="{{ old('engagement_rate', 4) }}">
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <button type="button" data-prev class="btn-ghost">← Back</button>
                            <button type="button" data-next class="btn-gradient">Next: Rates →</button>
                        </div>
                    </div>

                    {{-- STEP 4: Rates & availability --}}
                    <div data-step="4" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Rates &amp; availability</h2>
                            <p class="mt-1 text-sm text-slate-500">Set what you're open to. You can leave rates blank and negotiate per campaign.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label">UGC rate (in cents)</label>
                                <input class="input" type="number" min="0" name="rate_ugc_cents" placeholder="e.g. 15000 = $150" value="{{ old('rate_ugc_cents') }}">
                            </div>
                            <div>
                                <label class="label">Video rate (in cents)</label>
                                <input class="input" type="number" min="0" name="rate_video_cents" placeholder="e.g. 30000 = $300" value="{{ old('rate_video_cents') }}">
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                                <input type="checkbox" name="accepts_barter" value="1" class="h-5 w-5 rounded border-slate-300 text-violet-600 focus:ring-violet-400" checked>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">🎁 Open to barter</div>
                                    <div class="text-xs text-slate-500">Trade content for free products</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                                <input type="checkbox" name="accepts_paid" value="1" class="h-5 w-5 rounded border-slate-300 text-violet-600 focus:ring-violet-400" checked>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">💰 Open to paid</div>
                                    <div class="text-xs text-slate-500">Get paid per deliverable</div>
                                </div>
                            </label>
                        </div>

                        <div class="mt-6 rounded-2xl bg-gradient-to-r from-rose-500 to-pink-500 p-5 text-white">
                            <div class="text-sm font-bold">You're one click away</div>
                            <p class="mt-1 text-xs text-white/85">Publish your profile to start receiving campaign invites within 24h.</p>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <button type="button" data-prev class="btn-ghost">← Back</button>
                            <button type="submit" class="btn-gradient">🚀 Publish profile</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.app>
