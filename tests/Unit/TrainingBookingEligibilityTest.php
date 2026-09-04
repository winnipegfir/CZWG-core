<?php

namespace Tests\Unit;

use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use App\Services\TrainingBookingEligibility;
use PHPUnit\Framework\TestCase;

class TrainingBookingEligibilityTest extends TestCase
{
    /** @dataProvider eligibilityCases */
    public function test_booking_eligibility($mentorRating, $studentRating, $mentorable, $instructor, $mentor, $assigned, $self, $expected): void
    {
        $provider = $this->getMockBuilder(User::class)
            ->onlyMethods(['isTrainingInstructor', 'isTrainingMentor'])->getMock();
        $provider->method('isTrainingInstructor')->willReturn($instructor);
        $provider->method('isTrainingMentor')->willReturn($mentor);
        $provider->id = $self ? 100 : 200;
        $provider->rating_id = $mentorRating;

        $studentUser = new User(['id' => 100, 'rating_id' => $studentRating]);
        $student = new Student(['user_id' => 100, 'mentorable' => $mentorable, 'instructor_id' => $assigned ? 7 : null]);
        $student->setRelation('user', $studentUser);

        $this->assertSame($expected, TrainingBookingEligibility::allows($student, $provider, $instructor ? 7 : null));
    }

    public static function eligibilityCases(): array
    {
        return [
            'S3 mentor with S1 student' => [4, 2, true, false, true, false, false, true],
            'S3 mentor with S2 student' => [4, 3, true, false, true, false, false, true],
            'equal ratings denied' => [4, 4, true, false, true, false, false, false],
            'higher student denied' => [4, 5, true, false, true, false, false, false],
            'C1 mentor with S3 student' => [5, 4, true, false, true, false, false, true],
            'mentor below S3 denied' => [3, 2, true, false, true, false, false, false],
            'rating alone does not grant mentor role' => [5, 2, true, false, false, false, false, false],
            'nonmentorable cannot book mentor' => [4, 2, false, false, true, false, false, false],
            'assigned instructor allowed' => [8, 2, false, true, true, true, false, true],
            'unassigned instructor denied for nonmentorable' => [8, 2, false, true, true, false, false, false],
            'mentorable can book unassigned instructor' => [8, 2, true, true, true, false, false, true],
            'self booking mentor denied' => [5, 4, true, false, true, false, true, false],
            'self booking instructor denied' => [8, 4, true, true, true, true, true, false],
            'supervisor rating not controller qualification' => [11, 2, true, false, true, false, false, false],
            'inactive student denied mentor' => [4, -1, true, false, true, false, false, false],
        ];
    }
}
