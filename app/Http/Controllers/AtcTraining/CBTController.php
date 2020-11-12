<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Application;
use App\Models\Settings\AuditLogEntry;
use App\Models\Settings\CoreSettings;
use App\Models\AtcTraining\InstructingSession;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\CBT\CbtExamAssign;
use App\Models\AtcTraining\CBT\CbtExamResult;
use App\Models\AtcTraining\CBT\CbtExam;
use App\Models\AtcTraining\CBT\CbtExamQuestion;
use App\Models\AtcTraining\CBT\CbtExamAnswer;
use App\Models\AtcTraining\CBT\CbtModuleAssign;
use App\Models\AtcTraining\CBT\CbtModule;
use App\Models\AtcTraining\CBT\CbtModuleLesson;
use App\Models\AtcTraining\RosterMember;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\InstructorStudents;
use App\Models\Users\User;
use App\Models\Users\UserNotification;
use Auth;
use Calendar;
use Carbon\Carbon;
use Flash;
use Illuminate\Http\Request;
use Mail;

class CBTController extends Controller
{

public function index()
{
  $student = Student::all();
  return view('dashboard.training.CBT.index', compact('student'));
}

public function moduleindex()
{
  $student = Student::where('user_id', Auth::user()->id)->first();
      //Student Assigned Modules
  if ($student != null) {
  $modules = CbtModuleAssign::where('student_id', $student->id)->get();
  if (count($modules) < 1) {
    if ($student->instructor != null) {
    return redirect()->back()->withError('You do not have any assigned modules! Contact your Instructor at '.$student->instructor->email. '');
    }
      if ($student->instructor == null) {
    return redirect()->back()->withError('You do not have any assigned modules or an assigned Instructor! Please contact the Chief Instructor for help!');
      }
    }
  }

  return view('dashboard.training.CBT.modules', compact('modules'));
}

public function moduleindexadmin()
{
  $modules = CbtModule::all();
  return view('dashboard.training.CBT.modulesadmin', compact('modules'));
}

public function viewmodule($id, $progress)
//TO DO: ADD VIEW CURRENT LESSON
{
  if (Auth::user()->permissions >= 3) {
  $lessons = CbtModuleLesson::where('cbt_modules_id', $id)->get();
  $currentlesson = CbtModuleLesson::where([
    ['cbt_modules_id', $id],
    ['lesson', $progress],
  ])->first();
  return view('dashboard.training.CBT.viewmodule', compact('lessons', 'currentlesson'));
}
  $student = Student::where('user_id', Auth::user()->id)->first();
  $lessons = CbtModuleLesson::where('cbt_modules_id', $id)->get();
  $currentlesson = CbtModuleLesson::where([
    ['cbt_modules_id', $id],
    ['lesson', $progress],
  ])->first();

  $update = CbtModuleAssign::where([
    ['student_id', $student->id],
    ['cbt_module_id', $id],
  ])->first();

  if ($update->started_at == null) {
    $update->started_at = Carbon::now()->toDateTimeString();
    $update->save();
  }
  if ($progress != 'intro') {
      $update->{$progress} = 1;
      $update->save();
    }
  if ($progress == 'conclusion') {
    $update->completed_at = Carbon::now()->toDateTimeString();
    $update->save();
  }


  return view('dashboard.training.CBT.viewmodule', compact('lessons', 'currentlesson', 'update'));
}

public function viewAdminModule($id)
{
  if (Auth::user()->permissions >= 4) {
    $module = CbtModule::whereId($id)->first();
    $modules = CbtModule::all();
    $assignedstudents = CbtModuleAssign::where([
      ['cbt_module_id', $id],
      ['conclusion', '0'],
    ])->get();
    $completedstudents = CbtModuleAssign::where([
      ['cbt_module_id', $id],
      ['conclusion', '1'],
    ])->get();

    return view('dashboard.training.CBT.viewmoduleadmin', compact('module', 'modules', 'assignedstudents', 'completedstudents'));
}
}

public function modifyModule(Request $request, $id)
{
  return redirect()->back()->withError('This feature has not been implemented yet!');
}

public function assignModule(Request $request, $id)
{
  return redirect()->back()->withError('This feature has not been implemented yet!');
}

public function examindex()
{
  $student = Student::where('user_id', Auth::user()->id)->first();
  $exams = CbtExamAssign::where('student_id', $student->id)->get();
  $completedexams = CbtExamResult::where('student_id', $student->id)->get();

  return view('dashboard.training.CBT.exams.index', compact('exams', 'completedexams'));
}

public function examadminview()
{
  $exams = CbtExam::all();

  return view('dashboard.training.CBT.exams.examadmin', compact('exams'));
}
public function startExam($id)
{
  $subject = CbtExam::find($id);
        session()->forget('next_question_id');
        return view('dashboard.training.CBT.exams.startexam', compact('subject'));
}

public function exam($id)
{
  $subject = CbtExam::find($id);
  $questions = CbtExamQuestion::orderByRaw('RAND()')->take(10)->get();

  return view('dashboard.training.CBT.exams.exam', compact('subject', 'questions'));

}

public function gradeExam(Request $req, $id)
{
return redirect()->back()->withError('This feature has not been implemented yet!');
}



public function testgradeExam(Request $req, $id)
{
  $i = 1;
  $grade = 0;

$q1 = CbtExamQuestion::where('id', $req->input('1id'))->first();
if ($q1->answer == $req->input('1')) {
  $grade++;
}
$q2 = CbtExamQuestion::where('id', $req->input('2id'))->first();
if ($q2->answer == $req->input('2')) {
  $grade++;
}
$q3 = CbtExamQuestion::where('id', $req->input('3id'))->first();
if ($q3->answer == $req->input('3')) {
  $grade++;
}
$q4 = CbtExamQuestion::where('id', $req->input('4id'))->first();
if ($q4->answer == $req->input('4')) {
  $grade++;
}
$q5 = CbtExamQuestion::where('id', $req->input('5id'))->first();
if ($q5->answer == $req->input('5')) {
  $grade++;
}
$q6 = CbtExamQuestion::where('id', $req->input('6id'))->first();
if ($q6->answer == $req->input('6')) {
  $grade++;
}
$q7 = CbtExamQuestion::where('id', $req->input('7id'))->first();
if ($q7->answer == $req->input('7')) {
  $grade++;
}
$q8 = CbtExamQuestion::where('id', $req->input('8id'))->first();
if ($q8->answer == $req->input('8')) {
  $grade++;
}
$q9 = CbtExamQuestion::where('id', $req->input('9id'))->first();
if ($q9->answer == $req->input('9')) {
  $grade++;
}
$q10 = CbtExamQuestion::where('id', $req->input('10id'))->first();
if ($q10->answer == $req->input('10')) {
  $grade++;
}
$percentage = $grade/10*100;
echo "Percentage is $percentage%";
}

public function saveAnswer(Request $req, $id)
{
  //save result
      $student = Student::where('user_id', Auth::user()->id)->first();
      $subject = CbtExam::find($id);
      $question = CbtExamQuestion::find($req->get('question_id'));
      if($req->get('option') != null){
        //save the answer into table
        //dd($time_taken);

        CbtExamAnswer::create([
          'student_id'=>$student->id,
          'cbt_exam_question_id'=>$req->get('question_id'),
          'cbt_exam_id' => $id,
          'user_answer'=>$req->get('option'),
          'question' => $question->question,
          'option1' => $question->option1,
          'option2' => $question->option2,
          'option3' => $question->option3,
          'option4' => $question->option4,
          'right_answer'=>$question->answer,
      ]);
  }


      $next_question_id = $subject->questions()->where('id','>',$req->get('question_id'))->min('id');
        if($next_question_id != null) {
      return Response()->json(['next_question_id' => $next_question_id]);

  }

  return redirect()->route('gradeExam',[$id]);
}


public function modifyExam(Request $request, $id)
{
  //  User::updateOrCreate
  return redirect()->back()->withError('This feature has not been implemented yet!');
}

public function getQuestions($id){

    $subject = Subject::findOrFail($id);

    $title = "Manage questions";
    $answer = ['1'=>1, '2'=>2,'3'=> 3,'4'=> 4];
    $questions = $subject->questions;
    $title_button = "Save question";
        //dd($questions);
  return view('subject.questions', compact('subject', 'title', 'answer', 'questions', 'title_button'));
    }

public function addQuestion($id)
{

}

public function deleteQuestion($id)
{
  $subj_id = Question::find($id)->subject->id;
        Question::destroy($id);
        session()->flash('flash_mess', 'Question #'.$id.' was deleted');
  return redirect(action('SubjectController@getQuestions',$subj_id));
}

public function deleteExam($id)
{

}

public function assignExam(Request $request, $id)
{
  return redirect()->back()->withError('This feature has not been implemented yet!');
}


}
