<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'in:brand,creator'],
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->account_type === 'brand') {
            $workspace = Workspace::create([
                'uuid' => (string) Str::uuid(),
                'name' => $request->workspace_name ?? Str::before($request->email, '@')."'s brand",
                'plan' => 'free',
                'plan_status' => 'trialing',
                'onboarding_step' => 'signup',
            ]);

            $user->workspaces()->attach($workspace->id, ['role' => 'owner', 'accepted_at' => now()]);
        }

        event(new Registered($user));
        Auth::login($user, true);

        if ($request->account_type === 'creator') {
            return redirect()->route('creator.onboarding.create');
        }

        return redirect()->route('brand.onboarding');
    }
}
