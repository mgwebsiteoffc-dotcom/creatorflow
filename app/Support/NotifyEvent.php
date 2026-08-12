<?php

namespace App\Support;

use App\Models\Creator;
use App\Models\User;
use App\Models\Workspace;

/**
 * Sugar over NotificationDispatcher — resolves a recipient from a Creator /
 * User / Workspace / plain array and fires the event.
 */
class NotifyEvent
{
    public static function fire(string $eventKey, Creator|User|Workspace|array|null $recipient, array $vars = [], array $inappMeta = []): array
    {
        $dispatcher = app(NotificationDispatcher::class);
        $to = static::normalize($recipient);
        if (! $to) return ['ok' => false, 'error' => 'no recipient'];
        return $dispatcher->fire($eventKey, $to, $vars, $inappMeta);
    }

    protected static function normalize($recipient): ?array
    {
        if (is_array($recipient)) return $recipient;
        if ($recipient instanceof Creator) {
            return [
                'email' => $recipient->email ?: $recipient->user?->email,
                'phone' => $recipient->phone,
                'name'  => $recipient->display_name,
                'type'  => 'creator',
                'id'    => $recipient->id,
            ];
        }
        if ($recipient instanceof User) {
            return [
                'email' => $recipient->email,
                'phone' => $recipient->phone ?? null,
                'name'  => $recipient->name,
                'type'  => 'user',
                'id'    => $recipient->id,
            ];
        }
        if ($recipient instanceof Workspace) {
            // Send to owner (first workspace user).
            $owner = $recipient->users()->first();
            if (! $owner) return null;
            return [
                'email' => $recipient->contact_email ?: $owner->email,
                'phone' => $recipient->contact_phone ?? null,
                'name'  => $recipient->name,
                'type'  => 'user',
                'id'    => $owner->id,
            ];
        }
        return null;
    }
}
