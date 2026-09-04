<?php

namespace App\Services;

use App\Classes\VatsimRating;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;

class TrainingBookingEligibility
{
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

        // SUP/ADM are administrative ratings, not evidence of controller qualification.
        // Unknown, suspended and inactive ratings must not qualify automatically.
        return $provider->isTrainingMentor()
            && $mentorRating >= VatsimRating::Student3->value
            && $mentorRating <= VatsimRating::Instructor3->value
            && $studentRating >= VatsimRating::Observer->value
            && $studentRating <= VatsimRating::Instructor3->value
            && $mentorRating > $studentRating;
    }
}
