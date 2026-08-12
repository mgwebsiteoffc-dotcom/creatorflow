<?php

namespace App\Http\Controllers;

use App\Models\CampaignInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Magic-link handler for creator invitation emails / WhatsApp messages.
 *
 * URL:  /i/{uuid}
 *
 * Behavior:
 *   1. Look up the invitation. If missing / expired → soft "no longer valid" screen.
 *   2. If the visitor is already logged in as the target creator → straight to
 *      /creator/invitations with the row highlighted.
 *   3. If logged out but the creator's user account exists → auto-login (the
 *      invitation UUID + a matching email is the auth signal; this mirrors
 *      how "magic link" flows work in Slack / Notion / Vercel).
 *   4. Anonymous + no matching account → registration screen pre-filled with
 *      the creator's email so signup completes the round trip.
 */
class InvitationLinkController extends Controller
{
    public function open(string $uuid, Request $request)
    {
        $invitation = CampaignInvitation::with(['creator.user', 'campaign.workspace'])
            ->where('uuid', $uuid)
            ->first();

        if (! $invitation) {
            return response()->view('invitations.expired', [], 404);
        }

        // Silently mark it "opened" so the brand's analytics reflect email opens.
        if (! $invitation->opened_at) {
            $invitation->update(['opened_at' => now()]);
        }

        // Expired?
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return response()->view('invitations.expired', ['invitation' => $invitation], 410);
        }

        $creator = $invitation->creator;
        $creatorUser = $creator?->user;
        $currentUser = $request->user();

        // Already logged in as the right person — jump right to the invitations screen.
        if ($currentUser && $creatorUser && $currentUser->id === $creatorUser->id) {
            return redirect()
                ->route('creator.invitations', ['highlight' => $invitation->uuid])
                ->with('status', "Opening your invitation from {$invitation->campaign->workspace->name}…");
        }

        // Anonymous but the creator has a user account → magic-login.
        // We trust the UUID because it's 128-bit random and only the intended
        // recipient's email address ever received it. Same trust model as
        // Laravel's signed URL / MustVerifyEmail flow.
        if (! $currentUser && $creatorUser) {
            Auth::login($creatorUser, remember: true);
            $request->session()->regenerate();

            return redirect()
                ->route('creator.invitations', ['highlight' => $invitation->uuid])
                ->with('status', "Welcome back, {$creator->display_name}!");
        }

        // Logged in as someone else — sign out first and try again on the same link.
        if ($currentUser && $creatorUser && $currentUser->id !== $creatorUser->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect(route('invite.open', ['uuid' => $uuid]));
        }

        // Fully anonymous + no user account for this creator yet → registration
        // page pre-filled with the invitation email so signup completes the loop.
        return redirect()->route('register', [
            'invite' => $invitation->uuid,
            'email'  => $creator?->email,
            'name'   => $creator?->display_name,
        ]);
    }
}
