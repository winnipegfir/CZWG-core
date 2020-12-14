<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\AtcTraining\Instructor;
use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtModuleAssign extends Model
{
    //
    protected $fillable = [
        'name', 'content_html', 'assigned_at', 'started_at', 'completed_at', 'cbt_module_id', 'student_id', 'instructor_id', 'intro', 'lesson1', 'lesson2', 'lesson3', 'lesson4', 'lesson5', 'lesson6', 'lesson7', 'lesson8', 'lesson9', 'lesson10', 'conclusion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtModule()
    {
        return $this->belongsTo(CbtModule::class);
    }

    public function Student()
    {
        return $this->belongsTo(Student::class);
    }

    public function Instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
