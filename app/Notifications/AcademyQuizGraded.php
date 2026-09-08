<?php

namespace App\Notifications;

use App\Models\Academy\QuizSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AcademyQuizGraded extends Notification
{
    use Queueable;

    public function __construct(protected QuizSubmission $submission)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $submission = $this->submission;
        $course = $submission->quiz->module->course;
        $percentage = round($submission->percentage());
        $passed = $submission->passed();

        return [
            'title' => 'Academy self assessment graded',
            'body' => $course->title.' was graded at '.$percentage.'% — '.($passed ? 'passed.' : 'not yet passed.'),
            'url' => route('academy.submissions.show', $submission),
            'icon' => $passed ? 'fa-check-circle' : 'fa-clipboard-check',
        ];
    }
}
