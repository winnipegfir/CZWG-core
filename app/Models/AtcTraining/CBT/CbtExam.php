<?php

namespace App\Models\AtcTraining\CBT;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\CBT\CbtExamAssign;
use App\Models\AtcTraining\CBT\CbtExamResult;
use App\Models\AtcTraining\CBT\CbtExamQuestion;
class CbtExam extends Model
{
    //
    protected $fillable = [
        'name', 'created_by', 'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtExamAssign()
    {
      return $this->hasMany(CbtExamAssign::class);
    }

    public function CbtExamResult()
    {
      return $this->hasMany(CbtExamResult::class);
    }

    public function Questions()
    {
      return $this->hasMany(CbtExamQuestion::class);
    }

}
