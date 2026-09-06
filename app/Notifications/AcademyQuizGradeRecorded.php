<?php

namespace App\Notifications;

use App\Models\Academy\QuizSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AcademyQuizGradeRecorded extends Notification
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
        $student = $submission->user ? $submission->user->fullName('FL') : 'Student';
        $percentage = round($submission->percentage());

        return [
            'title' => 'Academy grade saved',
            'body' => 'You graded '.$student.'\'s '.$course->title.' self assessment at '.$percentage.'%.',
            'url' => route('academy.grading.show', $submission),
            'icon' => 'fa-check-circle',
        ];
    }
}
