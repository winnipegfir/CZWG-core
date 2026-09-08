<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Models\Academy\Quiz;
use App\Models\Academy\QuizSubmission;
use App\Notifications\AcademyQuizGradeRecorded;
use App\Notifications\AcademyQuizGraded;
use App\Services\AcademyAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademyQuizController extends Controller
{
    public function submit(Request $request, Quiz $quiz)
    {
        $quiz->load('module.course', 'questions.answers');
        abort_unless($quiz->published && AcademyAccess::canViewCourse(Auth::user(), $quiz->module->course), 404);

        $rules = [];
        foreach ($quiz->questions as $question) {
            $rules['responses.'.$question->id] = $question->type === 'written'
                ? ['required', 'string', 'max:10000']
                : ['required', 'integer', 'exists:academy_answers,id'];
        }

        $data = $request->validate($rules);

        $submission = DB::transaction(function () use ($quiz, $data) {
            $hasWritten = $quiz->questions->contains('type', 'written');
            $submission = $quiz->submissions()->create([
                'user_id' => Auth::id(),
                'status' => $hasWritten ? 'pending_review' : 'graded',
                'maximum_score' => $quiz->questions->sum('points'),
                'submitted_at' => now(),
                'graded_at' => $hasWritten ? null : now(),
            ]);

            $automatic = 0;
            foreach ($quiz->questions as $question) {
                $value = $data['responses'][$question->id];

                if ($question->type === 'written') {
                    $submission->responses()->create([
                        'question_id' => $question->id,
                        'written_response' => $value,
                    ]);
                    continue;
                }

                $answer = $question->answers->firstWhere('id', (int) $value);
                abort_unless($answer && $answer->question_id === $question->id, 422);

                $points = $answer->is_correct ? $question->points : 0;
                $automatic += $points;
                $submission->responses()->create([
                    'question_id' => $question->id,
                    'answer_id' => $answer->id,
                    'awarded_points' => $points,
                ]);
            }

            $submission->update(['automatic_score' => $automatic]);

            return $submission;
        });

        return redirect()->route('academy.submissions.show', $submission)
            ->with('success', 'Self assessment submitted.');
    }

    public function show(QuizSubmission $submission)
    {
        abort_unless($submission->user_id === Auth::id() || Auth::user()->isTrainingInstructor(), 403);
        $submission->load('quiz.module.course', 'responses.question.answers', 'responses.answer', 'grader');
        $courseProgress = $submission->quiz->module->course->progressFor($submission->user_id);

        return view('academy.submission', compact('submission', 'courseProgress'));
    }

    public function gradingIndex()
    {
        $submissions = QuizSubmission::with('user', 'quiz.module.course')
            ->orderByRaw("CASE WHEN status = 'pending_review' THEN 0 ELSE 1 END")
            ->latest('submitted_at')
            ->paginate(30);

        return view('academy.admin.submissions', compact('submissions'));
    }

    public function gradeForm(QuizSubmission $submission)
    {
        $submission->load('user', 'quiz.module.course', 'responses.question.answers', 'responses.answer', 'grader');

        return view('academy.admin.grade', compact('submission'));
    }

    public function grade(Request $request, QuizSubmission $submission)
    {
        $submission->load('user', 'quiz.module.course', 'responses.question');
        $written = $submission->responses->filter(fn ($r) => $r->question->type === 'written');

        $rules = [
            'final_score' => ['nullable', 'integer', 'between:0,'.max(0, (int) $submission->maximum_score)],
        ];

        foreach ($written as $response) {
            $rules['scores.'.$response->id] = ['required', 'integer', 'between:0,'.$response->question->points];
            $rules['feedback.'.$response->id] = ['nullable', 'string', 'max:5000'];
        }

        $data = $request->validate($rules);
        $grader = Auth::user();

        DB::transaction(function () use ($submission, $written, $data, $grader) {
            $manual = 0;

            foreach ($written as $response) {
                $score = (int) $data['scores'][$response->id];
                $manual += $score;
                $response->update([
                    'awarded_points' => $score,
                    'grader_feedback' => $data['feedback'][$response->id] ?? null,
                ]);
            }

            $submission->update([
                'manual_score' => $manual,
                // Blank means use the automatic + written-question tally. A value lets the
                // instructor exercise discretion over the recorded final mark.
                'final_score' => array_key_exists('final_score', $data) && $data['final_score'] !== null
                    ? (int) $data['final_score']
                    : null,
                'status' => 'graded',
                'graded_by' => $grader->id,
                'graded_at' => now(),
            ]);
        });

        $submission->refresh()->load('user', 'quiz.module.course', 'grader');

        // Use the site's existing database-notification centre for both sides of the grading action.
        if ($submission->user) {
            $submission->user->notify(new AcademyQuizGraded($submission));
        }
        $grader->notify(new AcademyQuizGradeRecorded($submission));

        $result = $submission->passed() ? 'passed' : 'not yet passed';

        return redirect()->route('academy.grading.index')->with(
            'success',
            'Grade saved: '.$submission->finalScore().'/'.$submission->maximum_score
                .' ('.round($submission->percentage()).'%) — '.$result.'. Student notified.'
        );
    }
}
