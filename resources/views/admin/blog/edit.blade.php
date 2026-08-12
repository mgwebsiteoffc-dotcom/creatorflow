<x-layouts.admin :title="$post->exists ? 'Edit post' : 'New post'">
    <a href="{{ route('admin.blog.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Blog</a>
    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ $post->exists ? 'Edit post' : 'New blog post' }}</h1>
    <p class="mt-1 text-sm text-slate-500">Body supports Markdown. SEO fields are optional; smart defaults derive from title/excerpt.</p>

    <form method="POST" action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-6 lg:grid-cols-3">
        @csrf

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="space-y-4">
                    <div>
                        <label class="label">Title</label>
                        <input class="input" name="title" required value="{{ old('title', $post->title) }}">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Slug (URL) <span class="text-xs font-normal text-slate-400">optional</span></label>
                            <input class="input" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="auto from title">
                        </div>
                        <div>
                            <label class="label">Category</label>
                            <input class="input" name="category" value="{{ old('category', $post->category) }}" placeholder="Playbooks">
                        </div>
                    </div>
                    <div>
                        <label class="label">Excerpt</label>
                        <textarea class="input min-h-20" name="excerpt" placeholder="One-liner for the blog index & meta description">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6" data-md-editor>
                <div class="flex items-center justify-between">
                    <label class="label !mb-0">Body <span class="text-xs font-normal text-slate-400">(Markdown supported)</span></label>
                    <div class="flex gap-1 rounded-lg border border-slate-200 bg-white p-0.5 text-xs">
                        <button type="button" data-md-mode="write" class="tab-pill !py-1 !px-2.5 !text-xs is-active">Write</button>
                        <button type="button" data-md-mode="preview" class="tab-pill !py-1 !px-2.5 !text-xs">Preview</button>
                    </div>
                </div>
                <div class="mt-2 flex flex-wrap gap-1 rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 p-1.5 text-xs">
                    <button type="button" data-md="h2" class="rounded px-2 py-1 font-bold hover:bg-white">H</button>
                    <button type="button" data-md="b" class="rounded px-2 py-1 font-bold hover:bg-white">B</button>
                    <button type="button" data-md="i" class="rounded px-2 py-1 italic hover:bg-white">I</button>
                    <button type="button" data-md="ul" class="rounded px-2 py-1 hover:bg-white">• List</button>
                    <button type="button" data-md="ol" class="rounded px-2 py-1 hover:bg-white">1. List</button>
                    <button type="button" data-md="quote" class="rounded px-2 py-1 hover:bg-white"></button>
                    <button type="button" data-md="link" class="rounded px-2 py-1 hover:bg-white"></button>
                    <button type="button" data-md="code" class="rounded px-2 py-1 font-mono hover:bg-white">{`}</button>
                </div>
                <textarea data-md-textarea class="input !rounded-t-none min-h-96 font-mono text-sm" name="body" required>{{ old('body', $post->body) }}</textarea>
                <div data-md-preview class="hidden mt-2 min-h-96 rounded-xl border border-slate-200 bg-white p-6"></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-base font-bold text-slate-900">Publish</h3>
                <label class="mt-4 flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published)) class="h-4 w-4 rounded border-slate-300 text-violet-600">
                    Published
                </label>
                <div class="mt-4">
                    <label class="label">Publish date</label>
                    <input class="input" type="date" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d')) }}">
                </div>
                <div class="mt-4">
                    <label class="label">Read time</label>
                    <input class="input" name="read_minutes" placeholder="7 min" value="{{ old('read_minutes', $post->read_minutes) }}">
                </div>
                <button class="btn-primary mt-6 w-full">Save post</button>
                @if($post->exists)
                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn-secondary mt-2 w-full text-center">View live ↗</a>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-base font-bold text-slate-900">Cover</h3>
                <div class="mt-3">
                    <label class="label">Upload cover image</label>
                    <input type="file" name="cover" accept="image/*" class="block w-full text-xs">
                    @if($post->cover_image_path)
                        <img src="{{ $post->cover_image_path }}" class="mt-2 h-24 w-full rounded-lg object-cover">
                    @endif
                </div>
                <div class="mt-3">
                    <label class="label">Or gradient (Tailwind classes)</label>
                    <input class="input" name="cover_gradient" value="{{ old('cover_gradient', $post->cover_gradient) }}" placeholder="from-violet-500 to-pink-500">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-base font-bold text-slate-900">SEO</h3>
                <div class="mt-3 space-y-3">
                    <div>
                        <label class="label">Meta title</label>
                        <input class="input" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}">
                    </div>
                    <div>
                        <label class="label">Meta description</label>
                        <textarea class="input min-h-20" name="meta_description">{{ old('meta_description', $post->meta_description) }}</textarea>
                    </div>
                    <div>
                        <label class="label">Canonical URL</label>
                        <input class="input" type="url" name="canonical_url" value="{{ old('canonical_url', $post->canonical_url) }}" placeholder="https://…">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-base font-bold text-slate-900">FAQ (JSON-LD)</h3>
                <p class="mt-1 text-xs text-slate-500">Array of <code>{"q":"…","a":"…"}</code>. Renders as FAQPage schema + on-page section.</p>
                <textarea class="input mt-3 min-h-32 font-mono text-xs" name="faq_json" placeholder='[{"q":"…","a":"…"}]'>{{ old('faq_json', $post->faq_json ? json_encode($post->faq_json, JSON_PRETTY_PRINT) : '') }}</textarea>
            </div>
        </aside>
    </form>
</x-layouts.admin>
