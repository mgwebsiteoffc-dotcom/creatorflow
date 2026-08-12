@php $action = $exp->exists ? route('admin.ab.update', $exp) : route('admin.ab.store'); @endphp
<x-layouts.admin :title="$exp->exists ? 'Edit experiment' : 'New experiment'">
    <a href="{{ route('admin.ab.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Experiments</a>
    <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">{{ $exp->exists ? 'Edit experiment' : 'New experiment' }}</h1>

    <form method="POST" action="{{ $action }}" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        @if($exp->exists) @method('PATCH') @endif
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Basics</h2>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div><label class="label">Name</label><input class="input" name="name" required value="{{ old('name', $exp->name) }}" placeholder="Hero copy — playful vs corporate"></div>
                    <div><label class="label">Slug</label><input class="input font-mono" name="slug" value="{{ old('slug', $exp->slug) }}" placeholder="hero-playful-vs-corporate"></div>
                    <div><label class="label">Surface</label>
                        <select class="input" name="surface">
                            @foreach(['landing','hero','pricing','cta','signup'] as $s)
                                <option value="{{ $s }}" @selected(($exp->surface ?? '') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="label">Goal event (optional)</label><input class="input font-mono" name="goal_event" value="{{ old('goal_event', $exp->goal_event) }}" placeholder="signup_started"></div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Variants</h2>
                <p class="mt-1 text-xs text-slate-500">Weights must sum to 100. Blade helper returns an array with <code>key</code>, <code>label</code>, <code>copy</code>.</p>
                <div class="mt-4 space-y-2">
                    @php $variants = old('variant_keys') ? array_map(null, (array) old('variant_keys'), (array) old('variant_weights'), (array) old('variant_labels'), (array) old('variant_copy')) : ($exp->variants ?? [['key' => 'A', 'weight' =>50], ['key' => 'B', 'weight' =>50]]); @endphp
                    @foreach($variants as $i => $v)
                        <div class="grid gap-2 md:grid-cols-[80px_100px_1fr_1fr]">
                            <input class="input font-mono" name="variant_keys[]"    value="{{ is_array($v) ? ($v['key'] ?? '') : ($v[0] ?? '') }}" placeholder="A">
                            <input class="input font-mono" name="variant_weights[]" value="{{ is_array($v) ? ($v['weight'] ?? 50) : ($v[1] ?? 50) }}" placeholder="50">
                            <input class="input" name="variant_labels[]" value="{{ is_array($v) ? ($v['label'] ?? '') : '' }}" placeholder="Label">
                            <input class="input" name="variant_copy[]"   value="{{ is_array($v) ? ($v['copy']  ?? '') : '' }}" placeholder="Copy / value">
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Status</h2>
                <select class="input mt-3" name="status">
                    @foreach(['draft','running','paused','concluded'] as $s)
                        <option value="{{ $s }}" @selected(($exp->status ?? 'draft') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="btn-primary mt-4 w-full">Save experiment</button>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-xs text-slate-600">
                <h3 class="text-sm font-black text-slate-800">Use in Blade</h3>
                <pre class="mt-2 overflow-x-auto rounded bg-white p-2 font-mono text-[11px]">@php $v = \App\Support\AbTest::variant('{{ $exp->slug ?: 'your-slug' }}'); @endphp
&#123;&#123; $v['copy'] ?? 'Default copy' &#125;&#125;</pre>
                <h3 class="mt-3 text-sm font-black text-slate-800">Track conversion</h3>
                <pre class="mt-2 overflow-x-auto rounded bg-white p-2 font-mono text-[11px]">\App\Support\AbTest::convert('{{ $exp->slug ?: 'your-slug' }}');</pre>
            </section>
        </aside>
    </form>
</x-layouts.admin>
