<?php

namespace App\Support;

use App\Models\CreatorSocialAccount;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Http;

/**
 * Instagram Graph API — pulls follower count, engagement rate, media, and
 * insights for a creator's connected IG Business account.
 *
 * OAuth flow: creator connects via Meta Business login on their profile page,
 * we get long-lived page token + IG business user id, then this service does
 * the rest.
 *
 * Docs: https://developers.facebook.com/docs/instagram-api
 */
class InstagramGraphService
{
    protected string $baseUrl = 'https://graph.facebook.com/v19.0';

    public function isEnabled(): bool
    {
        $s = PlatformSetting::current();
        return (bool) $s->instagram_enabled && ! empty($s->instagram_app_id);
    }

    /**
     * Exchange short-lived user token → long-lived user token.
     */
    public function exchangeToken(string $shortLivedToken): array
    {
        $s = PlatformSetting::current();
        try {
            $r = Http::get("{$this->baseUrl}/oauth/access_token", [
                'grant_type'        => 'fb_exchange_token',
                'client_id'         => $s->instagram_app_id,
                'client_secret'     => $s->instagram_app_secret,
                'fb_exchange_token' => $shortLivedToken,
            ])->throw()->json();
            return ['ok' => true, 'token' => $r['access_token'] ?? null, 'expires_in' => $r['expires_in'] ?? null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Given a page token + page id, look up the linked IG Business user id.
     */
    public function fetchIgUserId(string $pageId, string $pageToken): ?string
    {
        try {
            $r = Http::get("{$this->baseUrl}/{$pageId}", [
                'fields'       => 'instagram_business_account',
                'access_token' => $pageToken,
            ])->throw()->json();
            return $r['instagram_business_account']['id'] ?? null;
        } catch (\Throwable) { return null; }
    }

    /**
     * Refresh follower count, media count, avg engagement rate for a single
     * creator social account. Updates the row + returns the fresh numbers.
     */
    public function syncAccount(CreatorSocialAccount $account): array
    {
        if (! $this->isEnabled() || empty($account->graph_access_token) || empty($account->graph_ig_user_id)) {
            return ['ok' => false, 'error' => 'Not connected'];
        }

        try {
            // 1. Basic profile — followers, media count, name, username
            $profile = Http::get("{$this->baseUrl}/{$account->graph_ig_user_id}", [
                'fields'       => 'username,name,followers_count,media_count,biography,profile_picture_url',
                'access_token' => $account->graph_access_token,
            ])->throw()->json();

            // 2. Last 25 posts — compute avg engagement rate from likes + comments
            $media = Http::get("{$this->baseUrl}/{$account->graph_ig_user_id}/media", [
                'fields'       => 'id,like_count,comments_count,media_type,timestamp',
                'limit'        => 25,
                'access_token' => $account->graph_access_token,
            ])->throw()->json();

            $posts = $media['data'] ?? [];
            $followers = (int) ($profile['followers_count'] ?? 0);
            $engagementRate = 0.0;
            if (count($posts) > 0 && $followers > 0) {
                $totalEngagements = collect($posts)->sum(fn ($p) => (int) ($p['like_count'] ?? 0) + (int) ($p['comments_count'] ?? 0));
                $engagementRate = round(($totalEngagements / count($posts) / $followers) * 100, 2);
            }

            $account->update([
                'handle'          => $profile['username'] ?? $account->handle,
                'url'             => 'https://instagram.com/'.($profile['username'] ?? $account->handle),
                'follower_count'  => $followers,
                'engagement_rate' => min(99.99, max(0, $engagementRate)),
                'verified'        => true, // came from graph, so it's real
                'metadata'        => array_merge((array) $account->metadata, [
                    'media_count' => $profile['media_count'] ?? null,
                    'bio'         => $profile['biography'] ?? null,
                    'avatar'      => $profile['profile_picture_url'] ?? null,
                    'last_25_posts' => count($posts),
                ]),
                'last_synced_at'  => now(),
                'graph_synced_at' => now(),
            ]);

            // Also bump the parent creator's follower_count_total + engagement_rate
            if ($account->creator) {
                $account->creator->update([
                    'follower_count_total' => $followers,
                    'engagement_rate'      => $engagementRate,
                ]);
            }

            return ['ok' => true, 'followers' => $followers, 'er' => $engagementRate, 'posts' => count($posts)];
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
