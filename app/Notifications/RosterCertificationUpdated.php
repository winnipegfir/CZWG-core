<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RosterCertificationUpdated extends Notification
{
    use Queueable;

    /**
     * @param array<int, string> $changes Human-readable lines describing what changed, e.g. "TWR: Solo → Certified".
     */
    public function __construct(protected array $changes)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Your roster record was updated',
            'body'  => implode(', ', $this->changes),
            'url'   => route('profile.view', $notifiable->id),
            'icon'  => 'fa-id-badge',
        ];
    }
}
