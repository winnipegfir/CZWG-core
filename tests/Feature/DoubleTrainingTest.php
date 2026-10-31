<?php
namespace Tests\Feature;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingSession;
use App\Models\Users\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DoubleTrainingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app('view')->getFinder()->prependLocation(base_path('tests/Fixtures/sector-files'));
        config(['app.key' => 'base64:'.base64_encode(str_repeat('x',32))]);
        Notification::fake(); Http::preventStrayRequests();
        Schema::create('users', function (Blueprint $t) {
            $t->unsignedInteger('id')->primary();
            foreach (['fname','lname','email','timezone'] as $f) $t->string($f)->nullable();
            $t->integer('permissions'); $t->integer('rating_id'); $t->timestamps();
        });
        Schema::create('instructors', function (Blueprint $t) { $t->increments('id'); $t->integer('user_id'); $t->timestamps(); });
        Schema::create('teachers', function (Blueprint $t) { $t->increments('id'); $t->integer('user_cid'); $t->boolean('is_instructor')->default(false); });
        Schema::create('students', function (Blueprint $t) {
            $t->increments('id'); $t->integer('user_id'); $t->boolean('mentorable');
            $t->integer('instructor_id')->nullable(); $t->integer('status'); $t->timestamps();
        });
        Schema::create('training_sessions', function (Blueprint $t) {
            $t->increments('id'); $t->integer('provider_user_id'); $t->integer('instructor_id')->nullable();
            $t->integer('student_id')->nullable(); $t->dateTime('start_time'); $t->dateTime('end_time');
            $t->string('note')->nullable(); $t->string('status'); $t->dateTime('booked_at')->nullable(); $t->timestamps();
        });
        (require database_path('migrations/2026_10_30_000001_add_double_training_seats.php'))->up();
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

    public function test_double_training_accepts_two_independent_students_but_not_a_third(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $start = now()->addDays(2)->startOfHour();
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => $start->toDateTimeString(), 'end_time' => $start->copy()->addHours(3)->toDateTimeString(), 'double_training' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertSame(2, TrainingSession::count());
        $seats = TrainingSession::orderBy('seat')->get();
        $this->assertSame($seats[0]->availability_group, $seats[1]->availability_group);
        $first = $this->account(910002); $this->student($first);
        $second = $this->account(910003); $this->student($second);
        $third = $this->account(910004); $this->student($third);
        $this->book($first, $seats[0]);
        $this->book($second, $seats[1]);
        $this->book($third, $seats[1]);
        $this->assertSame(2, TrainingSession::where('status', 'pending')->count());
        $this->assertSame(4, TrainingSession::where('status', 'open')->count() + TrainingSession::where('status', 'pending')->count());
        $bookings = TrainingSession::where('status', 'pending')->get();
        $this->assertNotEquals($bookings[0]->student_id, $bookings[1]->student_id);
        $this->actingAs($mentor)->post(route('training.sessions.confirm', $bookings[0]->id));
        $this->assertSame('booked', $bookings[0]->fresh()->status);
        $this->assertSame('pending', $bookings[1]->fresh()->status);
        $this->actingAs($mentor)->post(route('training.sessions.cancel', $bookings[0]->id));
        $this->assertSame('pending', $bookings[1]->fresh()->status);
        $this->book($third, $seats[0]);
        $this->assertSame(2, TrainingSession::whereIn('status', ['pending', 'booked'])->count());
    }

    public function test_student_cannot_book_both_double_training_seats(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $start = now()->addDays(2)->startOfHour();
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => $start->toDateTimeString(), 'end_time' => $start->copy()->addHour()->toDateTimeString(), 'double_training' => 1,
        ]);
        $student = $this->account(910002); $this->student($student);
        $seats = TrainingSession::get();
        $this->book($student, $seats[0]); $this->book($student, $seats[1]);
        $this->assertSame(1, TrainingSession::where('status', 'pending')->count());
        $this->assertSame(1, TrainingSession::where('status', 'open')->count());
    }

    public function test_single_training_remains_single_and_overlapping_availability_is_rejected(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $slot = $this->slot($mentor);
        $one = $this->account(910002); $this->student($one);
        $two = $this->account(910003); $this->student($two);
        $this->book($one, $slot); $this->book($two, $slot);
        $this->assertSame(1, TrainingSession::where('status','pending')->count());
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => $slot->start_time->toDateTimeString(), 'end_time' => $slot->end_time->toDateTimeString(), 'double_training' => 1,
        ]);
        $this->assertSame(0, TrainingSession::where('capacity',2)->count());
    }

    public function test_unauthorized_user_cannot_create_double_availability(): void
    {
        $user = $this->account(910002); $this->student($user);
        $this->actingAs($user)->post(route('training.sessions.store'), [
            'start_time' => now()->addDay()->toDateTimeString(), 'end_time' => now()->addDay()->addHour()->toDateTimeString(), 'double_training' => 1,
        ])->assertForbidden();
        $this->assertSame(0, TrainingSession::count());
    }

    public function test_second_seat_still_enforces_student_eligibility(): void
    {
        $mentor = $this->account(910001, 2, 4);
        $start = now()->addDays(2)->startOfHour();
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => $start->toDateTimeString(), 'end_time' => $start->copy()->addHour()->toDateTimeString(), 'double_training' => 1,
        ]);
        $one = $this->account(910002); $this->student($one);
        $higher = $this->account(910003,1,5); $this->student($higher);
        $seats = TrainingSession::get();
        $this->book($one,$seats[0]); $this->book($higher,$seats[1]);
        $this->assertSame(1,TrainingSession::where('status','pending')->count());
        $this->assertSame(1,TrainingSession::where('status','open')->count());
    }

    public function test_double_slot_forms_render_shared_session_labels(): void
    {
        app('view')->getFinder()->prependLocation(base_path('tests/Fixtures/sector-files'));
        $mentor = $this->account(910001, 2, 4);
        $start = now()->addDays(2)->startOfHour();
        $this->actingAs($mentor)->post(route('training.sessions.store'), [
            'start_time' => $start->toDateTimeString(), 'end_time' => $start->copy()->addHour()->toDateTimeString(), 'double_training' => 1,
        ]);
        $this->get(route('training.sessions.index'))->assertOk()->assertSee('Double training')->assertSee('seat 1/2');
        $one = $this->account(910002); $this->student($one);
        $this->actingAs($one)->get(route('training.book.index'))->assertOk()->assertSee('may be shared with another student');
    }
}
