<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class StudentNote extends Model
{
    protected $fillable = [
        'student_id', 'author_id', 'title', 'content', 'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentnote()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function getInstructorAttribute()
    {
        return Instructor::whereId($this->author_id)->firstOrFail();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
