<?php

namespace App\Models\AtcTraining\CBT;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users\User;
use App\Models\AtcTraining\Student;
use App\Models\AtcTraining\CBT\CbtModule;
use App\Models\AtcTraining\CBT\CbtModuleAssign;
class CbtModuleLesson extends Model
{
    //
    protected $fillable = [
        'name', 'content_html', 'created_by', 'updated_by', 'updated_at', 'cbt_modules_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function CbtModule()
    {
      return $this->hasMany(CbtModule::class);
    }

}
