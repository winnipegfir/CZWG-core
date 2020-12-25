<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class InstructorStudents extends Model
{
    protected $table = 'instructor_students';

    protected $fillable = [
        'id',  'student_id', 'student_name', 'instructor_id', 'instructor_name', 'instructor_email', 'assigned_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
