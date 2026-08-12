<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\CreatorNiche;
use App\Models\CreatorPreference;
use App\Models\CreatorSocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function create(Request $request)
    {
        if ($request->user()->creator) {
            return redirect()->route('creator.dashboard');
        }

        return view('creator.onboarding');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:190'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'country' => ['nullable', 'string', 'max:2'],
            'city' => ['nullable', 'string', 'max:120'],
            'niches' => ['required', 'array', 'min:1'],
            'niches.*' => ['string', 'max:120'],
            'instagram_handle' => ['nullable', 'string', 'max:190'],
            'tiktok_handle' => ['nullable', 'string', 'max:190'],
            'youtube_handle' => ['nullable', 'string', 'max:190'],
            'follower_count_total' => ['nullable', 'integer', 'min:0'],
            'engagement_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rate_ugc' => ['nullable', 'numeric', 'min:0'],
            'rate_video' => ['nullable', 'numeric', 'min:0'],
            'accepts_barter' => ['nullable', 'boolean'],
            'accepts_paid' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        $creator = Creator::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'display_name' => $data['display_name'],
            'slug' => $this->uniqueSlug($data['display_name']),
            'bio' => $data['bio'] ?? null,
            'email' => $user->email,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'niches' => $data['niches'],
            'status' => 'active',
            'open_to_work' => true,
            'accepts_barter' => (bool) ($data['accepts_barter'] ?? true),
            'accepts_paid' => (bool) ($data['accepts_paid'] ?? true),
            'rate_ugc_cents' => isset($data['rate_ugc']) ? (int) round(((float) $data['rate_ugc']) * 100) : null,
            'rate_video_cents' => isset($data['rate_video']) ? (int) round(((float) $data['rate_video']) * 100) : null,
            'follower_count_total' => $data['follower_count_total'] ?? 0,
            'engagement_rate' => $data['engagement_rate'] ?? 0,
            'performance_score' => 60,
            'fraud_risk' => 8,
        ]);

        foreach ($data['niches'] as $niche) {
            CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $niche]);
        }

        $this->syncSocial($creator, 'instagram', $data['instagram_handle'] ?? null, $data);
        $this->syncSocial($creator, 'tiktok', $data['tiktok_handle'] ?? null, $data);
        $this->syncSocial($creator, 'youtube', $data['youtube_handle'] ?? null, $data);

        CreatorPreference::create(['creator_id' => $creator->id]);

        return redirect()->route('creator.dashboard')
            ->with('status', 'Welcome to CreatorPlex! Your profile is live.');
    }

    protected function syncSocial(Creator $creator, string $platform, ?string $handle, array $data): void
    {
        if (! $handle) {
            return;
        }

        CreatorSocialAccount::create([
            'creator_id' => $creator->id,
            'platform' => $platform,
            'handle' => ltrim($handle, '@'),
            'follower_count' => (int) ($data['follower_count_total'] ?? 0),
            'engagement_rate' => (float) ($data['engagement_rate'] ?? 0),
        ]);
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Creator::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
