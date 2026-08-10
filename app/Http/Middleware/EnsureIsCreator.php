<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsCreator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->creator) {
            return redirect()->route('creator.onboarding.create')->with('error', 'Complete your creator profile to continue.');
        }

        view()->share('currentCreator', $user->creator);

        return $next($request);
    }
}
