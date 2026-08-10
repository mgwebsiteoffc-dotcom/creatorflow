<x-layouts.app panel="creator" title="Complete your creator profile">
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-bold">Set up your creator profile</h1>
        <p class="mt-1 text-sm text-slate-500">This is how brands discover you. You can edit everything later.</p>

        <form method="POST" action="{{ route('creator.onboarding.store') }}" class="card mt-5 space-y-5 p-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Display name *</label>
                    <input class="input" name="display_name" value="{{ old('display_name', auth()->user()->name) }}" required>
                </div>
                <div>
                    <label class="label">Country</label>
                    <input class="input" name="country" value="{{ old('country') }}" placeholder="US">
                </div>
            </div>

            <div>
                <label class="label">Bio</label>
                <textarea class="input min-h-24" name="bio" placeholder="Tell brands what you create and who your audience is.">{{ old('bio') }}</textarea>
            </div>

            <div>
                <label class="label">Niches *</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Beauty & Skincare','Fashion','Food & Beverage','Fitness','Travel','Tech','Home','Gaming','Parenting','Pets','Finance','Lifestyle'] as $n)
                        <label class="cursor-pointer rounded-full border border-slate-200 px-3 py-1.5 text-sm has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50">
                            <input type="checkbox" name="niches[]" value="{{ $n }}" class="sr-only" @checked(in_array($n, old('niches', [])))> {{ $n }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label">Instagram</label>
                    <input class="input" name="instagram_handle" value="{{ old('instagram_handle') }}" placeholder="@handle">
                </div>
                <div>
                    <label class="label">TikTok</label>
                    <input class="input" name="tiktok_handle" value="{{ old('tiktok_handle') }}" placeholder="@handle">
                </div>
                <div>
                    <label class="label">YouTube</label>
                    <input class="input" name="youtube_handle" value="{{ old('youtube_handle') }}" placeholder="@handle">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label">Total followers</label>
                    <input class="input" type="number" name="follower_count_total" value="{{ old('follower_count_total', 0) }}">
                </div>
                <div>
                    <label class="label">Engagement %</label>
                    <input class="input" type="number" step="0.1" name="engagement_rate" value="{{ old('engagement_rate', 4) }}">
                </div>
                <div>
                    <label class="label">UGC rate $</label>
                    <input class="input" type="number" name="rate_ugc_cents" value="{{ old('rate_ugc_cents') }}" placeholder="e.g. 15000">
                </div>
            </div>

            <div class="flex gap-6 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="accepts_barter" value="1" checked> Open to barter</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="accepts_paid" value="1" checked> Open to paid</label>
            </div>

            <button class="btn-primary w-full sm:w-auto">Publish profile</button>
        </form>
    </div>
</x-layouts.app>
