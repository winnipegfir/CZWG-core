<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingSession;
use Auth;
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

        return view('dashboard.training.sessions.instructor', compact('slots'));
    }

    public function store(Request $request)
    {
        $instructor = Auth::user()->instructorProfile;
        abort_if(!$instructor, 403, 'You do not have an instructor profile.');

        $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'required|date|after:start_time',
            'type'       => 'nullable|string|max:255',
        ]);

        TrainingSession::create([
            'instructor_id' => $instructor->id,
            'start_time'    => $request->input('start_time'),
            'end_time'      => $request->input('end_time'),
            'type'          => $request->input('type'),
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

        $slot->status = 'cancelled';
        $slot->save();

        return redirect()->back()->withSuccess('Session cancelled.');
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
            ->where('status', 'booked')
            ->orderBy('start_time')
            ->get();

        return view('dashboard.training.sessions.student', compact('student', 'openSlots', 'myBookings'));
    }

    public function book($id)
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $booked = DB::transaction(function () use ($id, $student) {
            $slot = TrainingSession::where('id', $id)->lockForUpdate()->first();

            if (!$slot || $slot->status !== 'open' || $slot->instructor_id !== $student->instructor_id) {
                return false;
            }

            $slot->student_id = $student->id;
            $slot->status = 'booked';
            $slot->booked_at = now();
            $slot->save();

            return true;
        });

        if (!$booked) {
            return redirect()->back()->withError('That slot is no longer available.');
        }

        return redirect()->back()->withSuccess('Session booked.');
    }

    public function studentCancel($id)
    {
        $student = Auth::user()->studentProfile;
        abort_if(!$student, 403, 'You do not have a student profile.');

        $slot = TrainingSession::where('id', $id)->firstOrFail();
        abort_if($slot->student_id !== $student->id, 403);

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

        return view('dashboard.training.sessions.all', compact('sessions', 'instructors', 'students'));
    }

    public function adminDestroy($id)
    {
        $slot = TrainingSession::where('id', $id)->firstOrFail();

        if ($slot->status !== 'open') {
            return redirect()->back()->withError('Only open (unbooked) slots can be deleted. Cancel a booked slot instead.');
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
