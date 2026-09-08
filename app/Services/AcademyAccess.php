<?php

namespace App\Services;

use App\Models\Academy\Course;
use App\Models\Academy\VatcanMember;
use App\Models\Users\User;

class AcademyAccess
{
    public static function isQualifiedVisitor(User $user): bool
    {
        return VatcanMember::where('cid', $user->id)
            ->where('active_visitor_member', true)
            ->where('rating_id', '>=', 4)
            ->exists();
    }

    public static function canSeeCourse(User $user, Course $course): bool
    {
        if (! $course->visitor_only) {
            return true;
        }

        // Academy staff need to preview and teach visitor material. Ordinary home
        // controllers never see visitor-only course cards.
        return (int) $user->permissions >= 5
            || $user->isTrainingInstructor()
            || self::isQualifiedVisitor($user);
    }

    public static function canViewCourse(User $user, Course $course): bool
    {
        if (! $course->published || (int) $user->permissions < 1) {
            return false;
        }

        if (! self::canSeeCourse($user, $course)) {
            return false;
        }

        // Administrators and instructors need the complete published catalogue for testing, teaching and review.
        if ((int) $user->permissions >= 5 || $user->isTrainingInstructor()) {
            return true;
        }

        // Student access is now based on an active enrollment. The VATCAN sync creates
        // rating-based enrollments, while admins can still create manual overrides.
        return $course->enrollments()
            ->where('user_id', $user->id)
            ->where('active', true)
            ->exists();
    }
}
