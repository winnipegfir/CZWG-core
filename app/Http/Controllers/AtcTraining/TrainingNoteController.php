<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Application;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\AtcTraining\InstructingSession;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use App\Models\Users\User;
use App\Models\Users\UserNotification;
use Auth;
use Calendar;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Mail;

class TrainingNoteController extends Controller
{

public function addNote(Request $request)
{
  $student = Student::where('id', $request->input('student'))->firstorFail();
  $instructor = Instructor::where('user_id', 'Auth::user()->id')->firstorFail();
  $newnote = StudentNote::create([
    'student_id' => $student->id,
    'instructor_id' => $instructor->id,
    'title' => $request->input('title'),
    'content' => $request->input('content'),
    'created_at' => Carbon::now()->toDateTimeString(),
  ]);
  dd($student);
  return redirect()->back()->withSuccess('You have added a training note for '.$student->user->fullName('FLC'). '');
}

}
