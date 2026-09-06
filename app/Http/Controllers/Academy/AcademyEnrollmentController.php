<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Models\Academy\Course;
use App\Models\Academy\Enrollment;
use App\Models\Academy\VatcanMember;
use App\Models\Academy\VatcanSyncRun;
use App\Models\Users\User;
use App\Services\AcademyVatcanSyncService;
use App\Services\VatcanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademyEnrollmentController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('sort_order')->orderBy('title')->get();
        $students = User::where('permissions', '>=', 1)
            ->where(function ($query) {
                $query->whereHas('studentProfile')->orWhereHas('rosterProfile');
            })
            ->orderBy('lname')->orderBy('fname')->get();
        $enrollments = Enrollment::with('user', 'course', 'assigner')
            ->where('active', true)
            ->latest('assigned_at')->get();

        $vatcanMembers = VatcanMember::where('active_home_member', true)
            ->orderBy('rating_id')->orderBy('last_name')->orderBy('first_name')->get();
        $lastSync = VatcanSyncRun::latest('id')->first();
        $vatcan = new VatcanService;
        $vatcanConfigured = $vatcan->isConfigured();
        $vatcanAvailable = $vatcan->canSyncRoster();
        $vatcanSource = $vatcan->preferredRosterSourceLabel();

        return view('academy.admin.enrollments', compact(
            'courses', 'students', 'enrollments', 'vatcanMembers', 'lastSync',
            'vatcanConfigured', 'vatcanAvailable', 'vatcanSource'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'course_id' => ['required', 'integer', 'exists:academy_courses,id'],
        ]);
        $user = User::findOrFail($data['user_id']);
        abort_unless((int) $user->permissions >= 1, 422, 'This user is not an accepted FIR member.');

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $data['course_id'])->first();
        if ($enrollment) {
            $enrollment->update([
                'source' => 'manual',
                'active' => true,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'source_rating_id' => null,
                'source_synced_at' => null,
            ]);
        } else {
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $data['course_id'],
                'source' => 'manual',
                'active' => true,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
        }

        return back()->with('success', 'Course assigned to '.$user->fullName('FL').'.');
    }

    public function syncVatcan()
    {
        $run = (new AcademyVatcanSyncService)->sync(Auth::id());

        if ($run->status !== 'success') {
            return back()->with('error', $run->message ?: 'VATCAN Academy sync failed.');
        }

        return back()->with('success', sprintf(
            'VATCAN sync complete: %d home controllers, %d matched accounts, %d pending CIDs, %d enrollments activated.',
            $run->controllers_found,
            $run->users_matched,
            $run->pending_cids,
            $run->enrollments_activated
        ));
    }

    public function destroy(Enrollment $enrollment)
    {
        // Preserve history. A manual removal deactivates the record; a future VATCAN sync may
        // reactivate VATCAN-derived access when the rating still entitles the member.
        $enrollment->update(['active' => false]);
        return back()->with('success', 'Course enrollment removed.');
    }
}
