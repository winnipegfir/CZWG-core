<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class QuizResponse extends Model
{
    protected $table = 'academy_quiz_responses';
    protected $fillable = ['submission_id', 'question_id', 'answer_id', 'written_response', 'awarded_points', 'grader_feedback'];
    public function submission() { return $this->belongsTo(QuizSubmission::class); }
    public function question() { return $this->belongsTo(Question::class); }
    public function answer() { return $this->belongsTo(Answer::class); }
}
