<?php

namespace App\Http\Controllers\AtcTraining;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        return redirect()->back()->withSuccess('You have added a training note for '.$student->user->fullName('FLC').'');
    }
}
