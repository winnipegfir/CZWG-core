<?php

namespace App\Console\Commands;

use App\Models\Users\User;
use App\Services\TrainingBookingEligibility;
use Illuminate\Console\Command;

class CheckTrainingBooking extends Command
{
    protected $signature = 'training:check-booking {student_cid} {provider_cid}';
    protected $description = 'Read-only check of current student/provider booking eligibility (does not book a slot)';

    public function handle(): int
    {
        $studentUser = User::find($this->argument('student_cid'));
        $provider = User::find($this->argument('provider_cid'));
        if (!$studentUser || !$studentUser->studentProfile || !$provider) {
            $this->error('Student profile or provider account not found.');
            return 1;
        }
        $student = $studentUser->studentProfile;
        $allowed = TrainingBookingEligibility::allows($student, $provider, $provider->instructorProfile?->id);
        $this->table(['Check', 'Value'], [
            ['Student CID', $studentUser->id], ['Student rating ID', $studentUser->rating_id],
            ['Mentorable', $student->mentorable ? 'yes' : 'no'],
            ['Provider CID', $provider->id], ['Provider rating ID', $provider->rating_id],
            ['Instructor access', $provider->isTrainingInstructor() ? 'yes' : 'no'],
            ['Mentor access', $provider->isTrainingMentor() ? 'yes' : 'no'],
            ['Eligibility', $allowed ? 'ALLOWED' : 'DENIED'],
        ]);
        $this->line('Eligibility only: availability, overlapping bookings and future start time are checked when booking. No records changed.');
        return 0;
    }
}
