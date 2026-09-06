<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'academy_questions';
    protected $fillable = ['quiz_id', 'question', 'type', 'points', 'explanation', 'rubric', 'sort_order'];

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function answers() { return $this->hasMany(Answer::class)->orderBy('sort_order')->orderBy('id'); }
}
