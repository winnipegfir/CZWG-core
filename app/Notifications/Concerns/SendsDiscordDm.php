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

        return DiscordMessage::create("**{$data['title']}**\n{$data['body']}\n{$data['url']}");
    }
}
