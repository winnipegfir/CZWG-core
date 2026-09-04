<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingSession;
use App\Models\Users\User;
use App\Notifications\TrainingSessionBooked;
use App\Notifications\TrainingSessionCancelled;
use App\Notifications\TrainingSessionConfirmed;
use App\Services\TrainingBookingEligibility;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingSessionController extends Controller
{
    public function instructorIndex()
    {
        $slots = TrainingSession::where('provider_user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        $userTz = Auth::user()->displayTimezone();

        return view('dashboard.training.sessions.instructor', compact('slots', 'userTz'));
    }

    public function store(Request $request)
    {
        abort_unless(TrainingBookingEligibility::canProvide(Auth::user()), 403);
        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'note'       => 'nullable|string|max:255',
        ]);

        $userTz = Auth::user()->displayTimezone();

        $start = Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC');
        $end = Carbon::parse($request->input('end_time'), $userTz)->setTimezone('UTC');
        if ($start->lt(now())) {
            return redirect()->back()->withError('Availability must start in the future.');
        }

        $created = DB::transaction(function () use ($request, $start, $end) {
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            if (TrainingSession::where('provider_user_id', Auth::id())
                ->where('status', '!=', 'cancelled')
                ->where('start_time', '<', $end)->where('end_time', '>', $start)->exists()) {
                return false;
            }
            return TrainingSession::create([
            'instructor_id' => optional(Auth::user()->instructorProfile)->id,
            'provider_user_id' => Auth::id(),
            'start_time'    => $start,
            'end_time'      => $end,
            'note'          => $request->input('note'),
            'status'        => 'open',
            ]);
        });

        if (!$created) {
            return redirect()->back()->withError('That availability overlaps an existing session.');
        }

        return redirect()->back()->withSuccess('Slot added.');
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
        $slot = $this->lockSession($id);
        abort_if((int) $slot->provider_user_id !== (int) Auth::id(), 403);

        if ($slot->status !== 'open') {
            return redirect()->back()->withError('Only open (unbooked) slots can be deleted. Cancel a booked slot instead.');
        }

        $slot->delete();

        return redirect()->back()->withSuccess('Slot removed.');
        });
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
        $slot = $this->lockSession($id);
        abort_if((int) $slot->provider_user_id !== (int) Auth::id(), 403);
        abort_unless(in_array($slot->status, ['pending', 'booked']), 409);

        if ($slot->student && $slot->student->user) {
            $slot->student->user->notify(new TrainingSessionCancelled($slot, 'Your training provider', 'student'));
        }

        $slot->student_id = null;
        $slot->status = 'open';
        $slot->booked_at = null;

        DB::transaction(fn () => $this->mergeAdjacentOpenSlots($slot));

        return redirect()->back()->withSuccess('Session cancelled — the time is open again.');
        });
    }

    /**
     * When a slot becomes open again (cancellation, unassignment), fold it
     * into any touching open slots from the same training provider with the same
     * note so booking one hour at a time doesn't fragment availability into
     * a pile of adjacent slivers.
     */
    private function mergeAdjacentOpenSlots(TrainingSession $slot): TrainingSession
    {
        $matchesNote = function ($query) use ($slot) {
            return is_null($slot->note) ? $query->whereNull('note') : $query->where('note', $slot->note);
        };

        $merged = true;
        while ($merged) {
            $merged = false;

            $left = $matchesNote(
                TrainingSession::where('provider_user_id', $slot->provider_user_id)
                    ->where('status', 'open')
                    ->where('id', '!=', $slot->id)
                    ->where('end_time', $slot->start_time)
                    ->lockForUpdate()
            )->first();

            if ($left) {
                $slot->start_time = $left->start_time;
                $left->delete();
                $merged = true;
            }

            $right = $matchesNote(
                TrainingSession::where('provider_user_id', $slot->provider_user_id)
                    ->where('status', 'open')
                    ->where('id', '!=', $slot->id)
                    ->where('start_time', $slot->end_time)
                    ->lockForUpdate()
            )->first();

            if ($right) {
                $slot->end_time = $right->end_time;
                $right->delete();
                $merged = true;
            }
        }

        $slot->save();

        return $slot;
    }

    public function confirm($id)
    {
        return DB::transaction(function () use ($id) {
        $slot = $this->lockSession($id);
        abort_if((int) $slot->provider_user_id !== (int) Auth::id(), 403);

        if ($slot->status !== 'pending') {
            return redirect()->back()->withError('Only pending sessions can be confirmed.');
        }

        abort_unless($slot->student && TrainingBookingEligibility::allows(
            $slot->student, $slot->provider, $slot->instructor_id
        ), 403, 'This booking is no longer eligible.');

        $slot->status = 'booked';
        $slot->save();

        if ($slot->student && $slot->student->user) {
            $slot->student->user->notify(new TrainingSessionConfirmed($slot));
        }

        return redirect()->back()->withSuccess('Session confirmed.');
        });
    }

    /**
     * One-click confirm from the "Confirm Slot" button in the Discord DM.
     * Authorization here is the signature itself (the link is only ever
     * handed out privately, scoped to one session, and time-limited) rather
     * than the logged-in session, so this works even from a phone that isn't
     * logged into the site.
     */
    public function discordConfirm(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
        $slot = $this->lockSession($id);

        abort_unless(hash_equals($slot->confirmationContext(), (string) $request->query('booking', '')), 403,
            'This confirmation link is no longer valid. Please confirm from the website.');

        abort_unless($slot->student && TrainingBookingEligibility::allows(
            $slot->student, $slot->provider, $slot->instructor_id
        ), 403, 'This booking is no longer eligible.');

        if ($slot->status !== 'pending') {
            return view('dashboard.training.sessions.discord-confirm-result', [
                'success' => $slot->status === 'booked',
                'message' => $slot->status === 'booked'
                    ? 'This session was already confirmed.'
                    : 'This session can no longer be confirmed — it may have been cancelled.',
            ]);
        }

        $slot->status = 'booked';
        $slot->save();

        if ($slot->student && $slot->student->user) {
            $slot->student->user->notify(new TrainingSessionConfirmed($slot));
        }

        return view('dashboard.training.sessions.discord-confirm-result', [
            'success' => true,
            'message' => 'Session confirmed'
                . ($slot->student && $slot->student->user ? ' — ' . $slot->student->user->fullName('FL') . ' has been notified.' : '.'),
        ]);
        });
    }

    public function studentIndex()
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $openSlots = collect();
        if ($student->mentorable) {
            $openSlots = TrainingSession::open()
                ->with('provider')
                ->orderBy('start_time')
                ->get();
        } elseif ($student->instructor_id) {
            $openSlots = TrainingSession::open()
                ->with('provider')
                ->where('instructor_id', $student->instructor_id)
                ->orderBy('start_time')
                ->get();
        }

        $openSlots = $openSlots->filter(fn ($slot) => TrainingBookingEligibility::allows(
            $student, $slot->provider, $slot->instructor_id
        ))->values();

        $myBookings = TrainingSession::with('provider')
            ->where('student_id', $student->id)
            ->whereIn('status', ['booked', 'pending'])
            ->orderBy('start_time')
            ->get();

        $userTz = Auth::user()->displayTimezone();

        return view('dashboard.training.sessions.student', compact('student', 'openSlots', 'myBookings', 'userTz'));
    }

    /**
     * Students book in fixed 1-hour windows. The chosen window must fall
     * entirely within a still-open slot; that slot is resized down to the
     * booked hour, and whatever's left before/after is split off into new
     * open slots so the rest of the provider's availability stays bookable.
     */
    public function book(Request $request)
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $request->validate([
            'start_time' => 'required|date',
            'instructor_id' => 'nullable|exists:instructors,id',
            'provider_user_id' => 'nullable|exists:users,id',
        ]);

        // Mentorable students may book with eligible mentors or instructors; everyone else is
        // locked to their own assigned instructor regardless of what's posted.
        $instructorId = ($student->mentorable && $request->filled('instructor_id'))
            ? (int) $request->input('instructor_id')
            : $student->instructor_id;

        $providerUserId = $student->mentorable
            ? (int) $request->input('provider_user_id')
            : optional(Instructor::find($student->instructor_id))->user_id;

        if (!$providerUserId) {
            return redirect()->back()->withError('That training provider is not available.');
        }

        $userTz = Auth::user()->displayTimezone();
        $start = Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC')->second(0);
        $end = $start->copy()->addHour();

        if ($start->lt(now())) {
            return redirect()->back()->withError('That time is in the past.');
        }

        $booked = DB::transaction(function () use ($student, $start, $end, $instructorId, $providerUserId) {
            // Serialize requests for the same provider or student, including requests
            // targeting different availability rows. Stable ordering limits deadlocks.
            User::whereIn('id', [$student->user_id, $providerUserId])
                ->orderBy('id')->lockForUpdate()->get();
            $student->refresh();
            if (TrainingSession::whereIn('status', ['pending', 'booked'])
                ->where('start_time', '<', $end)->where('end_time', '>', $start)
                ->where(function ($query) use ($student, $providerUserId) {
                    $query->where('student_id', $student->id)
                        ->orWhere('provider_user_id', $providerUserId);
                })->exists()) {
                return false;
            }
            $slot = TrainingSession::where('provider_user_id', $providerUserId)
                ->where('status', 'open')
                ->where('start_time', '<=', $start)
                ->where('end_time', '>=', $end)
                ->when(!$student->mentorable, fn ($query) => $query->where('instructor_id', $instructorId))
                ->lockForUpdate()->first();

            if (!$slot || !TrainingBookingEligibility::allows($student, $slot->provider, $slot->instructor_id)) {
                return false;
            }

            if ($slot->start_time->lt($start)) {
                TrainingSession::create([
                    'instructor_id' => $slot->instructor_id,
                    'provider_user_id' => $slot->provider_user_id,
                    'start_time' => $slot->start_time,
                    'end_time' => $start,
                    'note' => $slot->note,
                    'status' => 'open',
                ]);
            }

            if ($slot->end_time->gt($end)) {
                TrainingSession::create([
                    'instructor_id' => $slot->instructor_id,
                    'provider_user_id' => $slot->provider_user_id,
                    'start_time' => $end,
                    'end_time' => $slot->end_time,
                    'note' => $slot->note,
                    'status' => 'open',
                ]);
            }

            $slot->start_time = $start;
            $slot->end_time = $end;
            $slot->student_id = $student->id;
            $slot->status = 'pending';
            $slot->booked_at = now();
            $slot->save();

            return $slot;
        });

        if (!$booked) {
            return redirect()->back()->withError('That time is unavailable or you are not eligible to book with that provider.');
        }

        if ($booked->provider) {
            $booked->provider->notify(new TrainingSessionBooked($booked));
        }

        return redirect()->back()->withSuccess('Session booked — waiting on your training provider to confirm.');
    }

    public function studentCancel($id)
    {
        return DB::transaction(function () use ($id) {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $slot = $this->lockSession($id);
        abort_if($slot->student_id !== $student->id, 403);
        abort_unless(in_array($slot->status, ['pending', 'booked']), 409);

        if ($slot->provider) {
            $slot->provider->notify(new TrainingSessionCancelled($slot, 'The student', 'instructor'));
        }

        $slot->student_id = null;
        $slot->status = 'open';
        $slot->booked_at = null;

        DB::transaction(fn () => $this->mergeAdjacentOpenSlots($slot));

        return redirect()->back()->withSuccess('Booking cancelled.');
        });
    }

    public function adminIndex()
    {
        $sessions = TrainingSession::with(['provider', 'instructor.user', 'student.user'])
            ->orderBy('start_time', 'desc')
            ->get();

        $instructors = Instructor::with('user')->get();
        $students = Student::with('user')->get();
        $userTz = Auth::user()->displayTimezone();

        return view('dashboard.training.sessions.all', compact('sessions', 'instructors', 'students', 'userTz'));
    }

    public function adminDestroy($id)
    {
        return DB::transaction(function () use ($id) {
        $slot = $this->lockSession($id);

        if (in_array($slot->status, ['booked', 'pending'])) {
            return redirect()->back()->withError('Cancel a booked or pending session before deleting it.');
        }

        $slot->delete();

        return redirect()->back()->withSuccess('Slot removed.');
        });
    }

    public function adminCancel($id)
    {
        return DB::transaction(function () use ($id) {
        $slot = $this->lockSession($id);
        $slot->status = 'cancelled';
        $slot->save();

        return redirect()->back()->withSuccess('Session cancelled.');
        });
    }

    public function adminUpdate(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'note'       => 'nullable|string|max:255',
        ]);

        $userTz = Auth::user()->displayTimezone();

        $slot = $this->lockSession($id);
        $slot->start_time = Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC');
        $slot->end_time = Carbon::parse($request->input('end_time'), $userTz)->setTimezone('UTC');
        $slot->note = $request->input('note');
        $this->assertNoOverlap($slot);
        $slot->save();

        return redirect()->back()->withSuccess('Session updated.');
        });
    }

    public function adminReassign(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'student_id'    => 'nullable|exists:students,id',
        ]);

        $targetProvider = Instructor::findOrFail($request->input('instructor_id'))->user_id;
        $targetStudent = $request->filled('student_id') ? Student::findOrFail($request->input('student_id')) : null;
        $slot = $this->lockSession($id, array_filter([$targetProvider, $targetStudent?->user_id]));
        $slot->instructor_id = $request->input('instructor_id');
        $slot->provider_user_id = Instructor::findOrFail($request->input('instructor_id'))->user_id;

        if ($request->filled('student_id')) {
            $student = Student::findOrFail($request->input('student_id'));
            abort_unless(TrainingBookingEligibility::allows(
                $student, User::find($slot->provider_user_id), $slot->instructor_id
            ), 403, 'This student is not eligible for that provider.');
            $slot->student_id = $request->input('student_id');
            $slot->status = 'booked';
            $slot->booked_at = $slot->booked_at ?: now();
            $this->assertNoOverlap($slot);
            $slot->save();
        } else {
            $slot->student_id = null;
            $slot->status = 'open';
            $slot->booked_at = null;
            $this->assertNoOverlap($slot);

            DB::transaction(fn () => $this->mergeAdjacentOpenSlots($slot));
        }

        return redirect()->back()->withSuccess('Session updated.');
        });
    }

    private function lockSession($id, array $extraUsers = []): TrainingSession
    {
        $snapshot = TrainingSession::findOrFail($id);
        $users = array_filter(array_merge($extraUsers, [
            $snapshot->provider_user_id, $snapshot->student?->user_id,
        ]));
        User::whereIn('id', $users)->orderBy('id')->lockForUpdate()->get();
        $slot = TrainingSession::whereKey($id)->lockForUpdate()->firstOrFail();
        abort_if($slot->provider_user_id != $snapshot->provider_user_id
            || $slot->student_id != $snapshot->student_id, 409, 'Session changed. Refresh and try again.');
        return $slot;
    }

    private function assertNoOverlap(TrainingSession $slot): void
    {
        if ($slot->status === 'cancelled') {
            return;
        }
        $conflict = TrainingSession::where('id', '!=', $slot->id)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $slot->end_time)->where('end_time', '>', $slot->start_time)
            ->where(function ($query) use ($slot) {
                $query->where('provider_user_id', $slot->provider_user_id);
                if ($slot->student_id && in_array($slot->status, ['pending', 'booked'])) {
                    $query->orWhere(function ($query) use ($slot) {
                        $query->where('student_id', $slot->student_id)->whereIn('status', ['pending', 'booked']);
                    });
                }
            })->exists();
        abort_if($conflict, 409, 'This change would overlap another session.');
    }
}
