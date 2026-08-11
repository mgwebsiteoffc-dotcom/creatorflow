<?php

namespace App\View\Composers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();
        if (! $user) {
            $view->with(['recentNotifications' => collect(), 'unreadCount' => 0]);
            return;
        }

        $recipientType = $user->creator ? 'creator' : 'user';
        $recipientId   = $user->creator ? $user->creator->id : $user->id;

        $q = AppNotification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->latest();

        $view->with([
            'recentNotifications' => (clone $q)->take(8)->get(),
            'unreadCount' => (clone $q)->whereNull('read_at')->count(),
        ]);
    }
}
