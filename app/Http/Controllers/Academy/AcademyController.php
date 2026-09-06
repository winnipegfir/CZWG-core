<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Models\Academy\Course;
use App\Models\Academy\Module;
use App\Services\AcademyAccess;
use App\Services\AcademyVatcanSyncService;
use Illuminate\Support\Facades\Auth;

class AcademyController extends Controller
{
    public function index()
    {
        // If this CID was seen on a previous VATCAN sync before its first website login,
        // attach those pending entitlements the first time the member opens the Academy.
        (new AcademyVatcanSyncService)->claimPendingForUser(Auth::user());

        $courses = Course::where('published', true)
            ->withCount(['modules' => fn ($query) => $query->where('published', true)])
            ->orderBy('sort_order')->orderBy('id')->get();

        $courses->each(function ($course) {
            $course->can_access = AcademyAccess::canViewCourse(Auth::user(), $course);
            $course->student_progress = $course->can_access ? $course->progressFor(Auth::id()) : null;
        });

        return view('academy.index', compact('courses'));
    }

    public function course(Course $course)
    {
        abort_unless(AcademyAccess::canViewCourse(Auth::user(), $course), 404);
        $course->load(['modules' => fn ($query) => $query->where('published', true)]);
        $viewedModuleIds = \App\Models\Academy\ModuleProgress::where('user_id', Auth::id())->whereIn('module_id', $course->modules->pluck('id'))->pluck('module_id');
        $courseProgress = $course->progressFor(Auth::id());

        return view('academy.course', compact('course', 'viewedModuleIds', 'courseProgress'));
    }

    public function module(Course $course, Module $module)
    {
        abort_unless(AcademyAccess::canViewCourse(Auth::user(), $course) && $module->published && $module->course_id === $course->id, 404);
        \App\Models\Academy\ModuleProgress::updateOrCreate(['user_id'=>Auth::id(), 'module_id'=>$module->id], ['viewed_at'=>now()]);

        $course->load(['modules' => fn ($query) => $query->where('published', true)->with('quiz')]);
        $module->load('quiz.questions.answers');
        $viewedModuleIds = \App\Models\Academy\ModuleProgress::where('user_id', Auth::id())
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->pluck('module_id');
        $courseProgress = $course->progressFor(Auth::id());
        $moduleIndex = $course->modules->search(fn ($item) => $item->id === $module->id);
        $previousModule = $moduleIndex !== false && $moduleIndex > 0 ? $course->modules[$moduleIndex - 1] : null;
        $nextModule = $moduleIndex !== false && $moduleIndex < ($course->modules->count() - 1) ? $course->modules[$moduleIndex + 1] : null;
        $latestSubmission = $module->quiz
            ? $module->quiz->submissions()->where('user_id', Auth::id())->latest('submitted_at')->first()
            : null;

        return view('academy.module', compact(
            'course', 'module', 'latestSubmission', 'viewedModuleIds', 'courseProgress', 'previousModule', 'nextModule'
        ));
    }
}
