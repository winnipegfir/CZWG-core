<?php

namespace App\Services;

use App\Classes\VatsimRating;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;

class TrainingBookingEligibility
{
    public static function canProvide(User $provider): bool
    {
        // The locally assigned Mentor role controls access to provider tools.
        // Rating compatibility is checked separately for each student in allows().
        return $provider->isTrainingMentor();
    }

    public static function allows(Student $student, ?User $provider, $instructorId): bool
    {
        if (!$provider || !$student->user || (int) $student->user_id === (int) $provider->id) {
            return false;
        }

        if (!$student->mentorable) {
            return $student->instructor_id !== null
                && (int) $student->instructor_id === (int) $instructorId
                && $provider->isTrainingInstructor();
        }

        if ($provider->isTrainingInstructor()) {
            return true;
        }

        $mentorRating = (int) $provider->rating_id;
        $studentRating = (int) $student->user->rating_id;

        // Supervisors are a rare special case: when the site has explicitly
        // granted them the Mentor role, treat SUP as above the controller and
        // instructor ratings. ADM and unknown/inactive ratings do not qualify
        // here unless the user already qualifies as a training instructor.
        $mentorHasEligibleRating = ($mentorRating >= VatsimRating::Student3->value
                && $mentorRating <= VatsimRating::Instructor3->value)
            || $mentorRating === VatsimRating::Supervisor->value;

        return $provider->isTrainingMentor()
            && $mentorHasEligibleRating
            && $studentRating >= VatsimRating::Observer->value
            && $studentRating <= VatsimRating::Instructor3->value
            && $mentorRating > $studentRating;
    }
}
