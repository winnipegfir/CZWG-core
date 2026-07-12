<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Discord\DiscordChannel;
use Throwable;

/**
 * A Discord DM failure (missing/expired bot token, stale DM channel, Discord
 * being down, etc.) should never crash the request that triggered the
 * underlying notification — the vendor channel doesn't catch anything, so we
 * decorate it here and log instead of letting the exception propagate.
 */
class SafeDiscordChannel extends DiscordChannel
{
    public function send($notifiable, Notification $notification)
    {
        try {
            return parent::send($notifiable, $notification);
        } catch (Throwable $e) {
            Log::warning('Discord DM notification failed: ' . $e->getMessage(), [
                'notifiable_id' => $notifiable->id ?? null,
                'notification'  => get_class($notification),
            ]);

            return null;
        }
    }
}
