<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorSocialAccount;
use App\Models\PlatformSetting;
use App\Support\InstagramGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * OAuth glue for creators to hook their IG Business account.
 * Two entry points:
 *   • GET /creator/instagram/connect  → redirects to Meta authorise URL
 *   • GET /creator/instagram/callback → exchanges code, saves tokens, syncs
 */
class InstagramConnectController extends Controller
{
    public function connect(Request $request)
    {
        $s = PlatformSetting::current();
        abort_unless($s->instagram_enabled && $s->instagram_app_id, 404, 'Instagram integration is disabled by admin.');

        $state = Str::random(24);
        session(['ig_oauth_state' => $state]);

        $url = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
            'client_id'     => $s->instagram_app_id,
            'redirect_uri'  => route('creator.instagram.callback'),
            'state'         => $state,
            'response_type' => 'code',
            'scope'         => 'instagram_basic,pages_show_list,pages_read_engagement,business_management,instagram_manage_insights',
        ]);
        return redirect()->away($url);
    }

    public function callback(Request $request, InstagramGraphService $ig)
    {
        abort_unless($request->get('state') === session('ig_oauth_state'), 403, 'OAuth state mismatch');
        $creator = $request->user()?->creator;
        abort_unless($creator, 403);

        $s = PlatformSetting::current();
        $code = $request->get('code');

        try {
            // 1. Short-lived user token
            $shortR = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id'     => $s->instagram_app_id,
                'client_secret' => $s->instagram_app_secret,
                'redirect_uri'  => route('creator.instagram.callback'),
                'code'          => $code,
            ])->throw()->json();

            // 2. Exchange to long-lived
            $long = $ig->exchangeToken($shortR['access_token'] ?? '');
            if (! ($long['ok'] ?? false)) throw new \RuntimeException($long['error'] ?? 'Token exchange failed');
            $userToken = $long['token'];

            // 3. Find the first Page + its IG Business account
            $pages = Http::get('https://graph.facebook.com/v19.0/me/accounts', [
                'access_token' => $userToken,
                'fields'       => 'id,name,access_token,instagram_business_account',
            ])->throw()->json();

            $pageWithIg = collect($pages['data'] ?? [])->first(fn ($p) => ! empty($p['instagram_business_account']['id']));
            if (! $pageWithIg) {
                return redirect()->route('creator.profile.show')->with('error', 'No Instagram Business account linked to your Facebook Page. Convert your IG to a Business/Creator account first.');
            }

            $account = CreatorSocialAccount::updateOrCreate(
                ['creator_id' => $creator->id, 'platform' => 'instagram'],
                [
                    'handle'             => $pageWithIg['name'] ?? 'ig',
                    'graph_access_token' => $pageWithIg['access_token'] ?? $userToken,
                    'graph_business_id'  => $pageWithIg['instagram_business_account']['id'],
                    'graph_page_id'      => $pageWithIg['id'],
                    'graph_ig_user_id'   => $pageWithIg['instagram_business_account']['id'],
                    'verified'           => true,
                ]
            );

            // 4. First sync
            $ig->syncAccount($account);

            return redirect()->route('creator.profile.show')->with('status', '✓ Instagram connected — followers + ER auto-syncing daily.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('creator.profile.show')->with('error', '❌ Instagram connect failed: '.$e->getMessage());
        }
    }
}
