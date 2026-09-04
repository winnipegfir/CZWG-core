<?php

namespace App\Notifications;

use App\Models\AtcTraining\TrainingSession;
use App\Notifications\Concerns\SendsDiscordDm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingSessionConfirmed extends Notification
{
    use Queueable, SendsDiscordDm;

    public function __construct(protected TrainingSession $session)
    {
    }

    public function via($notifiable)
    {
        return $this->viaChannels($notifiable);
    }

    public function toArray($notifiable)
    {
        $providerName = $this->session->provider
            ? $this->session->provider->fullName('FL')
            : 'Your training provider';

        $when = $this->session->start_time->copy()->setTimezone($notifiable->displayTimezone())->format('D, M j g:i A');

        return [
            'title' => 'Training session confirmed',
            'body'  => $providerName . ' confirmed your session on ' . $when . '.',
            'url'   => route('training.book.index'),
            'icon'  => 'fa-chalkboard-teacher',
        ];
    }
}
