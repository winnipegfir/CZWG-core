<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class SoloRequest extends Model
{
    protected $fillable = [
        'student_id', 'approved', 'instructor_id', 'approved_at', 'position',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function Student()
    {
        return $this->belongsTo(Student::class);
    }
}
