<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\AtcTraining\Student;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtNotification extends Model
{
    //
    protected $fillable = [
        'student_id', 'message', 'dismissed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Student()
    {
        return $this->hasMany(Student::class);
    }
}
