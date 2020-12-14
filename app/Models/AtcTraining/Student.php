<?php

namespace App\Models\AtcTraining;

use App\Models\AtcTraining\CBT\CbtModuleAssign;
use App\Models\AtcTraining\CBT\ExamAssign;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'status', 'instructor_id', 'last_status_change', 'accepted_application', 'entry_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function getApplicationAttribute()
    {
        return Application::whereId($this->accepted_application)->firstOrFail();
    }

    public function instructingSessions()
    {
        return $this->hasMany(InstructingSession::class);
    }

    public function trainingNotes()
    {
        return $this->hasMany(StudentNote::class);
    }

    public function exams()
    {
        return $this->HasMany(ExamAssign::class);
    }

    public function CbtModuleAssigns()
    {
        return $this->hasMany(CbtModuleAssign::class);
    }
}
