<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\StudentNote;
use App\Models\AtcTraining\InstructingSession;
use App\Models\AtcTraining\CbtModuleAssign;

class Instructor extends Model
{
    protected $fillable = [
        'user_id', 'qualification', 'email',
    ];

    /*
     * * Return who posted the article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function studentnotes()
    {
      return $this->hasMany(StudentNote::class);
    }

    public function sessions()
    {
        return $this->hasMany(InstructingSession::class);
    }
    public function CbtModuleAssign()
    {
      return $this->hasMany(CbtModuleAssign::class);
    }
}
