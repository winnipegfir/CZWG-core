<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'academy_answers';
    protected $fillable = ['question_id', 'answer', 'is_correct', 'sort_order'];
    protected $casts = ['is_correct' => 'boolean'];

    public function question() { return $this->belongsTo(Question::class); }
}
