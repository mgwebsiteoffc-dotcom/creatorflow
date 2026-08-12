<x-layouts.admin title="Case studies">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Content · Case studies</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Case studies</h1>
            <p class="mt-1 text-sm text-slate-500">Long-form brand success stories rendered at /case-studies. Requires the "Admin-managed case studies" flag to be on (currently @if(\App\Models\PlatformSetting::feature('case_study_cms'))<span class="badge-green">enabled</span>@else<span class="badge-rose">disabled</span>@endif).</p>
        </div>
        <a href="{{ route('admin.case-studies.create') }}" class="btn-gradient !py-2 text-sm">+ New case study</a>
    </div>

    <form method="GET" class="mt-6 grid gap-2 sm:grid-cols-[1fr_auto] sm:items-center">
        <input class="input" name="q" value="{{ request('q') }}" placeholder="Search brand or headline…">
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Brand</th><th class="p-3">Headline</th><th class="p-3">Category</th><th class="p-3">Status</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($studies as $s)
                    <tr class="hover:bg-violet-50/30">
                        <td class="p-3 font-semibold text-slate-800">{{ $s->brand_name }}</td>
                        <td class="p-3 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($s->headline, 80) }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ $s->industry ?: '—' }} · {{ $s->city ?: '—' }} · <span class="capitalize">{{ $s->campaign_type ?: '—' }}</span></td>
                        <td class="p-3">
                            @if($s->isPublished())<x-badge tone="green">Published</x-badge>@else<x-badge tone="slate">Draft</x-badge>@endif
                            @if($s->featured)<x-badge tone="amber">Featured</x-badge>@endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.case-studies.edit', $s) }}" class="btn-secondary !py-1 !text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.case-studies.destroy', $s) }}" data-confirm="Delete case study?">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-sm text-slate-500">No case studies yet. <a href="{{ route('admin.case-studies.create') }}" class="text-violet-700 hover:underline">Publish the first one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $studies->links() }}</div>
</x-layouts.admin>
