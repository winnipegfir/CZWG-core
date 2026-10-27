<?php

namespace App\Services;

use App\Classes\VatsimRating;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;

class TrainingBookingEligibility
{
    public static function canProvide(User $provider): bool
    {
        $rating = (int) $provider->rating_id;

        return $provider->isTrainingMentor()
            && (($rating >= VatsimRating::Student3->value
                    && $rating <= VatsimRating::Instructor3->value)
                || $rating === VatsimRating::Supervisor->value);
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

        // A Supervisor may provide training only when Winnipeg has explicitly
        // granted the local Mentor role. ADM and unknown/inactive ratings are
        // not part of this exception.
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
