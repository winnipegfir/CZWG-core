<?php

namespace App\Models\AtcTraining\CBT;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\CBT\CbtExamAssign;
use App\Models\AtcTraining\CBT\CbtExamResult;

class CbtExamQuestion extends Model
{
    //
    protected $fillable = [
        'exam_id', 'question', 'option1', 'option2', 'option3', 'option4', 'answer',
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
