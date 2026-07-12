<?php

namespace App\Notifications;

use App\Models\Events\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventRosterRemoved extends Notification
{
    use Queueable;

    public function __construct(protected Event $event)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Removed from event roster',
            'body'  => 'You were removed from the roster for ' . $this->event->name . '.',
            'url'   => route('events.view', $this->event->slug),
            'icon'  => 'fa-calendar-times',
        ];
    }
}
