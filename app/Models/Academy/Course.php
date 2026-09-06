<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'academy_courses';

    protected $fillable = ['title', 'slug', 'description', 'thumbnail', 'icon', 'sort_order', 'published', 'default_enrollment', 'created_by', 'updated_by'];

    protected $casts = ['published' => 'boolean', 'default_enrollment' => 'boolean'];

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('sort_order')->orderBy('id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function progressFor(int $userId): array
    {
        $moduleIds = $this->modules()->where('published', true)->pluck('id');
        $viewed = ModuleProgress::where('user_id', $userId)->whereIn('module_id', $moduleIds)->count();

        $submissions = QuizSubmission::with('quiz')
            ->where('user_id', $userId)
            ->whereHas('quiz.module', fn ($query) => $query->where('course_id', $this->id))
            ->get();

        // A course becomes complete as soon as the student has a graded passing attempt.
        // Failed/pending attempts keep the course In Progress, and a later retry does not erase
        // a pass that was already earned.
        $complete = $submissions->contains(fn ($submission) => $submission->passed());

        return [
            'status' => $complete ? 'complete' : (($viewed > 0 || $submissions->isNotEmpty()) ? 'in_progress' : 'not_started'),
            'viewed' => $viewed,
            'total' => $moduleIds->count(),
        ];
    }
}
