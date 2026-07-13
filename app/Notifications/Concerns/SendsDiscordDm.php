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

        $components = null;
        if (method_exists($this, 'discordButton') && ($button = $this->discordButton($notifiable))) {
            $components = [[
                'type' => 1, // action row
                'components' => [[
                    'type' => 2, // button
                    'style' => 5, // link
                    'label' => $button['label'],
                    'url' => $button['url'],
                ]],
            ]];
        }

        // DiscordMessage::create() defaults embed/components to [] rather than
        // null, and the vendor channel always sends both keys — Discord's v10
        // API rejects the empty-array 'embed' field with a 400, so pass null
        // explicitly for embed (and for components too when there's no button).
        return DiscordMessage::create("**{$data['title']}**\n{$data['body']}\n{$data['url']}", null, $components);
    }
}
