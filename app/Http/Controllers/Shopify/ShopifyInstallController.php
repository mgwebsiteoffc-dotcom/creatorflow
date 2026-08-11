<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Jobs\SyncChannel;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Domains\AI\Actions\AnalyzeStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShopifyInstallController extends Controller
{
    /**
     * Step 1: merchant clicks install — redirect to Shopify grant screen.
     *
     * If no `shop` parameter is present we render a tiny public install form
     * so the CTA never dead-ends with a validation 422.
     */
    public function install(Request $request)
    {
        // No shop provided → show the install form (public, unauthenticated).
        if (! $request->filled('shop')) {
            return view('shopify.install');
        }

        // Normalise: users may paste "mystore" or "mystore.myshopify.com" or a full URL.
        $shop = trim((string) $request->input('shop'));
        $shop = preg_replace('#^https?://#', '', $shop);
        $shop = preg_replace('#/.*$#', '', $shop);
        if ($shop !== '' && ! str_contains($shop, '.myshopify.com')) {
            $shop = $shop.'.myshopify.com';
        }
        $request->merge(['shop' => $shop]);

        $data = $request->validate([
            'shop' => ['required', 'string', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9\-]*\.myshopify\.com$/'],
        ], [
            'shop.regex' => 'Enter your store as mystore.myshopify.com (or just "mystore").',
        ]);

        $shop = $data['shop'];
        $apiVersion = config('creatorflow.shopify.api_version');
        $scopes = implode(',', config('creatorflow.shopify.scopes'));
        $redirectUri = route('shopify.callback');
        $nonce = Str::random(24);

        session(['shopify_oauth_nonce' => $nonce, 'shopify_shop' => $shop]);

        $url = "https://{$shop}/admin/oauth/authorize?".http_build_query([
            'client_id' => config('creatorflow.shopify.client_id'),
            'scope' => $scopes,
            'redirect_uri' => $redirectUri,
            'state' => $nonce,
        ]);

        return redirect()->away($url);
    }

    /**
     * Step 2: Shopify redirects back with a temporary code. Exchange it for
     * a permanent access token and provision the workspace + channel.
     */
    public function callback(Request $request)
    {
        $shop = session('shopify_shop', $request->get('shop'));
        $state = $request->get('state');

        if ($state !== session('shopify_oauth_nonce')) {
            abort(403, 'OAuth state mismatch.');
        }

        $code = $request->get('code');

        // In local/demo without real Shopify credentials, fabricate a token so
        // the install flow still completes end-to-end.
        if (config('creatorflow.demo.fake_external_calls') || ! config('creatorflow.shopify.client_id')) {
            $accessToken = 'fake_token_'.Str::random(24);
        } else {
            $response = Http::post("https://{$shop}/admin/oauth/access_token", [
                'client_id' => config('creatorflow.shopify.client_id'),
                'client_secret' => config('creatorflow.shopify.client_secret'),
                'code' => $code,
            ])->throw()->json();

            $accessToken = $response['access_token'];
        }

        $user = Auth::user() ?? User::firstOrCreate(
            ['email' => "owner@{$shop}"],
            ['name' => $shop, 'password' => bcrypt(Str::random(32)), 'uuid' => (string) Str::uuid(), 'email_verified_at' => now()]
        );

        $workspace = Workspace::firstOrCreate(
            ['name' => $shop],
            [
                'uuid' => (string) Str::uuid(),
                'website' => "https://{$shop}",
                'plan' => 'free',
                'plan_status' => 'trialing',
                'onboarding_step' => 'shopify_connected',
                'settings' => ['shop_domain' => $shop],
            ]
        );

        if (! $user->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            $user->workspaces()->attach($workspace->id, ['role' => 'owner', 'accepted_at' => now()]);
        }

        $channel = Channel::updateOrCreate(
            ['workspace_id' => $workspace->id, 'type' => 'shopify', 'external_id' => $shop],
            [
                'name' => 'Shopify',
                'credentials' => ['access_token' => $accessToken, 'shop_domain' => $shop],
                'status' => 'active',
            ]
        );

        Auth::login($user);
        session(['active_workspace_id' => $workspace->id]);

        // Kick off the initial product sync + AI store analysis.
        SyncChannel::dispatch($workspace->id, $channel->id, 'products', 'full');

        try {
            app(AnalyzeStore::class)->run($workspace->fresh());
        } catch (\Throwable) {
            // Non-blocking — AI suggestions can be generated later.
        }

        return redirect()->route('brand.dashboard');
    }
}
