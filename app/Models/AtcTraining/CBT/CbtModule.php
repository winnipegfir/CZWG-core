<?php

namespace App\Models\AtcTraining\CBT;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class CbtModule extends Model
{
    //
    protected $fillable = [
        'name', 'description_html', 'user_id', 'updated_at', 'cbt_exam_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtModuleLesson()
    {
        return $this->belongsTo(CbtModuleLesson::class);
    }

    public function CbtModuleAssign()
    {
        return $this->hasMany(CbtModuleAssign::class);
    }

    public function CbtExam()
    {
        return $this->belongsTo(CbtExam::class);
    }
}
