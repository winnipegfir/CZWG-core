<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Models\Academy\Answer;
use App\Models\Academy\Course;
use App\Models\Academy\Module;
use App\Models\Academy\ModuleProgress;
use App\Models\Academy\Question;
use App\Models\Academy\Quiz;
use App\Models\Academy\QuizSubmission;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AcademyAdminController extends Controller
{
    public function hub()
    {
        return view('academy.admin.hub');
    }

    public function progress(Request $request)
    {
        $courses = Course::with(['modules' => function ($query) {
            $query->where('published', true);
        }])->where('published', true)->orderBy('sort_order')->orderBy('id')->get();

        $search = trim((string) $request->query('q', ''));

        $students = User::where('permissions', '>=', 1)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhere('fname', 'like', '%' . $search . '%')
                        ->orWhere('lname', 'like', '%' . $search . '%')
                        ->orWhereRaw("CONCAT(fname, ' ', lname) LIKE ?", ['%' . $search . '%']);
                });
            })
            ->orderByRaw("CASE rating_id
                WHEN 1 THEN 0
                WHEN 2 THEN 1
                WHEN 3 THEN 2
                WHEN 4 THEN 3
                WHEN 5 THEN 4
                WHEN 6 THEN 5
                WHEN 7 THEN 6
                WHEN 8 THEN 7
                WHEN 9 THEN 8
                WHEN 10 THEN 9
                WHEN 11 THEN 10
                WHEN 12 THEN 11
                ELSE 99
            END")
            ->orderBy('lname')
            ->orderBy('fname')
            ->get();

        $studentIds = $students->pluck('id');
        $moduleIds = $courses->flatMap(function ($course) {
            return $course->modules->pluck('id');
        })->values();

        $moduleProgress = ModuleProgress::whereIn('user_id', $studentIds)
            ->whereIn('module_id', $moduleIds)
            ->get()
            ->groupBy('user_id');

        $submissions = QuizSubmission::with('quiz.module')
            ->whereIn('user_id', $studentIds)
            ->whereHas('quiz.module', function ($query) use ($courses) {
                $query->whereIn('course_id', $courses->pluck('id'));
            })
            ->orderByDesc('submitted_at')
            ->get()
            ->groupBy('user_id');

        $progress = [];
        foreach ($students as $student) {
            $studentProgress = $moduleProgress->get($student->id, collect());
            $studentSubmissions = $submissions->get($student->id, collect());

            foreach ($courses as $course) {
                $courseModuleIds = $course->modules->pluck('id');
                $viewed = $studentProgress->whereIn('module_id', $courseModuleIds)->count();
                $total = $courseModuleIds->count();

                $courseSubmissions = $studentSubmissions->filter(function ($submission) use ($course) {
                    return $submission->quiz && $submission->quiz->module
                        && (int) $submission->quiz->module->course_id === (int) $course->id;
                });

                $passed = $courseSubmissions->contains(function ($submission) {
                    return $submission->passed();
                });

                $status = $passed ? 'complete' : (($viewed > 0 || $courseSubmissions->isNotEmpty()) ? 'in_progress' : 'not_started');

                $progress[$student->id][$course->id] = [
                    'status' => $status,
                    'viewed' => $viewed,
                    'total' => $total,
                ];
            }
        }

        return view('academy.admin.progress', compact('courses', 'students', 'progress', 'search'));
    }

    public function index()
    {
        $courses = Course::withCount('modules')->orderBy('sort_order')->orderBy('id')->get();
        return view('academy.admin.index', compact('courses'));
    }

    public function storeCourse(Request $request)
    {
        $data = $this->courseData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = Storage::url($request->file('thumbnail')->store('public/academy/course-thumbnails'));
        }
        Course::create($data);
        return back()->with('success', 'Course created.');
    }

    public function editCourse(Course $course)
    {
        $course->load('modules.quiz.questions.answers');
        return view('academy.admin.course', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        $data = $this->courseData($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $course->id);
        $data['updated_by'] = Auth::id();
        if ($request->boolean('remove_thumbnail') && $course->thumbnail) {
            Storage::delete(str_replace('/storage/', 'public/', $course->thumbnail));
            $data['thumbnail'] = null;
        }
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::delete(str_replace('/storage/', 'public/', $course->thumbnail));
            }
            $data['thumbnail'] = Storage::url($request->file('thumbnail')->store('public/academy/course-thumbnails'));
        }
        $course->update($data);
        return back()->with('success', 'Course updated.');
    }

    public function destroyCourse(Course $course)
    {
        if ($course->thumbnail) {
            Storage::delete(str_replace('/storage/', 'public/', $course->thumbnail));
        }
        $course->delete();
        return redirect()->route('academy.admin.index')->with('success', 'Course deleted.');
    }

    public function storeModule(Request $request, Course $course)
    {
        $data = $this->moduleData($request);
        $data['slug'] = $this->uniqueModuleSlug($course, $data['title']);
        $course->modules()->create($data);
        return back()->with('success', 'Module created.');
    }

    public function editModule(Module $module)
    {
        $module->load('course', 'quiz.questions.answers');
        return view('academy.admin.module', compact('module'));
    }

    public function updateModule(Request $request, Module $module)
    {
        $data = $this->moduleData($request);
        $data['slug'] = $this->uniqueModuleSlug($module->course, $data['title'], $module->id);
        $module->update($data);
        return back()->with('success', 'Module updated.');
    }

    public function destroyModule(Module $module)
    {
        $course = $module->course;
        $module->delete();
        return redirect()->route('academy.admin.courses.edit', $course)->with('success', 'Module deleted.');
    }

    public function saveQuiz(Request $request, Module $module)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'passing_score' => ['required', 'integer', 'between:1,100'],
        ]);
        $data['published'] = $request->boolean('published');
        $module->quiz()->updateOrCreate(['module_id' => $module->id], $data);
        return back()->with('success', 'Self assessment settings saved.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'type' => ['required', Rule::in(['multiple_choice', 'written'])],
            'points' => ['required', 'integer', 'between:1,20'],
            'explanation' => ['nullable', 'string'],
            'rubric' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'answers' => ['required_if:type,multiple_choice', 'array', 'min:2'],
            'answers.*' => ['nullable', 'string'],
            'correct_answer' => ['required_if:type,multiple_choice', 'integer'],
        ]);
        if ($data['type'] === 'multiple_choice') abort_unless(array_key_exists($data['correct_answer'], $data['answers']), 422);

        $question = $quiz->questions()->create([
            'question' => $data['question'],
            'type' => $data['type'], 'points' => $data['points'],
            'explanation' => $data['explanation'] ?? null,
            'rubric' => $data['rubric'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
        foreach ($data['type'] === 'multiple_choice' ? $data['answers'] : [] as $index => $answer) {
            $question->answers()->create(['answer' => $answer, 'is_correct' => (int) $index === (int) $data['correct_answer'], 'sort_order' => $index]);
        }
        return back()->with('success', 'Question added.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'points' => ['required', 'integer', 'between:1,20'],
            'explanation' => ['nullable', 'string'],
            'rubric' => ['nullable', 'string'],
            'answers' => [Rule::requiredIf($question->type === 'multiple_choice'), 'array'],
            'answers.*' => ['nullable', 'string'],
            'answer_ids' => [Rule::requiredIf($question->type === 'multiple_choice'), 'array'],
            'correct_answer' => [Rule::requiredIf($question->type === 'multiple_choice'), 'integer'],
        ]);
        if ($question->type === 'multiple_choice') abort_unless(array_key_exists($data['correct_answer'], $data['answers']), 422);

        $question->update(['question' => $data['question'], 'points' => $data['points'], 'explanation' => $data['explanation'] ?? null, 'rubric' => $data['rubric'] ?? null]);
        foreach ($data['answers'] ?? [] as $index => $answerText) {
            $answerId = $data['answer_ids'][$index] ?? null;
            $answer = $question->answers()->whereKey($answerId)->firstOrFail();
            $answer->update(['answer' => $answerText, 'is_correct' => (int) $index === (int) $data['correct_answer'], 'sort_order' => $index]);
        }
        return back()->with('success', 'Question updated.');
    }

    private function courseData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'remove_thumbnail' => ['nullable', 'boolean'],
        ]);
        $data['icon'] = $data['icon'] ?: 'fa-graduation-cap';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['published'] = $request->boolean('published');
        $data['default_enrollment'] = $request->boolean('default_enrollment');
        return $data;
    }

    private function moduleData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'google_slides_embed_code' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $embedCode = trim((string) ($data['google_slides_embed_code'] ?? ''));
        unset($data['google_slides_embed_code']);
        $data['google_slides_url'] = null;

        if ($embedCode !== '') {
            // Accept the iframe Google gives you under File > Share > Publish to web > Embed.
            // We store only its trusted Google Slides src URL rather than raw HTML.
            if (preg_match("~<iframe\\b[^>]*\\bsrc=['\"]([^'\"]+)['\"][^>]*>~i", $embedCode, $matches)) {
                $embedUrl = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
            } elseif (preg_match('~^https://docs\.google\.com/presentation/d/~i', $embedCode)) {
                // Also accept a bare Google Slides publish/embed URL for convenience.
                $embedUrl = html_entity_decode($embedCode, ENT_QUOTES | ENT_HTML5);
            } else {
                throw ValidationException::withMessages([
                    'google_slides_embed_code' => 'Paste the Google Slides Publish to web iframe embed code.',
                ]);
            }

            $parts = parse_url($embedUrl);
            $host = strtolower($parts['host'] ?? '');
            $path = $parts['path'] ?? '';

            if ($host !== 'docs.google.com' || ! preg_match('~^/presentation/d/(?:e/)?[a-zA-Z0-9_-]+/(?:pubembed|embed)$~', $path)) {
                throw ValidationException::withMessages([
                    'google_slides_embed_code' => 'That embed code is not a supported Google Slides Publish to web presentation.',
                ]);
            }

            $data['google_slides_url'] = $embedUrl;
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['published'] = $request->boolean('published');
        return $data;
    }

    private function uniqueSlug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'course'; $slug = $base; $i = 2;
        while (Course::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) $slug = $base.'-'.$i++;
        return $slug;
    }

    private function uniqueModuleSlug(Course $course, string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'module'; $slug = $base; $i = 2;
        while (Module::where('course_id', $course->id)->where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) $slug = $base.'-'.$i++;
        return $slug;
    }
}
