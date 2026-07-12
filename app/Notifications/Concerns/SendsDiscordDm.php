<?php

namespace App\Notifications\Concerns;

use App\Notifications\Channels\SafeDiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

trait SendsDiscordDm
{
    protected function viaChannels($notifiable, array $default = ['database'])
    {
        if ($notifiable->wantsDiscordNotifications()) {
            $default[] = SafeDiscordChannel::class;
        }

        return $default;
    }

    public function toDiscord($notifiable)
    {
        $data = $this->toArray($notifiable);

        // DiscordMessage::create() defaults embed/components to [] rather than
        // null, and the vendor channel always sends both keys — Discord's v10
        // API rejects the empty-array 'embed' field with a 400, so pass null
        // explicitly for both instead of relying on the vendor default.
        return DiscordMessage::create("**{$data['title']}**\n{$data['body']}\n{$data['url']}", null, null);
    }
}
