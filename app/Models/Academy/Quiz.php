<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'academy_quizzes';
    protected $fillable = ['module_id', 'title', 'passing_score', 'published'];
    protected $casts = ['published' => 'boolean'];

    public function module() { return $this->belongsTo(Module::class); }
    public function questions() { return $this->hasMany(Question::class)->orderBy('sort_order')->orderBy('id'); }
    public function submissions() { return $this->hasMany(QuizSubmission::class); }
}
