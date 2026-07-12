<?php

namespace App\Notifications\Concerns;

use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

trait SendsDiscordDm
{
    protected function viaChannels($notifiable, array $default = ['database'])
    {
        if ($notifiable->wantsDiscordNotifications()) {
            $default[] = DiscordChannel::class;
        }

        return $default;
    }

    public function toDiscord($notifiable)
    {
        $data = $this->toArray($notifiable);

        return DiscordMessage::create("**{$data['title']}**\n{$data['body']}\n{$data['url']}");
    }
}
