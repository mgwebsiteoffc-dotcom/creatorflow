<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Services\StripeConnectService;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request, StripeConnectService $stripe)
    {
        $creator = $request->user()->creator;

        $payouts = $creator->payouts()->latest()->paginate(20);
        $availableCents = (int) $creator->payouts()->where('status', 'paid')->sum('net_cents');
        $pendingCents = (int) $creator->payouts()->where('status', 'pending')->sum('amount_cents');

        $onboardingUrl = ! $creator->stripe_connect_id ? $stripe->createExpressAccountLink($creator) : null;

        return view('creator.earnings', compact('payouts', 'availableCents', 'pendingCents', 'onboardingUrl'));
    }
}
