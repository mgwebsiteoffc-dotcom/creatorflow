<x-layouts.admin title="Blog">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Blog</h1>
            <p class="mt-1 text-sm text-slate-500">SEO-friendly posts with meta, canonical, and JSON-LD FAQ support.</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="btn-primary !py-2 text-sm">+ New post</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Title</th><th class="p-3">Category</th><th class="p-3">Status</th><th class="p-3">Author</th><th class="p-3">Updated</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($posts as $p)
                    <tr>
                        <td class="p-3"><span class="font-semibold text-slate-900">{{ $p->title }}</span><br><span class="text-xs text-slate-500">/{{ $p->slug }}</span></td>
                        <td class="p-3 text-slate-600">{{ $p->category ?: '—' }}</td>
                        <td class="p-3">@if($p->is_published)<x-badge tone="green">Published</x-badge>@else<x-badge tone="slate">Draft</x-badge>@endif</td>
                        <td class="p-3 text-slate-600">{{ $p->author?->name ?? '—' }}</td>
                        <td class="p-3 text-slate-500">{{ $p->updated_at->diffForHumans() }}</td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('blog.show', $p->slug) }}" target="_blank" class="btn-ghost !py-1 !text-xs">View ↗</a>
                                <a href="{{ route('admin.blog.edit', $p) }}" class="btn-secondary !py-1 !text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.blog.destroy', $p) }}" data-confirm="Delete this post?">@csrf @method('DELETE')
                                    <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-sm text-slate-500">No posts yet. Click <em>+ New post</em>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
</x-layouts.admin>
