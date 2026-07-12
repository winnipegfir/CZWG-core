<?php

namespace App\Notifications;

use App\Models\AtcTraining\TrainingSession;
use App\Notifications\Concerns\SendsDiscordDm;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingSessionBooked extends Notification
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
        $studentName = $this->session->student && $this->session->student->user
            ? $this->session->student->user->fullName('FL')
            : 'A student';

        $when = $this->session->start_time->copy()->setTimezone($notifiable->displayTimezone())->format('D, M j g:i A');

        return [
            'title' => 'New training slot booked',
            'body'  => $studentName . ' booked ' . $when . ' — confirm it to lock it in.',
            'url'   => route('training.sessions.index'),
            'icon'  => 'fa-calendar-check',
        ];
    }
}
