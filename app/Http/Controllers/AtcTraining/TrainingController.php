<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Application;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\AtcTraining\InstructingSession;
use App\Models\AtcTraining\Instructor;
use App\Mail\ApplicationAcceptedStaffEmail;
use App\Mail\ApplicationAcceptedUserEmail;
use App\Mail\ApplicationDeniedUserEmail;
use App\Mail\ApplicationStartedStaffEmail;
use App\Mail\ApplicationStartedUserEmail;
use App\Mail\ApplicationWithdrawnEmail;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\TrainingWaittime;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\InstructorStudents;
use App\Models\AtcTraining\StudentNote;
use App\Models\Users\User;
use App\Models\Users\UserNotification;
use Auth;
use Calendar;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Str;

class TrainingController extends Controller
{

public function index()
{
  $user = Auth::user();
  $instructor = Instructor::where('user_id', $user->id)->first();
  $yourStudents = null;
  if($instructor) {
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

  $student = Student::where('id', $id)->first();
  $instructor = Instructor::where('user_id', Auth::user()->id)->first();
      if ($student != null && $instructor != null) {
  $newnote = StudentNote::create([
    'student_id' => $student->id,
    'author_id' => $instructor->id,
    'title' => $request->input('title'),
    'content' => $request->input('content'),
    'created_at' => Carbon::now()->toDateTimeString(),
  ]);

  return redirect('/dashboard/training/students/'.$student->id.'')->withSuccess('You have added a training note for '.$student->user->fullName('FLC'). '');
} else {
  return redirect('/dashboard/training/students/'.$student->id.'')->withError('You do not have sufficient permissions to do this!');
}

}

public function trainingTime() {
  $training_time = TrainingWaittime::where('id', 1)->first();

  return view('training', compact('training_time'));
}

public function editTrainingTime(Request $request) {
  request()->validate([
    'waitTime' => 'required',
  ]);

  $training_time = TrainingWaittime::where('id', 1)->first();
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
  return redirect()->back()->withSuccess('Added '.$request->input('cid'). ' as an Instructor!');
}

public function newStudent(Request $request)
{
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
    'instructor_id' => $request->input('instructor_id'),
    'status' => '1',
    'last_status_change' => Carbon::now()->toDateTimeString(),
    'created_at' => Carbon::now()->toDateTimeString(),
    'accepted_application' => $application->id,
  ]);
  return redirect('dashboard/training/students/'.$student->id.'')->withSuccess('Added New Student: '.$student->user->fullName('FLC'). '');
}
public function currentStudents()
{
  $students = Student::where('status', '1')->get();
  $potentialstudent = User::all();
  $instructors = Instructor::all();

  return view('dashboard.training.students.current', compact('students', 'potentialstudent', 'instructors'));
}

public function completedStudents()
{
  $students = Student::where('status', '2')->get();
  $potentialstudent = User::all();
  $instructors = Instructor::all();

  return view('dashboard.training.students.current', compact('students', 'potentialstudent', 'instructors'));
}

public function newStudents()
{
  $students = Student::where('status', '0')->get();
  $potentialstudent = User::all();
  $instructors = Instructor::all();

  return view('dashboard.training.students.current', compact('students', 'potentialstudent', 'instructors'));
}

public function viewStudent($id)
{
  $student = Student::where('id', $id)->firstorFail();
  $instructors = Instructor::all();

  return view('dashboard.training.students.viewstudent', compact('student', 'instructors'));
}

public function changeStudentStatus(Request $request, $id)
{
  $student = Student::where('id', $id)->firstorFail();
  if ($student != null) {
    $student->status = $request->input('status');
    $student->save();
  }
  return redirect()->back()->withSuccess('Sucessfully Changed The Status Of '.$student->user->fullName('FLC'). '');
}

///Nate Problem... worry about it
public function assignInstructorToStudent(Request $request, $id)
{
  $student = Student::where('id', $id)->firstorFail();
  $instructor = $request->input('instructor');
  if ($student != null) {
    if ($instructor != 'unassign') {
  $student->instructor_id = $request->input('instructor');
  $student->save();
    return redirect()->back()->withSuccess('Paired '.$student->user->fullName('FLC'). 'with Instructor ' .$student->instructor->user->fullName('FLC'). '');
  }
  if ($instructor == 'unassign') {
    $student->instructor_id = null;
    $student->save();
      return redirect()->back()->withSuccess('Unassigned '.$student->user->fullName('FLC'). ' from Instructor ');
  }
}

  if ($student == null) {
    return redirect()->back()->withError('Unable to find a student with CID '.$student->user->id. '');
  }
    }

public function viewNote($id)
{
  $note = StudentNote::where('id', $id)->firstorFail();
  return view('dashboard.training.students.viewnote', compact('note'));
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
  }
  else
  $student->delete();

  return redirect('/dashboard')->withSuccess('Student/Instructor Pairing Removed!');
}
}
