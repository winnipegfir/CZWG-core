<?php

namespace App\Notifications;

use App\Models\AtcTraining\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingSessionConfirmed extends Notification
{
    use Queueable;

    public function __construct(protected TrainingSession $session)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $instructorName = $this->session->instructor && $this->session->instructor->user
            ? $this->session->instructor->user->fullName('FL')
            : 'Your instructor';

        $when = $this->session->start_time->copy()->setTimezone($notifiable->displayTimezone())->format('D, M j g:i A');

        return [
            'title' => 'Training session confirmed',
            'body'  => $instructorName . ' confirmed your session on ' . $when . '.',
            'url'   => route('training.book.index'),
            'icon'  => 'fa-chalkboard-teacher',
        ];
    }
}
