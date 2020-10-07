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

public function startExam($id)
{
  $subject = CbtExam::find($id);
        session()->forget('next_question_id');
        return view('dashboard.training.CBT.exams.startexam', compact('subject'));
}

public function exam($id)
{
  $subject = CbtExam::find($id);
  $questions = $subject->questions()->get();
        //dd($questions);
  $first_question_id = $subject->questions()->min('id');
        //dd($first_question_id);
  $last_question_id = $subject->questions()->max('id');
      //  dd($last_question_id);
    if(session('next_question_id')){
      $current_question_id = session('next_question_id');
      //dd(session('next_question_id'));
        }
    else{
      $current_question_id = $first_question_id;
        session(['next_question_id'=>$current_question_id]);
        }
        //dd($current_question_id);
  return view('dashboard.training.CBT.exams.exam', compact('subject', 'questions', 'current_question_id', 'first_question_id', 'last_question_id'));
  //return redirect()->back()->withError('This feature has not been implemented yet!');
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
