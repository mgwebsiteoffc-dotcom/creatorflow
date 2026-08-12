@php $action = $study->exists ? route('admin.case-studies.update', $study) : route('admin.case-studies.store'); @endphp
<x-layouts.admin :title="$study->exists ? 'Edit case study' : 'New case study'">
    <a href="{{ route('admin.case-studies.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Case studies</a>
    <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">{{ $study->exists ? 'Edit case study' : 'New case study' }}</h1>

    <form method="POST" action="{{ $action }}" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        @if($study->exists) @method('PATCH') @endif

        {{-- MAIN --}}
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Story</h2>
                <div class="mt-4 grid gap-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="label">Brand name</label>
                            <input class="input" name="brand_name" required value="{{ old('brand_name', $study->brand_name) }}" placeholder="Glow & Co.">
                        </div>
                        <div>
                            <label class="label">Slug</label>
                            <input class="input font-mono" name="slug" value="{{ old('slug', $study->slug) }}" placeholder="glow-co-launch">
                        </div>
                    </div>
                    <div>
                        <label class="label">Headline</label>
                        <input class="input" name="headline" required value="{{ old('headline', $study->headline) }}" placeholder="How Glow & Co. seeded 240 creators in 60 days">
                    </div>
                    <div>
                        <label class="label">Subheadline</label>
                        <input class="input" name="subheadline" value="{{ old('subheadline', $study->subheadline) }}" placeholder="Zero cash fees, 4.2× ROAS, 1.8M organic views">
                    </div>
                    <div>
                        <label class="label">Summary (markdown)</label>
                        <textarea class="input min-h-24" name="summary">{{ old('summary', $study->summary) }}</textarea>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <div><label class="label">Challenge (markdown)</label><textarea class="input min-h-32" name="challenge">{{ old('challenge', $study->challenge) }}</textarea></div>
                        <div><label class="label">Solution (markdown)</label><textarea class="input min-h-32" name="solution">{{ old('solution', $study->solution) }}</textarea></div>
                        <div><label class="label">Results (markdown)</label><textarea class="input min-h-32" name="results">{{ old('results', $study->results) }}</textarea></div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">📈 Metrics</h2>
                <p class="mt-1 text-xs text-slate-500">Up to 6 hero metrics — rendered as a coloured KPI band.</p>
                <div class="mt-4 space-y-2">
                    @php $metrics = old('metric_label') ? array_map(null, (array) old('metric_label'), (array) old('metric_value'), (array) old('metric_tone')) : ($study->metrics ?? []); @endphp
                    @for($i = 0; $i < 6; $i++)
                        @php $m = $metrics[$i] ?? null; @endphp
                        <div class="grid gap-2 md:grid-cols-[1fr_1fr_160px]">
                            <input class="input" name="metric_label[]"  value="{{ is_array($m) ? ($m['label'] ?? '') : '' }}" placeholder="Label · e.g. ROAS">
                            <input class="input" name="metric_value[]"  value="{{ is_array($m) ? ($m['value'] ?? '') : '' }}" placeholder="Value · e.g. 4.2×">
                            <select class="input" name="metric_tone[]">
                                @foreach(['violet','emerald','amber','rose','sky','slate'] as $t)
                                    <option value="{{ $t }}" @selected(is_array($m) && ($m['tone'] ?? '') === $t)>{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">💬 Pull quote</h2>
                <div class="mt-4 grid gap-3">
                    <textarea class="input min-h-24" name="quote" placeholder="What the founder / marketer said">{{ old('quote', $study->quote) }}</textarea>
                    <div class="grid gap-3 md:grid-cols-2">
                        <input class="input" name="quote_author" value="{{ old('quote_author', $study->quote_author) }}" placeholder="Author name">
                        <input class="input" name="quote_role" value="{{ old('quote_role', $study->quote_role) }}" placeholder="Founder, Glow & Co.">
                    </div>
                </div>
            </section>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Publish</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" name="featured" value="1" @checked($study->featured) class="rounded"> Feature on hub
                    </label>
                    <div>
                        <label class="label">Position</label>
                        <input class="input" type="number" name="position" value="{{ old('position', $study->position ?? 0) }}">
                    </div>
                    <div>
                        <label class="label">Published at</label>
                        <input class="input" type="datetime-local" name="published_at" value="{{ old('published_at', $study->published_at?->format('Y-m-d\TH:i')) }}">
                        <p class="mt-1 text-xs text-slate-500">Leave blank to keep as draft.</p>
                    </div>
                    <button class="btn-primary w-full">Save</button>
                    @if($study->exists && $study->isPublished())
                        <a target="_blank" href="{{ url('/case-studies/'.$study->slug) }}" class="btn-secondary w-full text-center !py-2">View live →</a>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Categorise</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div><label class="label">Industry</label>
                        <select class="input" name="industry">
                            <option value="">—</option>
                            @foreach(['Beauty','Fashion','Food','Fitness','Home','Tech','Travel','Wellness','Kids'] as $i)
                                <option value="{{ $i }}" @selected(($study->industry ?? '') === $i)>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">City</label>
                        <input class="input" name="city" value="{{ old('city', $study->city) }}" placeholder="Delhi">
                    </div>
                    <div><label class="label">Campaign type</label>
                        <select class="input" name="campaign_type">
                            <option value="">—</option>
                            @foreach(['barter','paid','ugc','hybrid','seeding','giveaway'] as $t)
                                <option value="{{ $t }}" @selected(($study->campaign_type ?? '') === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Media</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div><label class="label">Cover image URL / path</label><input class="input" name="cover_image_path" value="{{ old('cover_image_path', $study->cover_image_path) }}" placeholder="https://…"></div>
                    <div><label class="label">Brand logo URL / path</label><input class="input" name="brand_logo_path" value="{{ old('brand_logo_path', $study->brand_logo_path) }}"></div>
                    <div><label class="label">Hero video URL (YouTube / Vimeo)</label><input class="input" type="url" name="hero_video_url" value="{{ old('hero_video_url', $study->hero_video_url) }}"></div>
                </div>
            </section>
        </aside>
    </form>
</x-layouts.admin>
