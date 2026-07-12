<?php

namespace App\Notifications;

use App\Models\Events\EventConfirm;
use App\Notifications\Concerns\SendsDiscordDm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventRosterConfirmed extends Notification
{
    use Queueable, SendsDiscordDm;

    public function __construct(protected EventConfirm $confirm)
    {
    }

    public function via($notifiable)
    {
        return $this->viaChannels($notifiable);
    }

    public function toArray($notifiable)
    {
        $event = $this->confirm->event;

        return [
            'title' => 'Added to event roster',
            'body'  => 'You\'re confirmed for ' . ($event ? $event->name : 'an event') . ' as ' . $this->confirm->position . '.',
            'url'   => $event ? route('events.view', $event->slug) : route('dashboard.index'),
            'icon'  => 'fa-calendar-check',
        ];
    }
}
