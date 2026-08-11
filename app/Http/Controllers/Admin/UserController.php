<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
            ->when($request->get('status') === 'suspended', fn ($q) => $q->where('account_status', 'suspended'))
            ->when($request->get('role'), fn ($q, $r) => $q->where('system_role', $r))
            ->withCount(['workspaces'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['workspaces', 'creator']);

        $paymentRecords = collect();
        $notifications  = collect();
        if (\App\Support\SchemaCheck::has('payment_records')) {
            $paymentRecords = \App\Models\PaymentRecord::where('recorded_by', $user->id)
                ->with('workspace:id,name')->latest()->take(20)->get();
        }
        if (\App\Support\SchemaCheck::has('notifications')) {
            $notifications = \App\Models\AppNotification::where('recipient_type', 'user')
                ->where('recipient_id', $user->id)->latest()->take(20)->get();
        }

        return view('admin.users.show', compact('user', 'paymentRecords', 'notifications'));
    }

    public function suspend(User $user, Request $request)
    {
        abort_if($user->id === $request->user()->id, 400, 'You cannot suspend yourself.');
        abort_if($user->isSuperAdmin(), 403, 'Superadmins cannot be suspended.');

        $user->update([
            'account_status'    => 'suspended',
            'suspension_reason' => $request->input('reason'),
            'suspended_at'      => now(),
        ]);

        return back()->with('status', "{$user->name} has been suspended.");
    }

    public function unsuspend(User $user)
    {
        $user->update([
            'account_status'    => 'active',
            'suspension_reason' => null,
            'suspended_at'      => null,
        ]);

        return back()->with('status', "{$user->name} has been reactivated.");
    }

    public function makeAdmin(User $user)
    {
        $user->update(['system_role' => 'admin']);
        return back()->with('status', "{$user->name} is now an admin.");
    }

    public function removeAdmin(User $user, Request $request)
    {
        abort_if($user->id === $request->user()->id, 400, 'You cannot demote yourself.');
        $user->update(['system_role' => 'user']);
        return back()->with('status', "{$user->name} is no longer an admin.");
    }
}
