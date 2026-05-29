<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\TrainingWaittime;
use App\Models\Users\User;
use App\Services\VatcanService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        return view('dashboard.training.indexinstructor', compact('yourStudents'));
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

        return view('dashboard.training.instructors.index', compact('instructors', 'potentialinstructor'));
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
        $check = Student::where('user_id', $request->input('student_id'))->first();
        if ($check != null) {
            return redirect()->back()->withError('This student already exists in the system!');
        }

        $instructor = null;
        if ($request->input('instructor') != 'unassign') {
            $instructor = $request->input('instructor');
        }

        $application = Application::create([
            'user_id' => $request->input('student_id'),
            'status' => '2',
            'submitted_at' => Carbon::now()->toDateTimeString(),
            'processed_at' => Carbon::now()->toDateTimeString(),
            'processed_by' => Auth::user()->id,
            'application_id' => Str::random(8),
        ]);
        $student = Student::create([
            'user_id' => $request->input('student_id'),
            'instructor_id' => $instructor,
            'status' => '0',
            'last_status_change' => Carbon::now()->toDateTimeString(),
            'created_at' => Carbon::now()->toDateTimeString(),
            'accepted_application' => $application->id,
            'entry_type' => $request->input('entry_type'),
            'waitlist_added_at' => Carbon::now(),
        ]);

        return redirect('dashboard/training/students/'.$student->id.'')->withSuccess('Added New Student: '.$student->user->fullName('FLC').'');
    }

    public function currentStudents()
    {
        $students = Student::whereNotNull('instructor_id')->orderBy('created_at', 'asc')->get();
        $instructors = Instructor::all();

        return view('dashboard.training.students.current', compact('students', 'instructors'));
    }

    public function newStudents()
    {
        $students = Student::whereNull('instructor_id')
            ->orderByRaw('waitlist_added_at IS NULL ASC')
            ->orderBy('waitlist_added_at', 'asc')
            ->get();
        $potentialstudent = User::all();
        $instructors = Instructor::all();

        return view('dashboard.training.students.waitlist', compact('students', 'potentialstudent', 'instructors'));
    }

    public function activateWithInstructor(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $instructorId = $request->input('instructor');

        $student->instructor_id = ($instructorId !== 'unassign' && $instructorId) ? $instructorId : null;
        $student->status = '1';
        $student->last_status_change = Carbon::now()->toDateTimeString();
        $student->save();


        return redirect()->route('training.students.waitlist')
            ->withSuccess('Started training for ' . $student->user->fullName('FLC') . '.');
    }

    public function viewStudent($id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $instructors = Instructor::all();
        $notes = (new VatcanService)->getNotes($student->user->id);

        return view('dashboard.training.students.viewstudent', compact('student', 'instructors', 'notes'));
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

        return redirect()->back()->withSuccess('Sucessfully Changed The Status Of '.$student->user->fullName('FLC').'');
    }

    ///Nate Problem... worry about it
    public function removeStudent($id)
    {
        $student = Student::where('id', $id)->firstOrFail();
        $name = $student->user->fullName('FLC');
        $student->delete();

        return redirect()->route('training.students.waitlist')
            ->withSuccess('Removed ' . $name . ' from the training system.');
    }

    public function assignInstructorToStudent(Request $request, $id)
    {
        $student = Student::where('id', $id)->firstorFail();
        $instructor = $request->input('instructor');
        if ($student != null) {
            if ($instructor != 'unassign') {
                $student->instructor_id = $request->input('instructor');
                $student->save();

                return redirect()->back()->withSuccess('Paired '.$student->user->fullName('FLC').'with Instructor '.$student->instructor->user->fullName('FLC').'');
            }
            if ($instructor == 'unassign') {
                $student->instructor_id = null;
                $student->save();

                return redirect()->back()->withSuccess('Unassigned '.$student->user->fullName('FLC').' from Instructor ');
            }
        }

        if ($student == null) {
            return redirect()->back()->withError('Unable to find a student with CID '.$student->user->id.'');
        }
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
