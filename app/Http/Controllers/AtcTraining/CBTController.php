<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\CBT\CbtExam;
use App\Models\AtcTraining\CBT\CbtExamAnswer;
use App\Models\AtcTraining\CBT\CbtExamAssign;
use App\Models\AtcTraining\CBT\CbtExamQuestion;
use App\Models\AtcTraining\CBT\CbtExamResult;
use App\Models\AtcTraining\CBT\CbtModule;
use App\Models\AtcTraining\CBT\CbtModuleAssign;
use App\Models\AtcTraining\CBT\CbtModuleLesson;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
                    return redirect()->back()->withError('You do not have any assigned modules! Contact your Instructor at '.$student->instructor->email.'');
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
        $student = Student::where('user_id', Auth::user()->id)->first();
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_1'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('1'),
            'right_answer' => $req->input('a_1'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_2'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('2'),
            'right_answer' => $req->input('a_2'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_3'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('3'),
            'right_answer' => $req->input('a_3'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_4'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('4'),
            'right_answer' => $req->input('a_4'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_5'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('5'),
            'right_answer' => $req->input('a_5'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_6'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('6'),
            'right_answer' => $req->input('a_6'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_7'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('7'),
            'right_answer' => $req->input('a_7'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_8'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('8'),
            'right_answer' => $req->input('a_8'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_9'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('9'),
            'right_answer' => $req->input('a_9'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        CbtExamAnswer::create([
            'student_id' => $student->id,
            'cbt_exam_question_id' => $req->input('question_10'),
            'cbt_exam_id' => $id,
            'question' => '1',
            'user_answer' => $req->input('10'),
            'right_answer' => $req->input('a_10'),
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        $score = '0';
        $answers = CbtExamAnswer::where([
            ['student_id', $student->id],
            ['cbt_exam_id', $id],
        ])->get();
        foreach ($answers as $a) {
            if ($a->user_answer == $a->right_answer) {
                $score++;
            }
        }
        $grade = $score / 10 * 100;
        CbtExamResult::create([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
            'instructor_id' => $student->instructor->id,
            'grade' => $grade,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $removeexam = CbtExamAssign::where([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
        ])->first();
        $removeexam->delete();
        $exam = CbtExam::whereId($id)->first();
        $results = CbtExamAnswer::where([
            'student_id' => $student->id,
            'cbt_exam_id' => $id,
        ])->get();

        return view('dashboard.training.CBT.exams.results', compact('exam', 'results', 'grade', 'score'));
    }

    public function examResults($id, $sid)
    {
        $exam = CbtExam::whereId($id)->first();
        $results = CbtExamAnswer::where([
            'student_id' => $sid,
            'cbt_exam_id' => $id,
        ])->get();
        $grade = CbtExamResult::where([
            'student_id' => $sid,
            'cbt_exam_id' => $id,
        ])->first();

        return view('dashboard.training.CBT.exams.results', compact('exam', 'results', 'grade'));
    }


    public function questionBank($id)
    {
        $exam = CbtExam::whereId($id)->first();
        $questions = CbtExamQuestion::where('cbt_exam_id', $id)->get();

        return view('dashboard.training.CBT.exams.qbank', compact('exam', 'questions'));
    }

    public function addQuestion(Request $request, $id)
    {
        $question = CbtExamQuestion::updateOrCreate([
            'cbt_exam_id' => $id,
            'question' => $request->input('question'),
            'option1' => $request->input('option1'),
            'option2' => $request->input('option2'),
            'option3' => $request->input('option3'),
            'option4' => $request->input('option4'),
            'answer' => $request->input('answer'),
        ]);

        return redirect()->back()->withSuccess('Added the question!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = CbtExamQuestion::whereId($id)->first();
        if ($question != null) {
            $question->question = $request->input('question');
            $question->option1 = $request->input('option1');
            $question->option2 = $request->input('option2');
            $question->option3 = $request->input('option3');
            $question->option4 = $request->input('option4');
            $question->answer = $request->input('answer');
            $question->save();

            return redirect()->back()->withSuccess('Edited the question!');
        } else {
            return redirect()->back()->withError('A Server error has occured. Please contact Webmaster!');
        }
    }

    public function deleteQuestion($id)
    {
        $question = CbtExamQuestion::whereId($id)->first();
        $question->delete();

        return redirect()->back()->withSuccess('Question has been deleted!');
    }

    public function saveAnswer(Request $req, $id)
    {
        //save result
        $student = Student::where('user_id', Auth::user()->id)->first();
        $subject = CbtExam::find($id);
        $question = CbtExamQuestion::find($req->get('question_id'));
        if ($req->get('option') != null) {
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

        $next_question_id = $subject->questions()->where('id', '>', $req->get('question_id'))->min('id');
        if ($next_question_id != null) {
            return Response()->json(['next_question_id' => $next_question_id]);
        }

        return redirect()->route('gradeExam', [$id]);
    }

    public function addExam(Request $req)
    {
        CbtExam::create([
            'name' => $req->input('name'),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        return redirect()->back()->withSuccess('Added '.$req->input('name').' Exam!');
    }

    public function modifyExam(Request $request, $id)
    {
        //  User::updateOrCreate
        return redirect()->back()->withError('This feature has not been implemented yet!');
    }

    public function getQuestions($id)
    {
        $subject = Subject::findOrFail($id);

        $title = 'Manage questions';
        $answer = ['1'=>1, '2'=>2, '3'=> 3, '4'=> 4];
        $questions = $subject->questions;
        $title_button = 'Save question';
        //dd($questions);
        return view('subject.questions', compact('subject', 'title', 'answer', 'questions', 'title_button'));
    }

    public function viewQuestions($id)
    {
        $questions = CbtExamQuestion::where('cbt_exam_id', $id)->get();
        $exam = CbtExam::whereId($id)->FirstorFail();

        return view('dashboard.training.cbt.exams.viewexamadmin', compact('questions', 'exam'));
    }

    public function deleteExam($id)
    {
    }

    public function assignExam(Request $request, $id)
    {
        return redirect()->back()->withError('This feature has not been implemented yet!');
    }
}
