<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingWaittime;
use App\Models\Users\User;
use App\Services\DiscordTrainingWebhook;
use App\Services\VatcanService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $instructor = Instructor::where('user_id', $user->id)->first();
        $yourStudents = null;
        if ($instructor) {
            $yourStudents = Student::where('instructor_id', $instructor->id)->get();
        }

        $longestWaiting = Student::whereNull('instructor_id')
            ->whereNotNull('waitlist_added_at')
            ->orderBy('waitlist_added_at', 'asc')
            ->first();

        $recentActivity = Student::orderBy('updated_at', 'desc')->limit(5)->get();

        $waitlistBreakdown = [
            'home'     => Student::whereNull('instructor_id')->where('entry_type', 'New Student')->count(),
            'visiting' => Student::whereNull('instructor_id')->where('entry_type', 'New Visitor')->count(),
            'transfer' => Student::whereNull('instructor_id')->where('entry_type', 'New Transfer')->count(),
        ];

        return view('dashboard.training.indexinstructor', compact('yourStudents', 'longestWaiting', 'recentActivity', 'waitlistBreakdown'));
    }

    public function reconcile()
    {
        $firResult = (new VatcanService)->getRoster();

        if ($firResult['status'] === 'error') {
            return view('dashboard.training.reconcile', [
                'error'              => $firResult['message'],
                'onVatcanNotLinked'  => collect(),
                'linkedNotOnVatcan'  => collect(),
                'studentsNotOnRoster' => collect(),
                'rosterNotInSystem'  => collect(),
            ]);
        }

        $firData = $firResult['data'];
        $vatcanMembers = collect(array_merge(
            $firData['controllers'] ?? [],
            $firData['visitors'] ?? []
        ));
        $allVatcanCids = $vatcanMembers->pluck('cid')->flip();

        // Existing: instructor sync checks
        $linkedStudentCids = Student::whereNotNull('instructor_id')->pluck('user_id')->flip();
        $onVatcanNotLinked = $vatcanMembers
            ->filter(fn($m) => !empty($m['instructor']) && !$linkedStudentCids->has($m['cid']));
        $vatcanLinkedCids = $vatcanMembers->filter(fn($m) => !empty($m['instructor']))->pluck('cid')->flip();
        $linkedNotOnVatcan = Student::whereNotNull('instructor_id')
            ->get()
            ->filter(fn($s) => !$vatcanLinkedCids->has($s->user_id));

        // New: students in our system (waitlist or linked) not on the VATCAN roster at all
        $studentsNotOnRoster = Student::all()
            ->filter(fn($s) => !$allVatcanCids->has($s->user_id));

        // New: VATCAN roster members not in our training system at all
        $allStudentCids = Student::pluck('user_id')->flip();
        $rosterNotInSystem = $vatcanMembers
            ->filter(fn($m) => !$allStudentCids->has($m['cid']))
            ->sortBy(fn($m) => ($m['last_name'] ?? '') . ($m['first_name'] ?? ''))
            ->values();

        return view('dashboard.training.reconcile', compact(
            'onVatcanNotLinked', 'linkedNotOnVatcan', 'studentsNotOnRoster', 'rosterNotInSystem'
        ) + ['error' => null]);
    }

    public function newNoteView($id)
    {
        $student = Student::where('id', $id)->firstorFail();

        return view('dashboard.training.students.newnote', compact('student'));
    }

    public function addNote(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstOrFail();

        $success = (new VatcanService)->createNote(
            $student->user->id,
            $request->input('title'),
            $request->input('content'),
            Auth::user()->id
        );

        if ($success) {
            return redirect()->route('training.students.view', $student->id)
                ->withSuccess('Training note added for ' . $student->user->fullName('FLC') . '.');
        }

        return redirect()->route('training.students.view', $student->id)
            ->withError('Failed to save note via VATCAN API. Please try again.');
    }

    public function trainingTime()
    {
        $training_time = TrainingWaittime::where('id', 1)->first();
        $waitlist = Student::where('status', '0')->get();

        return view('training', compact('training_time', 'waitlist'));
    }

    public function editTrainingTime(Request $request)
    {
        request()->validate([
            'waitTime' => 'required',
        ]);

        $training_time = TrainingWaittime::firstOrNew(['id' => 1]);
        $training_time->wait_length = $request->waitTime;
        $training_time->colour = $request->trainingTimeColour;
        $training_time->save();

        return back()->withSuccess('Waittime updated successfully!');
    }

    public function instructorsIndex()
    {
        $instructors = Instructor::all();
        $potentialinstructor = RosterMember::where('status', 'instructor')->get();

        $vatcanFlags = [];
        $missingFromOurSystem = collect();
        $vatcanError = null;

        try {
            $rosterResult = (new VatcanService)->getRoster();

            if ($rosterResult['status'] === 'ok') {
                $allMembers = collect(array_merge(
                    $rosterResult['data']['controllers'] ?? [],
                    $rosterResult['data']['visitors'] ?? []
                ));

                $vatcanInstructorCids = $allMembers->where('is_instructor', true)->pluck('cid')->flip();

                foreach ($instructors as $instructor) {
                    if ($instructor->user) {
                        $vatcanFlags[$instructor->user->id] = $vatcanInstructorCids->has($instructor->user->id);
                    }
                }

                $ourInstructorCids = $instructors->pluck('user_id')->flip();
                $missingFromOurSystem = $allMembers->filter(
                    fn($m) => ($m['is_instructor'] ?? false) && !$ourInstructorCids->has($m['cid'])
                )->values();
            } else {
                $vatcanError = $rosterResult['message'];
            }
        } catch (\Exception $e) {
            \Log::error('VATCAN roster error: ' . $e->getMessage());
            $vatcanError = 'Could not load VATCAN data.';
        }

        return view('dashboard.training.instructors.index', compact('instructors', 'potentialinstructor', 'vatcanFlags', 'missingFromOurSystem', 'vatcanError'));
    }

    public function removeInstructor($id)
    {
        $instructor = Instructor::where('id', $id)->firstOrFail();

        if ($instructor->students()->count() > 0) {
            return redirect()->back()->withError('Cannot remove an instructor who has students assigned.');
        }

        $instructor->delete();

        return redirect()->back()->withSuccess('Instructor removed.');
    }

    public function addInstructor(Request $request)
    {
        Instructor::create([
            'user_id' => $request->input('cid'),
            'qualification' => $request->input('qualification'),
            'email' => $request->input('email'),
        ]);

        return redirect()->back()->withSuccess('Added '.$request->input('cid').' as an Instructor!');
    }

    public function newStudent(Request $request)
    {
        $userId = $request->input('add_method') === 'cid'
            ? (int) $request->input('cid_input')
            : (int) $request->input('student_id');

        $check = Student::where('user_id', $userId)->first();
        if ($check != null) {
            return redirect()->back()->withError('This student already exists in the system!');
        }

        $memberResult = (new VatcanService)->getFirMembershipType($userId);
        if ($memberResult['status'] === 'error') {
            return redirect()->back()->withError('Could not verify FIR membership via VATCAN: ' . $memberResult['message']);
        }
        $entryType = $request->input('entry_type');
        if ($memberResult['type'] === 'visitor' && $entryType !== 'New Visitor') {
            return redirect()->back()->withError('CID ' . $userId . ' is on the CZWG roster as a visitor. They must be added as "New Visitor".');
        }
        if ($memberResult['type'] === 'none' && $entryType !== 'New Visitor') {
            return redirect()->back()->withError('CID ' . $userId . ' is not on the CZWG home roster. If they are visiting, add them as "New Visitor".');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $student = Student::create([
            'user_id'            => $userId,
            'status'             => '0',
            'last_status_change' => Carbon::now()->toDateTimeString(),
            'entry_type'         => $request->input('entry_type'),
            'waitlist_added_at'  => Carbon::now(),
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $userId;

        (new DiscordTrainingWebhook)->waitlistAdded(
            $student->user ? $student->user->fullName('FL') : 'CID ' . $userId,
            $userId,
            $request->input('entry_type'),
            Auth::user()->fullName('FL')
        );

        return redirect()->route('training.students.waitlist')
            ->withSuccess('Added ' . $name . ' to the waitlist.');
    }

    public function currentStudents()
    {
        $students = Student::whereNotNull('instructor_id')->orderBy('created_at', 'asc')->get();
        $instructors = Instructor::all();
        $potentialstudent = User::where('id', '!=', 1)->orderBy('lname')->get();

        $vatcanOnlyCount = 0;
        try {
            $rosterResult = (new VatcanService)->getRoster();
            if ($rosterResult['status'] === 'ok') {
                $allMembers = collect(array_merge(
                    $rosterResult['data']['controllers'] ?? [],
                    $rosterResult['data']['visitors'] ?? []
                ));
                $linkedStudentCids = $students->pluck('user_id')->flip();
                $vatcanOnlyCount = $allMembers->filter(
                    fn($m) => !empty($m['instructor']) && !$linkedStudentCids->has($m['cid'])
                )->count();
            }
        } catch (\Exception $e) {
            // silently fail — banner just won't show
        }

        return view('dashboard.training.students.current', compact('students', 'instructors', 'potentialstudent', 'vatcanOnlyCount'));
    }

    public function newLinkedStudent(Request $request)
    {
        $userId = $request->input('add_method') === 'cid'
            ? (int) $request->input('cid_input')
            : (int) $request->input('student_id');

        $check = Student::where('user_id', $userId)->first();
        if ($check != null) {
            return redirect()->back()->withError('This student already exists in the system!');
        }

        $memberResult = (new VatcanService)->getFirMembershipType($userId);
        if ($memberResult['status'] === 'error') {
            return redirect()->back()->withError('Could not verify FIR membership via VATCAN: ' . $memberResult['message']);
        }
        $entryType = $request->input('entry_type');
        if ($memberResult['type'] === 'visitor' && $entryType !== 'New Visitor') {
            return redirect()->back()->withError('CID ' . $userId . ' is on the CZWG roster as a visitor. They must be added as "New Visitor".');
        }
        if ($memberResult['type'] === 'none' && $entryType !== 'New Visitor') {
            return redirect()->back()->withError('CID ' . $userId . ' is not on the CZWG home roster. If they are visiting, add them as "New Visitor".');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $student = Student::create([
            'user_id'            => $userId,
            'status'             => '1',
            'instructor_id'      => $request->input('instructor_id') ?: null,
            'last_status_change' => Carbon::now()->toDateTimeString(),
            'entry_type'         => $request->input('entry_type'),
            'waitlist_added_at'  => null,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if ($student->instructor_id && $student->instructor) {
            (new VatcanService)->assignInstructor($student->user_id, $student->instructor->user->id, Auth::id());
        }

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $userId;
        return redirect()->route('training.students.current')
            ->withSuccess('Added ' . $name . ' as a linked student.');
    }

    public function checkMembership(Request $request): \Illuminate\Http\JsonResponse
    {
        $cid = (int) $request->input('cid');
        if (!$cid) {
            return response()->json(['status' => 'error', 'message' => 'No CID provided.']);
        }
        return response()->json((new VatcanService)->getFirMembershipType($cid));
    }

    public function newStudents(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = Student::whereNull('instructor_id')
            ->orderByRaw('waitlist_added_at IS NULL ASC')
            ->orderBy('waitlist_added_at', 'asc');

        if ($filter === 'home') {
            $query->where('entry_type', 'New Student');
        } elseif ($filter === 'visiting') {
            $query->where('entry_type', 'New Visitor');
        } elseif ($filter === 'transfer') {
            $query->where('entry_type', 'New Transfer');
        }

        $students = $query->get();
        $potentialstudent = User::where('id', '!=', 1)->orderBy('lname')->get();
        $instructors = Instructor::all();

        return view('dashboard.training.students.waitlist', compact('students', 'potentialstudent', 'instructors', 'filter'));
    }

    public function activateWithInstructor(Request $request, $id)
    {
        abort_if(Auth::user()->permissions < 4, 403);
        $student = Student::where('id', $id)->firstOrFail();
        $instructorId = $request->input('instructor');
        $vatcan = new VatcanService;

        $student->instructor_id = ($instructorId !== 'unassign' && $instructorId) ? $instructorId : null;
        $student->status = '1';
        $student->last_status_change = Carbon::now()->toDateTimeString();
        $student->save();

        if ($student->instructor_id && $student->user) {
            $vatcan->assignInstructor($student->user_id, $student->instructor->user->id, Auth::id());

            (new DiscordTrainingWebhook)->studentLinked(
                $student->user->fullName('FL'),
                $student->user_id,
                $student->instructor->user->fullName('FL'),
                Auth::user()->fullName('FL')
            );
        }

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $student->user_id;
        return redirect()->route('training.students.waitlist')
            ->withSuccess('Started training for ' . $name . '.');
    }

    public function viewStudent($id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $instructors = Instructor::all();

        if ($student->user) {
            $vatcan = new VatcanService;
            $notesResult = $vatcan->getNotes($student->user->id);
            $notes = $notesResult['notes'];
            $notesError = $notesResult['status'] === 'error' ? $notesResult['message'] : null;

            $userResult = $vatcan->getUser($student->user->id);
            $vatcanUser = $userResult['status'] === 'ok' ? ($userResult['data']['data'] ?? null) : null;
            $vatcanUserError = $userResult['status'] === 'error' ? $userResult['message'] : null;
        } else {
            $notes = [];
            $notesError = 'No VATCAN data available until this member logs in for the first time.';
            $vatcanUser = null;
            $vatcanUserError = 'No VATCAN data available until this member logs in for the first time.';
        }

        return view('dashboard.training.students.viewstudent', compact('student', 'instructors', 'notes', 'notesError', 'vatcanUser', 'vatcanUserError'));
    }


    public function changeStudentStatus(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstorFail();
        if ($student != null) {
            $student->status = $request->input('status');
            if ($request->input('status') == '0' && $student->waitlist_added_at === null) {
                $student->waitlist_added_at = Carbon::now();
            }
            $student->save();
        }

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $student->user_id;
        return redirect()->back()->withSuccess('Successfully Changed The Status Of ' . $name . '.');
    }

    ///Nate Problem... worry about it
    public function updateWaitlistDate(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $student->waitlist_added_at = $request->input('waitlist_added_at')
            ? Carbon::parse($request->input('waitlist_added_at'))
            : null;
        $student->save();

        return redirect()->back()->withSuccess('Waitlist date updated.');
    }

    public function updateEntryType(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $student->entry_type = $request->input('entry_type');
        $student->save();

        return redirect()->back()->withSuccess('Entry type updated.');
    }

    public function bulkRemoveStudents(Request $request)
    {
        $ids = $request->input('student_ids', []);
        if (!empty($ids)) {
            Student::whereIn('id', $ids)->delete();
        }

        return redirect()->back()->withSuccess(count($ids) . ' student(s) removed from the training system.');
    }

    public function removeStudent($id)
    {
        $student = Student::where('id', $id)->firstOrFail();

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $student->user_id;
        $student->delete();

        return redirect()->route('training.students.waitlist')
            ->withSuccess('Removed ' . $name . ' from the training system.');
    }

    public function assignInstructorToStudent(Request $request, $id)
    {
        abort_if(Auth::user()->permissions < 4, 403);
        $student = Student::where('id', $id)->firstOrFail();
        $instructorInput = $request->input('instructor');
        $vatcan = new VatcanService;

        if ($instructorInput !== 'unassign') {
            if ($student->instructor_id) {
                $vatcan->unassignInstructor($student->user_id);
            }
            $student->instructor_id = $instructorInput;
            $student->save();
            $vatcan->assignInstructor($student->user_id, $student->instructor->user->id, Auth::id());

            (new DiscordTrainingWebhook)->studentLinked(
                $student->user->fullName('FL'),
                $student->user_id,
                $student->instructor->user->fullName('FL'),
                Auth::user()->fullName('FL')
            );

            return redirect()->back()->withSuccess('Paired ' . $student->user->fullName('FLC') . ' with Instructor ' . $student->instructor->user->fullName('FLC') . '.');
        }

        $name = $student->user ? $student->user->fullName('FLC') : 'CID ' . $student->user_id;
        $vatcan->unassignInstructor($student->user_id);

        (new DiscordTrainingWebhook)->studentUnlinked(
            $student->user ? $student->user->fullName('FL') : 'CID ' . $student->user_id,
            $student->user_id,
            Auth::user()->fullName('FL')
        );

        $student->delete();

        return redirect()->route('training.students.current')
            ->withSuccess('Unlinked and removed ' . $name . ' from the training system.');
    }

    public function assignExam(Request $request)
    {
        $student = Student::find($request->input('studentid'));
        if (! $student) {
            return redirect()->back()->withError('Student cannot be found!');
        }
        $check = CbtExamResult::where([
            'student_id' => $request->input('studentid'),
            'cbt_exam_id' => $request->input('examid'),
        ])->first();
        if ($check != null) {
            $removeanswers = CbtExamAnswer::where([
                'student_id' => $student->id,
                'cbt_exam_id' => $request->input('examid'),
            ])->get();
            foreach ($removeanswers as $r) {
                $r->delete();
            }
            $removeresult = CbtExamResult::where([
                'student_id' => $student->id,
                'cbt_exam_id' => $request->input('examid'),
            ])->first();
            $removeresult->delete();
        }
        $questioncount = CbtExamQuestion::where('cbt_exam_id', $request->input('examid'))->get();
        if (count($questioncount) < 10) {
            return redirect()->back()->withError('This exam does not have the minimum 10 questions, so it cannot be assigned!');
        }

        $assign = CbtExamAssign::create([
            'student_id' => $student->id,
            'instructor_id' => $student->instructor_id,
            'cbt_exam_id' => $request->input('examid'),
        ]);
        CbtNotification::create([
            'student_id' => $student->id,
            'message' => 'You have been assigned the '.$assign->cbtexam->name.'',
            'dismissed' => '0',
        ]);

        return redirect()->back()->withSuccess('Assigned exam to student!');
    }

    public function unassignExam($id)
    {
        $exam = CbtExamAssign::whereId($id)->first();
        $exam->delete();

        return redirect()->back()->withSuccess('Unassigned exam sucessfully!');
    }

    public function assignModule(Request $request)
    {
        $student = Student::whereId($request->input('studentid'))->first();
        $check = CbtModuleAssign::where([
            ['cbt_module_id', $request->input('moduleid')],
            ['student_id', $student->id],
        ])->first();
        if ($check != null) {
            return redirect()->back()->withError('Student Already has this Module Assigned!');
        }
        if ($student->instructor == null) {
            $instructor = null;
        }
        if ($student->instructor != null) {
            $instructor = $student->instructor->id;
        }
        $module = CbtModuleAssign::create([
            'cbt_module_id' => $request->input('moduleid'),
            'student_id' => $student->id,
            'instructor_id' => $instructor,
            'intro' => '1',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtNotification::create([
            'student_id' => $student->id,
            'message' => 'You have been assigned the '.$module->cbtmodule->name.' Module!',
            'dismissed' => '0',
        ]);

        return redirect()->back()->withSuccess('Module assigned to student!');
    }

    public function ModuleUnassign($id)
    {
        $module = CbtModuleAssign::whereId($id)->first();
        $module->delete();

        return redirect()->back()->withSuccess('Unassigned module sucessfully!');
    }


    public function viewApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->firstOrFail();

        return view('dashboard.training.applications.viewapplication', compact('application'));
    }

    public function acceptApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        if ($application != null) {
            $application->status = '2';
            $application->processed_by = Auth::id();
            $application->processed_at = Carbon::now()->toDateTimeString();
            $application->save();
            $newstudent = Student::create([
                'user_id' => $application->user_id,
                'status' => '0',
                'created_at' => Carbon::now()->toDateTimeString(),
                'accepted_application' => $application->id,
                'waitlist_added_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->withInput()->withSuccess('You have accepted the application for '.$application->user->fullName('FLC').', they have been added as an On-Hold Student!');
    }

    public function denyApplication($application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        if ($application != null) {
            $application->status = '1';
            $application->processed_by = Auth::id();
            $application->processed_at = Carbon::now()->toDateTimeString();
            $application->save();
        }

        return redirect()->back()->withInput()->withError('You have DENIED the application for '.$application->user->fullName('FLC').'');
    }

    public function editStaffComment(Request $request, $application_id)
    {
        $application = Application::where('application_id', $application_id)->first();
        if ($application != null) {
            $application->staff_comment = $request->input('staff_comments');
            $application->save();
        }

        return redirect()->back()->withInput()->withSuccess('You have edited the staff comments for '.$application->user->fullName('FLC').'');
    }

    public function assignStudent(Request $request)
    {
        $fullnameuser = User::findOrFail($request->input('student_id'));
        $fullnameinstructor = User::findorFail($request->input('instructor_id'));
        $assignstudent = InstructorStudents::create([
            'student_id' => $request->input('student_id'),
            'student_name' => $fullnameuser->fullName('FL'),
            'instructor_id' => $request->input('instructor_id'),
            'instructor_name' => $fullnameinstructor->fullName('FL'),
            'instructor_email' => User::where('id', $request->input('instructor_id'))->firstOrFail()->email,
            'assigned_by' => Auth::id(),
        ]);

        return redirect('/dashboard')->withSuccess('Successfully paired Student!');
    }

    public function deleteStudent($id)
    {
        $student = InstructorStudents::where('student_id', $id)->first();
        if ($student === null) {
            return redirect('/dashboard')->withError('CID '.$id.' does not exist as a student!');
        } else {
            $student->delete();
        }

        return redirect('/dashboard')->withSuccess('Student/Instructor Pairing Removed!');
    }
}
