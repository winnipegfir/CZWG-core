<?php

namespace Tests\Feature;

use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingSession;
use App\Models\Users\User;
use App\Notifications\TrainingSessionBooked;
use App\Notifications\TrainingSessionConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MentorBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Notification::fake();
    }

    private function account(int $id, int $permission = 1, int $rating = 2): User
    {
        $user = new User([
            'id' => $id,
            'fname' => 'Test',
            'lname' => 'Account',
            'email' => "test{$id}@example.invalid",
            'permissions' => $permission,
            'rating_id' => $rating,
            'timezone' => 'UTC',
        ]);

        // Test accounts use explicit CIDs, not generated database IDs.
        $user->incrementing = false;
        $user->save();

        return User::findOrFail($id);
    }

    private function student(User $user, bool $mentorable = true, ?int $instructorId = null): Student
    {
        return Student::create([
            'user_id' => $user->id, 'mentorable' => $mentorable,
            'instructor_id' => $instructorId, 'status' => 1,
        ]);
    }

    private function slot(User $provider, ?int $instructorId = null): TrainingSession
    {
        return TrainingSession::create([
            'provider_user_id' => $provider->id, 'instructor_id' => $instructorId,
            'start_time' => now()->addDays(2)->startOfHour(),
            'end_time' => now()->addDays(2)->startOfHour()->addHours(3),
            'status' => 'open',
        ]);
    }

    private function book(User $user, TrainingSession $slot)
    {
        return $this->actingAs($user)->post(route('training.book.store'), [
            'provider_user_id' => $slot->provider_user_id,
            'start_time' => $slot->start_time->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_mentor_can_post_and_render_own_availability(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('training_sessions', ['provider_user_id' => $mentor->id, 'status' => 'open']);
        $this->get(route('training.sessions.index'))->assertOk();
    }

    public function test_low_rated_mentor_and_staff_only_cannot_post(): void
    {
        $payload = ['start_time' => now()->addDay()->toDateTimeString(), 'end_time' => now()->addDay()->addHour()->toDateTimeString()];
        $this->actingAs($this->account(910001, 2, 3))->post(route('training.sessions.store'), $payload)->assertForbidden();
        $this->actingAs($this->account(910002, 4, 5))->post(route('training.sessions.store'), $payload)->assertForbidden();
    }

    public function test_eligible_student_books_and_provider_confirms(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $user = $this->account(910002);
        $student = $this->student($user);
        $slot = $this->slot($mentor);
        $this->actingAs($user)->get(route('training.book.index'))->assertOk();
        $this->book($user, $slot)->assertRedirect();
        $this->assertDatabaseHas('training_sessions', ['id' => $slot->id, 'student_id' => $student->id, 'status' => 'pending']);
        Notification::assertSentTo($mentor, TrainingSessionBooked::class);
        $this->actingAs($mentor)->post(route('training.sessions.confirm', $slot->id))->assertRedirect();
        $this->assertSame('booked', $slot->fresh()->status);
        Notification::assertSentTo($user, TrainingSessionConfirmed::class);
    }

    public function test_equal_rating_and_nonmentorable_student_are_rejected(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $slot = $this->slot($mentor);
        $equal = $this->account(910002, 1, 4);
        $this->student($equal);
        $this->book($equal, $slot)->assertRedirect();
        $other = $this->account(910003);
        $this->student($other, false);
        $this->book($other, $slot)->assertRedirect();
        $this->assertSame('open', $slot->fresh()->status);
        Notification::assertNothingSent();
    }

    public function test_provider_cannot_manage_another_providers_slot(): void
    {
        $slot = $this->slot($this->account(910001, 2, 4));
        $this->actingAs($this->account(910002, 2, 5));
        $this->delete(route('training.sessions.destroy', $slot->id))->assertForbidden();
        $this->post(route('training.sessions.cancel', $slot->id))->assertForbidden();
        $this->post(route('training.sessions.confirm', $slot->id))->assertForbidden();
        $this->assertSame('open', $slot->fresh()->status);
    }

    public function test_assigned_instructor_booking_still_works_without_provider_input(): void
    {
        $provider = $this->account(910001, 3, 8);
        $instructor = Instructor::withoutEvents(fn () => Instructor::create([
            'user_id' => $provider->id, 'qualification' => 'Test', 'email' => $provider->email,
        ]));
        $user = $this->account(910002);
        $student = $this->student($user, false, $instructor->id);
        $slot = $this->slot($provider, $instructor->id);
        $this->actingAs($user)->post(route('training.book.store'), [
            'start_time' => $slot->start_time->toDateTimeString(),
        ])->assertRedirect();
        $this->assertDatabaseHas('training_sessions', ['id' => $slot->id, 'student_id' => $student->id, 'status' => 'pending']);
    }

    public function test_duplicate_booking_cannot_claim_same_time(): void
    {
        $slot = $this->slot($this->account(910001, 2, 4));
        $first = $this->account(910002);
        $second = $this->account(910003);
        $student = $this->student($first);
        $this->student($second);
        $this->book($first, $slot)->assertRedirect();
        $this->book($second, $slot)->assertRedirect();
        $this->assertSame($student->id, $slot->fresh()->student_id);
    }

    public function test_student_cannot_book_overlapping_sessions_with_different_providers(): void
    {
        $first = $this->slot($this->account(910001, 2, 4));
        $second = $this->slot($this->account(910002, 2, 5));
        $user = $this->account(910003);
        $this->student($user);
        $this->book($user, $first);
        $this->book($user, $second);
        $this->assertSame('pending', $first->fresh()->status);
        $this->assertSame('open', $second->fresh()->status);
    }

    public function test_student_can_cancel_and_availability_rejoins(): void
    {
        $slot = $this->slot($this->account(910001, 2, 4));
        $end = $slot->end_time->toDateTimeString();
        $user = $this->account(910002);
        $this->student($user);
        $this->book($user, $slot);
        $this->actingAs($user)->post(route('training.book.cancel', $slot->id))->assertRedirect();
        $this->assertSame('open', $slot->fresh()->status);
        $this->assertNull($slot->fresh()->student_id);
        $this->assertSame($end, $slot->fresh()->end_time->toDateTimeString());
    }

    public function test_stale_signed_confirmation_is_rejected_after_reassignment(): void
    {
        $slot = $this->slot($this->account(910001, 2, 4));
        $user = $this->account(910002);
        $this->student($user);
        $this->book($user, $slot);
        $slot->refresh();
        $url = URL::temporarySignedRoute('training.sessions.discordconfirm', now()->addHour(), [
            'id' => $slot->id, 'booking' => $slot->confirmationContext(),
        ]);
        $slot->provider_user_id = $this->account(910003, 2, 5)->id;
        $slot->save();
        $this->get($url)->assertForbidden();
        $this->assertSame('pending', $slot->fresh()->status);
    }
}
