<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\PushSubscription;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * GET /push/vapid-key — returns the platform VAPID public key so the
     * browser can subscribe.
     */
    public function vapidKey()
    {
        if (! SchemaCheck::has('platform_settings')) return response()->json(['enabled' => false]);
        $s = PlatformSetting::current();
        return response()->json([
            'enabled'   => (bool) ($s->vapid_public_key ?? null),
            'publicKey' => $s->vapid_public_key ?? null,
        ]);
    }

    /**
     * POST /push/subscribe — save subscription for the current user/creator.
     */
    public function subscribe(Request $request)
    {
        if (! SchemaCheck::has('push_subscriptions')) return response()->json(['ok' => false, 'error' => 'not_migrated'], 501);

        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:512'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth'   => ['required', 'string'],
        ]);

        $user = $request->user();
        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id'      => $user?->id,
                'creator_id'   => $user?->creator?->id,
                'p256dh'       => $data['keys']['p256dh'],
                'auth_token'   => $data['keys']['auth'],
                'user_agent'   => substr((string) $request->userAgent(), 0, 255),
                'last_seen_at' => now(),
            ]
        );
        return response()->json(['ok' => true]);
    }

    /**
     * POST /push/unsubscribe — remove subscription.
     */
    public function unsubscribe(Request $request)
    {
        if (! SchemaCheck::has('push_subscriptions')) return response()->json(['ok' => true]);
        $endpoint = (string) $request->input('endpoint');
        if ($endpoint) PushSubscription::where('endpoint', $endpoint)->delete();
        return response()->json(['ok' => true]);
    }
}
