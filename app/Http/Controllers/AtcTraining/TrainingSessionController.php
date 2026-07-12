<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingSession;
use App\Notifications\TrainingSessionBooked;
use App\Notifications\TrainingSessionCancelled;
use App\Notifications\TrainingSessionConfirmed;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingSessionController extends Controller
{
    public function instructorIndex()
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $slots = TrainingSession::where('instructor_id', $instructor->id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        $userTz = Auth::user()->displayTimezone();

        return view('dashboard.training.sessions.instructor', compact('slots', 'userTz'));
    }

    public function store(Request $request)
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'note'       => 'nullable|string|max:255',
        ]);

        $userTz = Auth::user()->displayTimezone();

        TrainingSession::create([
            'instructor_id' => $instructor->id,
            'start_time'    => Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC'),
            'end_time'      => Carbon::parse($request->input('end_time'), $userTz)->setTimezone('UTC'),
            'note'          => $request->input('note'),
            'status'        => 'open',
        ]);

        return redirect()->back()->withSuccess('Slot added.');
    }

    public function destroy($id)
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        abort_if($slot->instructor_id !== $instructor->id, 403);

        if ($slot->status !== 'open') {
            return redirect()->back()->withError('Only open (unbooked) slots can be deleted. Cancel a booked slot instead.');
        }

        $slot->delete();

        return redirect()->back()->withSuccess('Slot removed.');
    }

    public function cancel($id)
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        abort_if($slot->instructor_id !== $instructor->id, 403);

        if ($slot->student && $slot->student->user) {
            $slot->student->user->notify(new TrainingSessionCancelled($slot, 'Your instructor', 'student'));
        }

        $slot->student_id = null;
        $slot->status = 'open';
        $slot->booked_at = null;
        $slot->save();

        return redirect()->back()->withSuccess('Session cancelled — the time is open again.');
    }

    public function confirm($id)
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        abort_if($slot->instructor_id !== $instructor->id, 403);

        if ($slot->status !== 'pending') {
            return redirect()->back()->withError('Only pending sessions can be confirmed.');
        }

        $slot->status = 'booked';
        $slot->save();

        if ($slot->student && $slot->student->user) {
            $slot->student->user->notify(new TrainingSessionConfirmed($slot));
        }

        return redirect()->back()->withSuccess('Session confirmed.');
    }

    public function studentIndex()
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $openSlots = collect();
        if ($student->instructor_id) {
            $openSlots = TrainingSession::open()
                ->where('instructor_id', $student->instructor_id)
                ->orderBy('start_time')
                ->get();
        }

        $myBookings = TrainingSession::where('student_id', $student->id)
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
     * open slots so the rest of the instructor's availability stays bookable.
     */
    public function book(Request $request)
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $request->validate([
            'start_time' => 'required|date',
        ]);

        $userTz = Auth::user()->displayTimezone();
        $start = Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC')->second(0);
        $end = $start->copy()->addHour();

        $booked = DB::transaction(function () use ($student, $start, $end) {
            $slot = TrainingSession::where('instructor_id', $student->instructor_id)
                ->where('status', 'open')
                ->where('start_time', '<=', $start)
                ->where('end_time', '>=', $end)
                ->lockForUpdate()
                ->first();

            if (!$slot) {
                return false;
            }

            if ($slot->start_time->lt($start)) {
                TrainingSession::create([
                    'instructor_id' => $slot->instructor_id,
                    'start_time' => $slot->start_time,
                    'end_time' => $start,
                    'note' => $slot->note,
                    'status' => 'open',
                ]);
            }

            if ($slot->end_time->gt($end)) {
                TrainingSession::create([
                    'instructor_id' => $slot->instructor_id,
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
            return redirect()->back()->withError('That time is no longer available.');
        }

        if ($booked->instructor && $booked->instructor->user) {
            $booked->instructor->user->notify(new TrainingSessionBooked($booked));
        }

        return redirect()->back()->withSuccess('Session booked — waiting on your instructor to confirm.');
    }

    public function studentCancel($id)
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        abort_if($slot->student_id !== $student->id, 403);

        if ($slot->instructor && $slot->instructor->user) {
            $slot->instructor->user->notify(new TrainingSessionCancelled($slot, 'The student', 'instructor'));
        }

        $slot->student_id = null;
        $slot->status = 'open';
        $slot->booked_at = null;
        $slot->save();

        return redirect()->back()->withSuccess('Booking cancelled.');
    }

    public function adminIndex()
    {
        $sessions = TrainingSession::with(['instructor.user', 'student.user'])
            ->orderBy('start_time', 'desc')
            ->get();

        $instructors = Instructor::with('user')->get();
        $students = Student::with('user')->get();
        $userTz = Auth::user()->displayTimezone();

        return view('dashboard.training.sessions.all', compact('sessions', 'instructors', 'students', 'userTz'));
    }

    public function adminDestroy($id)
    {
        $slot = TrainingSession::where('id', $id)->firstOrFail();

        if (in_array($slot->status, ['booked', 'pending'])) {
            return redirect()->back()->withError('Cancel a booked or pending session before deleting it.');
        }

        $slot->delete();

        return redirect()->back()->withSuccess('Slot removed.');
    }

    public function adminCancel($id)
    {
        $slot = TrainingSession::where('id', $id)->firstOrFail();
        $slot->status = 'cancelled';
        $slot->save();

        return redirect()->back()->withSuccess('Session cancelled.');
    }

    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'note'       => 'nullable|string|max:255',
        ]);

        $userTz = Auth::user()->displayTimezone();

        $slot = TrainingSession::findOrFail($id);
        $slot->start_time = Carbon::parse($request->input('start_time'), $userTz)->setTimezone('UTC');
        $slot->end_time = Carbon::parse($request->input('end_time'), $userTz)->setTimezone('UTC');
        $slot->note = $request->input('note');
        $slot->save();

        return redirect()->back()->withSuccess('Session updated.');
    }

    public function adminReassign(Request $request, $id)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'student_id'    => 'nullable|exists:students,id',
        ]);

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        $slot->instructor_id = $request->input('instructor_id');

        if ($request->filled('student_id')) {
            $slot->student_id = $request->input('student_id');
            $slot->status = 'booked';
            $slot->booked_at = $slot->booked_at ?: now();
        } else {
            $slot->student_id = null;
            $slot->status = 'open';
            $slot->booked_at = null;
        }

        $slot->save();

        return redirect()->back()->withSuccess('Session updated.');
    }
}
