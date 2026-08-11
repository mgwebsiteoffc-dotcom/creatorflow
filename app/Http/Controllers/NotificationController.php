<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        [$type, $id] = $this->recipient($request);
        $notifications = AppNotification::where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->latest()
            ->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    public function open(AppNotification $notification, Request $request)
    {
        [$type, $id] = $this->recipient($request);
        abort_unless($notification->recipient_type === $type && $notification->recipient_id === $id, 403);
        $notification->update(['read_at' => now()]);

        return redirect()->to($notification->data['url'] ?? url('/'));
    }

    public function markAllRead(Request $request)
    {
        [$type, $id] = $this->recipient($request);
        AppNotification::where('recipient_type', $type)
            ->where('recipient_id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked read.');
    }

    protected function recipient(Request $request): array
    {
        $user = $request->user();
        return $user->creator
            ? ['creator', $user->creator->id]
            : ['user', $user->id];
    }
}
