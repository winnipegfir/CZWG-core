<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

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
