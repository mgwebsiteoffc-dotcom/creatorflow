<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorNiche;
use App\Models\CreatorSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $creator = $request->user()->creator->load(['nicheRows', 'socialAccounts', 'portfolioItems', 'preferences']);

        return view('creator.profile.show', compact('creator'));
    }

    public function edit(Request $request)
    {
        $creator = $request->user()->creator->load(['nicheRows', 'socialAccounts']);

        return view('creator.profile.edit', compact('creator'));
    }

    public function update(Request $request)
    {
        $creator = $request->user()->creator;

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:190'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'country' => ['nullable', 'char:2'],
            'city' => ['nullable', 'string', 'max:120'],
            'niches' => ['nullable', 'array'],
            'niches.*' => ['string', 'max:120'],
            'rate_ugc' => ['nullable', 'numeric', 'min:0'],
            'rate_video' => ['nullable', 'numeric', 'min:0'],
            'rate_post' => ['nullable', 'numeric', 'min:0'],
            'rate_story' => ['nullable', 'numeric', 'min:0'],
            'accepts_barter' => ['nullable', 'boolean'],
            'accepts_paid' => ['nullable', 'boolean'],
            'open_to_work' => ['nullable', 'boolean'],
        ]);

        $creator->fill([
            'display_name' => $data['display_name'],
            'bio' => $data['bio'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'niches' => $data['niches'] ?? [],
            'rate_ugc_cents' => isset($data['rate_ugc']) ? (int) round(((float) $data['rate_ugc']) * 100) : null,
            'rate_video_cents' => isset($data['rate_video']) ? (int) round(((float) $data['rate_video']) * 100) : null,
            'rate_post_cents' => isset($data['rate_post']) ? (int) round(((float) $data['rate_post']) * 100) : null,
            'rate_story_cents' => isset($data['rate_story']) ? (int) round(((float) $data['rate_story']) * 100) : null,
            'accepts_barter' => (bool) ($data['accepts_barter'] ?? false),
            'accepts_paid' => (bool) ($data['accepts_paid'] ?? false),
            'open_to_work' => (bool) ($data['open_to_work'] ?? true),
        ])->save();

        CreatorNiche::where('creator_id', $creator->id)->delete();
        foreach (($data['niches'] ?? []) as $niche) {
            CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $niche]);
        }

        return redirect()->route('creator.profile.show')->with('status', 'Profile updated.');
    }

    public function attachSocial(Request $request)
    {
        $data = $request->validate([
            'platform' => ['required', 'in:instagram,tiktok,youtube,x,facebook,linkedin,pinterest,blog'],
            'handle' => ['required', 'string', 'max:190'],
            'follower_count' => ['nullable', 'integer', 'min:0'],
            'engagement_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $creator = $request->user()->creator;

        CreatorSocialAccount::updateOrCreate(
            ['creator_id' => $creator->id, 'platform' => $data['platform']],
            [
                'handle' => ltrim($data['handle'], '@'),
                'url' => $data['handle'],
                'follower_count' => $data['follower_count'] ?? 0,
                'engagement_rate' => $data['engagement_rate'] ?? 0,
            ]
        );

        return back()->with('status', 'Social account linked.');
    }
}
