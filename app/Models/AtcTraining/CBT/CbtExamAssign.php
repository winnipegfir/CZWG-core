<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtExamAssign extends Model
{
    //
    protected $fillable = [
        'student_id', 'instructor_id', 'cbt_exam_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->BelongsTo(Student::class);
    }

    public function CbtExam()
    {
        return $this->belongsTo(CbtExam::class);
    }
}
