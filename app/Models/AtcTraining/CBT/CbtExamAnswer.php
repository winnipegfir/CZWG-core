<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtExamAnswer extends Model
{
    //
    protected $fillable = [
        'student_id', 'cbt_exam_result_id', 'cbt_exam_question_id', 'cbt_exam_id', 'user_answer', 'question', 'option1', 'option2', 'option3', 'option4', 'right_answer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtExamn()
    {
        return $this->belongsTo(CbtExam::class);
    }

    public function CbtExamQuestion()
    {
        return $this->belongsTo(CbtExamQuestion::class);
    }
}
