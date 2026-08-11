<x-layouts.admin title="Homepage">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Homepage content</h1>
            <p class="mt-1 text-sm text-slate-500">Client logos, sample reels, and hero images — everything visitors see up-top.</p>
        </div>
        <a href="{{ url('/') }}" target="_blank" class="btn-secondary !py-2 text-sm">View homepage ↗</a>
    </div>

    @if(! empty($schemaMissing))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">homepage_items table not migrated yet.</p><p class="mt-1 text-xs text-amber-800">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>. Until then the homepage falls back to bundled defaults.</p></div>
        </div>
    @endif

    {{-- Section tabs (visual) --}}
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="#client_logo" class="tab-pill is-active">🏢 Client logos ({{ $sections['client_logo']->count() }})</a>
        <a href="#sample_reel" class="tab-pill">🎬 Sample reels ({{ $sections['sample_reel']->count() }})</a>
        <a href="#hero_image"  class="tab-pill">🖼 Hero images ({{ $sections['hero_image']->count() }})</a>
    </div>

    {{-- ─────────── CLIENT LOGOS ─────────── --}}
    <section id="client_logo" class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">🏢 Client logos</h2>
                <p class="text-xs text-slate-500">Marquee strip on the homepage and city / service pages.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.homepage.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-2xl bg-slate-50 p-4 md:grid-cols-4">
            @csrf
            <input type="hidden" name="section" value="client_logo">
            <div>
                <label class="label">Brand name</label>
                <input class="input" name="title" required placeholder="Nykaa">
            </div>
            <div>
                <label class="label">Logo (transparent PNG)</label>
                <input class="input" type="file" name="image" accept="image/*" required>
            </div>
            <div>
                <label class="label">Link (optional)</label>
                <input class="input" type="url" name="external_url" placeholder="https://…">
            </div>
            <div class="flex items-end"><button class="btn-primary w-full">Add logo</button></div>
        </form>

        @if($sections['client_logo']->isEmpty())
            <p class="mt-6 rounded-xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No custom client logos yet — homepage uses the bundled defaults.</p>
        @else
            <div class="mt-6 grid gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach($sections['client_logo'] as $l)
                    <div class="group relative rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="grid h-16 place-items-center">
                            @if($l->image_path)
                                <img src="{{ $l->image_path }}" alt="{{ $l->title }}" class="max-h-14 max-w-full object-contain grayscale transition group-hover:grayscale-0">
                            @else
                                <span class="text-xs text-slate-400">no image</span>
                            @endif
                        </div>
                        <div class="mt-2 text-center text-[11px] font-semibold text-slate-700">{{ $l->title }}</div>
                        <form method="POST" action="{{ route('admin.homepage.destroy', $l) }}" class="absolute right-2 top-2" data-confirm="Delete {{ $l->title }}?">
                            @csrf @method('DELETE')
                            <button class="rounded-full bg-white/90 px-1.5 py-0.5 text-[10px] font-bold text-rose-600 shadow-sm hover:bg-rose-50">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ─────────── SAMPLE REELS ─────────── --}}
    <section id="sample_reel" class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">🎬 Sample reels</h2>
                <p class="text-xs text-slate-500">Portrait cards on the "Turning ideas into viral drops" carousel. Filterable by category on the site.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.homepage.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-2xl bg-slate-50 p-4 md:grid-cols-3">
            @csrf
            <input type="hidden" name="section" value="sample_reel">
            <div>
                <label class="label">Title</label>
                <input class="input" name="title" required placeholder="Samsara Ghee">
            </div>
            <div>
                <label class="label">Category</label>
                <select class="input" name="category">
                    @foreach(['Food','Beauty','Fashion','Travel','Cosmetics','Tech','Home','Lifestyle','Fitness','Store Visit'] as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Creator handle</label>
                <input class="input" name="subtitle" placeholder="@nova.eats">
            </div>
            <div>
                <label class="label">Views badge</label>
                <input class="input" name="meta" placeholder="2.4M views">
            </div>
            <div>
                <label class="label">Video file (mp4 / mov / webm · max 100 MB)</label>
                <input class="input" type="file" name="video" accept="video/mp4,video/quicktime,video/webm">
            </div>
            <div>
                <label class="label">Thumbnail / poster (optional)</label>
                <input class="input" type="file" name="poster" accept="image/*">
            </div>
            <div>
                <label class="label">Gradient fallback (Tailwind classes)</label>
                <input class="input" name="gradient" placeholder="from-amber-400 to-orange-500" value="from-violet-500 to-pink-500">
            </div>
            <div>
                <label class="label">External URL (optional)</label>
                <input class="input" type="url" name="external_url" placeholder="https://instagram.com/reel/…">
            </div>
            <div class="flex items-end"><button class="btn-primary w-full">Add reel</button></div>
        </form>

        @if($sections['sample_reel']->isEmpty())
            <p class="mt-6 rounded-xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">No custom sample reels yet — homepage uses the bundled fake reels.</p>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($sections['sample_reel'] as $r)
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="relative aspect-[9/16] bg-gradient-to-br {{ $r->gradient ?: 'from-violet-500 to-pink-500' }}">
                            @if($r->video_path)
                                <video src="{{ $r->video_path }}" @if($r->poster_path) poster="{{ $r->poster_path }}" @endif class="h-full w-full object-cover" muted playsinline controls></video>
                            @elseif($r->poster_path)
                                <img src="{{ $r->poster_path }}" class="h-full w-full object-cover" alt="{{ $r->title }}">
                            @endif
                            <span class="absolute left-2 top-2 rounded-full bg-black/50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">{{ $r->category ?: '—' }}</span>
                        </div>
                        <div class="p-3">
                            <div class="truncate text-sm font-bold text-slate-900">{{ $r->title }}</div>
                            <div class="text-[11px] text-slate-500">{{ $r->subtitle }} · {{ $r->meta }}</div>
                            <form method="POST" action="{{ route('admin.homepage.destroy', $r) }}" class="mt-2" data-confirm="Delete this reel?">
                                @csrf @method('DELETE')
                                <button class="text-[11px] font-semibold text-rose-600 hover:text-rose-800">✕ Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ─────────── HERO IMAGES ─────────── --}}
    <section id="hero_image" class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">🖼 Hero images</h2>
                <p class="text-xs text-slate-500">Optional slideshow images for the hero mock. Falls back to the built-in campaign card when empty.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.homepage.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-2xl bg-slate-50 p-4 md:grid-cols-3">
            @csrf
            <input type="hidden" name="section" value="hero_image">
            <div>
                <label class="label">Caption</label>
                <input class="input" name="title" placeholder="Live campaign screenshot">
            </div>
            <div>
                <label class="label">Image</label>
                <input class="input" type="file" name="image" accept="image/*" required>
            </div>
            <div class="flex items-end"><button class="btn-primary w-full">Add image</button></div>
        </form>

        @if($sections['hero_image']->isNotEmpty())
            <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                @foreach($sections['hero_image'] as $h)
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        @if($h->image_path)
                            <img src="{{ $h->image_path }}" alt="{{ $h->title }}" class="h-40 w-full object-cover">
                        @endif
                        <div class="p-3">
                            <div class="text-sm font-semibold text-slate-800">{{ $h->title }}</div>
                            <form method="POST" action="{{ route('admin.homepage.destroy', $h) }}" class="mt-2" data-confirm="Delete?">
                                @csrf @method('DELETE')
                                <button class="text-[11px] font-semibold text-rose-600 hover:text-rose-800">✕ Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.admin>
