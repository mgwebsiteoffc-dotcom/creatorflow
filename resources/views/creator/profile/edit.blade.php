<x-layouts.app panel="creator" title="Edit profile">
    <a href="{{ route('creator.profile.show') }}" class="text-sm text-slate-500">← Profile</a>
    <h1 class="mt-1 text-2xl font-bold">Edit profile</h1>

    <form method="POST" action="{{ route('creator.profile.update') }}" class="card mt-5 max-w-2xl space-y-4 p-5">
        @csrf @method('PUT')
        <div><label class="label">Display name</label><input class="input" name="display_name" value="{{ $creator->display_name }}" required></div>
        <div><label class="label">Bio</label><textarea class="input min-h-24" name="bio">{{ $creator->bio }}</textarea></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="label">Country</label><input class="input" name="country" value="{{ $creator->country }}"></div>
            <div><label class="label">City</label><input class="input" name="city" value="{{ $creator->city }}"></div>
        </div>
        <div>
            <label class="label">Niches</label>
            <div class="flex flex-wrap gap-2">
                @foreach(['Beauty & Skincare','Fashion','Food & Beverage','Fitness','Travel','Tech','Home','Gaming','Parenting','Pets','Finance','Lifestyle'] as $n)
                    <label class="cursor-pointer rounded-full border border-slate-200 px-3 py-1.5 text-sm has-[:checked]:border-rose-400 has-[:checked]:bg-rose-50">
                        <input type="checkbox" name="niches[]" value="{{ $n }}" class="sr-only" @checked($creator->nicheRows->contains('niche', $n))>{{ $n }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div><label class="label">UGC $</label><input class="input" type="number" name="rate_ugc_cents" value="{{ $creator->rate_ugc_cents }}"></div>
            <div><label class="label">Video $</label><input class="input" type="number" name="rate_video_cents" value="{{ $creator->rate_video_cents }}"></div>
            <div><label class="label">Post $</label><input class="input" type="number" name="rate_post_cents" value="{{ $creator->rate_post_cents }}"></div>
            <div><label class="label">Story $</label><input class="input" type="number" name="rate_story_cents" value="{{ $creator->rate_story_cents }}"></div>
        </div>
        <div class="flex gap-6 text-sm">
            <label class="flex items-center gap-2"><input type="checkbox" name="accepts_barter" value="1" @checked($creator->accepts_barter)> Open to barter</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="accepts_paid" value="1" @checked($creator->accepts_paid)> Open to paid</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="open_to_work" value="1" @checked($creator->open_to_work)> Open to work</label>
        </div>
        <button class="btn-primary">Save changes</button>
    </form>
</x-layouts.app>
