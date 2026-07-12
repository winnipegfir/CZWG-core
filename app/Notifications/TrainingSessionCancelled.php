<?php

namespace App\Notifications;

use App\Models\AtcTraining\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingSessionCancelled extends Notification
{
    use Queueable;

    /**
     * @param string $cancelledBy Label for who cancelled, e.g. "Your instructor" or "The student".
     * @param string $forRole 'student' or 'instructor' — determines which management page the link opens.
     */
    public function __construct(protected TrainingSession $session, protected string $cancelledBy, protected string $forRole)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $when = $this->session->start_time->copy()->setTimezone($notifiable->displayTimezone())->format('D, M j g:i A');

        return [
            'title' => 'Training session cancelled',
            'body'  => $this->cancelledBy . ' cancelled the session on ' . $when . '.',
            'url'   => $this->forRole === 'instructor' ? route('training.sessions.index') : route('training.book.index'),
            'icon'  => 'fa-calendar-times',
        ];
    }
}
