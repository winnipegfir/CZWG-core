<?php

namespace App\Models\Academy;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $table = 'academy_quiz_submissions';

    protected $fillable = [
        'quiz_id', 'user_id', 'status', 'automatic_score', 'manual_score', 'final_score',
        'maximum_score', 'graded_by', 'submitted_at', 'graded_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'final_score' => 'integer',
    ];

    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function grader() { return $this->belongsTo(User::class, 'graded_by'); }
    public function responses() { return $this->hasMany(QuizResponse::class, 'submission_id'); }

    /** Raw question-by-question tally before an instructor uses final-mark discretion. */
    public function calculatedScore(): int
    {
        return (int) $this->automatic_score + (int) $this->manual_score;
    }

    /** Final recorded score. Falls back to the raw tally when no override was used. */
    public function finalScore(): int
    {
        return $this->final_score !== null ? (int) $this->final_score : $this->calculatedScore();
    }

    /** Backwards-compatible name used by older Academy views/progress code. */
    public function totalScore(): int
    {
        return $this->finalScore();
    }

    public function percentage(): float
    {
        if ((int) $this->maximum_score <= 0) {
            return 0;
        }

        return ($this->finalScore() / (int) $this->maximum_score) * 100;
    }

    public function passed(): bool
    {
        return $this->status === 'graded'
            && (int) $this->maximum_score > 0
            && $this->quiz
            && $this->percentage() >= (int) $this->quiz->passing_score;
    }
}
