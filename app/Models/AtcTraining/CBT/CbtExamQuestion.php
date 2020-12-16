<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtExamQuestion extends Model
{
    //
    protected $fillable = [
        'cbt_exam_id', 'question', 'option1', 'option2', 'option3', 'option4', 'answer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtExamn()
    {
        return $this->belongsTo(CbtExam::class);
    }
}
